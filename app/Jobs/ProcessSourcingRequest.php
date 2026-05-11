<?php

namespace App\Jobs;

use App\Mail\SourcingRequestAlert;
use App\Models\SourcingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcessSourcingRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying.
     */
    public array $backoff = [60, 300, 900]; // 1min, 5min, 15min

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    public function __construct(
        private readonly SourcingRequest $sourcingRequest
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Mark as processing
        $this->sourcingRequest->update(['status' => 'ai_processing']);

        // Step 1: Send admin alert email
        $this->sendAdminAlert();

        // Step 2: Send customer acknowledgement
        $this->sendCustomerAck();

        // Step 3: Update status
        $this->sourcingRequest->update([
            'status'               => 'supplier_queried',
            'admin_notified'       => true,
            'admin_notified_at'    => now(),
        ]);

        Log::info('Sourcing request processed', [
            'reference' => $this->sourcingRequest->reference_number,
            'part'      => $this->sourcingRequest->part_number,
        ]);
    }

    private function sendAdminAlert(): void
    {
        $adminEmail = config('services.ic_marketplace.admin_email');

        Mail::to($adminEmail)
            ->send(new SourcingRequestAlert($this->sourcingRequest));
    }

    private function sendCustomerAck(): void
    {
        Mail::to($this->sourcingRequest->customer_email)
            ->send(new \App\Mail\SourcingRequestConfirmation($this->sourcingRequest));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessSourcingRequest job failed', [
            'reference' => $this->sourcingRequest->reference_number,
            'error'     => $exception->getMessage(),
        ]);

        // Revert status so it can be manually retried
        $this->sourcingRequest->update(['status' => 'pending']);
    }
}
