<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentCharge;
use App\DTOs\PaymentResponse;
use App\Enums\PaymentProvider;
use Illuminate\Support\Str;

class StripePaymentGateway implements PaymentGatewayInterface
{
    /**
     * Process a charge using the Stripe payment gateway.
     */
    public function charge(PaymentCharge $charge): PaymentResponse
    {
        // stripe specific implementation would go here
        // e.g.:
        // $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        // $intent = $stripe->paymentIntents->create([...]);

        $mockTransactionId = 'ch_stripe_'.Str::lower(Str::random(24));

        return PaymentResponse::success(
            transactionId: $mockTransactionId,
            provider: $this->provider(),
            amountInCents: $charge->amountInCents,
            currency: $charge->currency,
            message: "Successfully charged \${$this->formatAmount($charge->amountInCents)} via Stripe Card Payment.",
            rawReference: [
                'gateway' => 'stripe',
                'status' => 'succeeded',
                'payment_method_type' => 'card',
                'receipt_url' => "https://pay.stripe.test/receipts/{$mockTransactionId}",
                'invoice_id' => $charge->invoiceId,
            ],
        );
    }

    /**
     * Return the associated payment provider enum.
     */
    public function provider(): PaymentProvider
    {
        return PaymentProvider::Stripe;
    }

    /**
     * Helper to format amount in cents to human readable dollar string.
     */
    private function formatAmount(int $amountInCents): string
    {
        return number_format($amountInCents / 100, 2);
    }
}
