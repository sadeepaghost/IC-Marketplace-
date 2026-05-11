<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPartSuggestionService
{
    private string $apiKey;
    private string $model = 'claude-sonnet-4-20250514';
    private string $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key');
    }

    /**
     * Return an array of suggested alternative IC part numbers.
     * Results are cached for 24 hours to avoid redundant API calls.
     *
     * @param  string $partNumber  The searched part (e.g. "LM741")
     * @return array<int, array{part_number: string, manufacturer: string, description: string, reason: string}>
     */
    public function getSuggestions(string $partNumber): array
    {
        $cacheKey = 'ai_suggestions:' . strtoupper(trim($partNumber));

        return Cache::remember($cacheKey, now()->addDay(), function () use ($partNumber) {
            return $this->callApi($partNumber);
        });
    }

    private function callApi(string $partNumber): array
    {
        $prompt = <<<PROMPT
You are an expert electronic components engineer with deep knowledge of IC datasheets and cross-references.

A customer searched for the part number: "{$partNumber}"

This part was NOT found in our inventory. Your task:
1. Identify what type of IC this is (Op-Amp, MCU, voltage regulator, etc.).
2. Suggest up to 4 functionally equivalent or pin-compatible alternatives.
3. For each alternative, explain why it's a good substitute.

Respond ONLY with a valid JSON array. No markdown, no backticks, no preamble.
Format:
[
  {
    "part_number": "LM358",
    "manufacturer": "Texas Instruments",
    "description": "Dual Op-Amp, single supply, 1MHz bandwidth",
    "reason": "Pin-compatible dual op-amp, often used in place of single LM741 for lower power designs"
  }
]

If you cannot identify the part or find alternatives, return an empty array: []
PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])
            ->timeout(15)
            ->post($this->baseUrl, [
                'model'      => $this->model,
                'max_tokens' => 800,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Anthropic API error', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            }

            $content = $response->json('content.0.text', '[]');

            // Strip any accidental markdown fences
            $clean = preg_replace('/^```json\s*|\s*```$/m', '', trim($content));
            $suggestions = json_decode($clean, true);

            if (! is_array($suggestions)) {
                Log::warning('AI suggestion response was not a JSON array', ['raw' => $content]);
                return [];
            }

            // Sanitize each suggestion entry
            return array_map(fn ($s) => [
                'part_number'  => strip_tags($s['part_number']  ?? ''),
                'manufacturer' => strip_tags($s['manufacturer'] ?? ''),
                'description'  => strip_tags($s['description']  ?? ''),
                'reason'       => strip_tags($s['reason']       ?? ''),
            ], array_slice($suggestions, 0, 4));

        } catch (\Throwable $e) {
            Log::error('AI suggestion service exception', ['message' => $e->getMessage()]);
            return [];
        }
    }
}
