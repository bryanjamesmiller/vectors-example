<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\DTOs\PaymentCharge;
use App\DTOs\PaymentResponse;
use App\Enums\PaymentProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentProcessor
{
    /**
     * Create a new payment processor instance.
     */
    public function __construct(
        protected PaymentGatewayFactory $gatewayFactory
    ) {}

    /**
     * Process a payment charge through the specified gateway inside a database transaction.
     *
     * In this demonstration, processing is wrapped in a database transaction to ensure
     * that any ledger or audit log mutations commit atomically. In a production billing
     * system with live third-party SDKs, external charges use an idempotent intent key
     * followed by a transactional ledger settlement commit.
     */
    public function process(PaymentProvider $provider, PaymentCharge $charge): PaymentResponse
    {
        return DB::transaction(function () use ($provider, $charge): PaymentResponse {
            // Resolve the swappable payment gateway strategy via the factory
            $gateway = $this->gatewayFactory->make($provider);

            // Execute the charge through the gateway contract
            $response = $gateway->charge($charge);

            if ($response->successful) {
                Log::info('Payment successfully processed', [
                    'provider' => $provider->value,
                    'transaction_id' => $response->transactionId,
                    'amount_in_cents' => $charge->amountInCents,
                    'invoice_id' => $charge->invoiceId,
                ]);
            } else {
                Log::warning('Payment charge failed or was declined', [
                    'provider' => $provider->value,
                    'error' => $response->message,
                    'invoice_id' => $charge->invoiceId,
                ]);
            }

            return $response;
        });
    }
}
