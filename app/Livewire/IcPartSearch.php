<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\SourcingRequest;
use App\Jobs\ProcessSourcingRequest;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\RateLimiter;

class IcPartSearch extends Component
{
    public string $query = '';
    public string $mode = 'search';
    public bool $isLoading = false;
    public array $results = [];
    public array $aiSuggestions = [];
    public string $lastSearchedQuery = '';

    #[Rule('required|min:2|max:200')]
    public string $customerName = '';

    #[Rule('required|email|max:254')]
    public string $customerEmail = '';

    #[Rule('nullable|max:30')]
    public string $customerPhone = '';

    #[Rule('nullable|max:200')]
    public string $companyName = '';

    #[Rule('required|integer|min:1|max:999999')]
    public int $quantityRequired = 1;

    #[Rule('nullable|max:50')]
    public string $targetPrice = '';

    #[Rule('nullable|max:500')]
    public string $customerNotes = '';

    public bool $requestSubmitted = false;
    public string $submittedRefNumber = '';

    public function updatedQuery(): void
    {
        $this->mode = 'search';
        $this->requestSubmitted = false;

        if (strlen(trim($this->query)) < 2) {
            $this->results = [];
            $this->aiSuggestions = [];
            return;
        }

        $this->performSearch();
    }

    private function performSearch(): void
    {
        $sanitized = trim($this->query);

        $this->results = Product::query()
            ->where(function ($q) use ($sanitized) {
                $q->where('part_number', 'LIKE', "%{$sanitized}%")
                  ->orWhere('description', 'LIKE', "%{$sanitized}%")
                  ->orWhere('manufacturer', 'LIKE', "%{$sanitized}%");
            })
            ->select([
                'id', 'part_number', 'manufacturer', 'description',
                'category', 'package_type', 'stock_status',
                'quantity_available', 'unit_price_usd', 'lead_time',
                'supply_voltage_min', 'supply_voltage_max',
                'operating_temp_min', 'operating_temp_max',
                'rohs_compliant', 'datasheet_url', 'image_url',
            ])
            ->orderByRaw("
                CASE stock_status
                    WHEN 'in_stock'   THEN 1
                    WHEN 'low_stock'  THEN 2
                    ELSE 3
                END
            ")
            ->limit(12)
            ->get()
            ->toArray();

        $this->lastSearchedQuery = $sanitized;

        if (empty($this->results)) {
            $this->loadAiSuggestions($sanitized);
            $this->mode = 'sourcing';
        }
    }

    private function loadAiSuggestions(string $partNumber): void
    {
        try {
            $aiService = app(\App\Services\AiPartSuggestionService::class);
            $this->aiSuggestions = $aiService->getSuggestions($partNumber);
        } catch (\Throwable $e) {
            $this->aiSuggestions = [];
        }
    }

    public function searchSuggestion(string $partNumber): void
    {
        $this->query = $partNumber;
        $this->performSearch();
    }

    public function submitSourcingRequest(): void
    {
        $key = 'sourcing-request:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('rate_limit', 'Too many requests. Please try again later.');
            return;
        }
        RateLimiter::hit($key, 3600);

        $this->validate();

        $duplicate = SourcingRequest::where('customer_email', $this->customerEmail)
            ->where('part_number', strtoupper(trim($this->lastSearchedQuery)))
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($duplicate) {
            $this->addError('duplicate', 'You already submitted a request for this part. Check your inbox.');
            return;
        }

        $refNumber = 'SR-' . now()->format('Y') . '-' . str_pad(
            SourcingRequest::whereYear('created_at', now()->year)->count() + 1,
            5, '0', STR_PAD_LEFT
        );

        $sourcing = SourcingRequest::create([
            'reference_number'       => $refNumber,
            'part_number'            => strtoupper(trim($this->lastSearchedQuery)),
            'quantity_required'      => $this->quantityRequired,
            'target_price_usd'       => $this->targetPrice ?: null,
            'customer_name'          => $this->customerName,
            'customer_email'         => $this->customerEmail,
            'customer_phone'         => $this->customerPhone ?: null,
            'company_name'           => $this->companyName ?: null,
            'customer_notes'         => $this->customerNotes ?: null,
            'suggested_alternatives' => $this->aiSuggestions,
            'status'                 => 'pending',
            'ip_address'             => request()->ip(),
            'user_agent'             => substr(request()->userAgent() ?? '', 0, 500),
            'session_id'             => session()->getId(),
        ]);

        ProcessSourcingRequest::dispatch($sourcing)->onQueue('sourcing');

        $this->submittedRefNumber = $refNumber;
        $this->requestSubmitted   = true;
        $this->reset(['customerName', 'customerEmail', 'customerPhone', 'companyName',
                      'quantityRequired', 'targetPrice', 'customerNotes']);
    }

    public function backToSearch(): void
    {
        $this->mode = 'search';
        $this->requestSubmitted = false;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.ic-part-search');
    }
}
