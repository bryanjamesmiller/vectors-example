<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentCharge;
use App\DTOs\PaymentResponse;
use App\Enums\PaymentProvider;
use Illuminate\Support\Str;

class PaypalPaymentGateway implements PaymentGatewayInterface
{
    /**
     * Process a charge using the PayPal payment gateway.
     */
    public function charge(PaymentCharge $charge): PaymentResponse
    {
        // paypal specific implementation would go there
        // e.g.:
        // $paypalClient = new \PayPalCheckoutSdk\Core\PayPalHttpClient($environment);
        // $order = $paypalClient->execute(new OrdersCreateRequest());

        $mockTransactionId = 'PAYID-'.Str::upper(Str::random(18));

        return PaymentResponse::success(
            transactionId: $mockTransactionId,
            provider: $this->provider(),
            amountInCents: $charge->amountInCents,
            currency: $charge->currency,
            message: "Successfully charged \${$this->formatAmount($charge->amountInCents)} via PayPal Wallet / Pay in 4.",
            rawReference: [
                'gateway' => 'paypal',
                'status' => 'COMPLETED',
                'payer_status' => 'VERIFIED',
                'paypal_order_id' => $mockTransactionId,
                'invoice_id' => $charge->invoiceId,
            ],
        );
    }

    /**
     * Return the associated payment provider enum.
     */
    public function provider(): PaymentProvider
    {
        return PaymentProvider::PayPal;
    }

    /**
     * Helper to format amount in cents to human readable dollar string.
     */
    private function formatAmount(int $amountInCents): string
    {
        return number_format($amountInCents / 100, 2);
    }
}
