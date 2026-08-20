<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentProvider: string
{
    case Stripe = 'stripe';
    case PayPal = 'paypal';

    /**
     * Get the human-readable display label for the payment provider.
     */
    public function label(): string
    {
        return match ($this) {
            self::Stripe => 'Credit / Debit Card (Stripe)',
            self::PayPal => 'PayPal / Pay in 4',
        };
    }

    /**
     * Get the description of payment methods supported by this provider.
     */
    public function description(): string
    {
        return match ($this) {
            self::Stripe => 'Fast checkout via Visa, Mastercard, AMEX, Apple Pay, or Google Pay.',
            self::PayPal => 'Pay securely with your PayPal account balance, linked bank, or Pay in 4 installments.',
        };
    }

    /**
     * Get the icon name associated with this payment provider.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Stripe => 'credit-card',
            self::PayPal => 'currency-dollar',
        };
    }

    /**
     * Get the badge color style for UI display.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::Stripe => 'indigo',
            self::PayPal => 'blue',
        };
    }
}
