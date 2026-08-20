<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\PaymentProvider;

class PaymentGatewayFactory
{
    /**
     * Resolve the concrete payment gateway instance corresponding to the provider enum.
     */
    public function make(PaymentProvider $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            PaymentProvider::Stripe => app(StripePaymentGateway::class),
            PaymentProvider::PayPal => app(PaypalPaymentGateway::class),
        };
    }

    /**
     * Resolve the default configured payment gateway instance.
     */
    public function default(): PaymentGatewayInterface
    {
        $defaultConfig = (string) config('payments.default', 'stripe');
        $provider = PaymentProvider::tryFrom($defaultConfig) ?? PaymentProvider::Stripe;

        return $this->make($provider);
    }
}
