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
        return Http::withToken($this->secretKey)->baseUrl('https://api.paystack.co')->post('/transaction/initialize', $payload)->throw()->json();
    }

    public function verifyTransaction(string $reference): array
    {
        return Http::withToken($this->secretKey)->baseUrl('https://api.paystack.co')->get('/transaction/verify/'.rawurlencode($reference))->throw()->json();
    }

    /**
     * $amountInKobo omitted refunds the transaction's full amount; passed,
     * it refunds only that much (a proration, e.g. for unused subscription
     * time). Paystack processes refunds asynchronously — a successful call
     * here means the refund was accepted, not necessarily settled yet.
     */
    public function refundTransaction(string $transactionReference, ?int $amountInKobo = null): array
    {
        $payload = ['transaction' => $transactionReference];

        if ($amountInKobo !== null) {
            $payload['amount'] = $amountInKobo;
        }

        return Http::withToken($this->secretKey)->baseUrl('https://api.paystack.co')->post('/refund', $payload)->throw()->json();
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
