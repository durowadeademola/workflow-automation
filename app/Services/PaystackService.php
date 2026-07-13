<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackService
{
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = (string) config('services.paystack.secret_key');
    }

    public function initializeTransaction(array $payload): array
    {
        return Http::withToken($this->secretKey)
            ->baseUrl('https://api.paystack.co')
            ->post('/transaction/initialize', $payload)
            ->throw()
            ->json();
    }

    public function verifyTransaction(string $reference): array
    {
        return Http::withToken($this->secretKey)
            ->baseUrl('https://api.paystack.co')
            ->get('/transaction/verify/'.rawurlencode($reference))
            ->throw()
            ->json();
    }

    public function verifyWebhookSignature(string $rawPayload, ?string $signature): bool
    {
        if (! $signature || ! $this->secretKey) {
            return false;
        }

        $expected = hash_hmac('sha512', $rawPayload, $this->secretKey);

        return hash_equals($expected, $signature);
    }
}
