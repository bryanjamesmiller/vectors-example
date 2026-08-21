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

    /**
     * Get the badge pill label for UI display.
     */
    public function badgeText(): string
    {
        return match ($this) {
            self::Stripe => 'Card / Apple Pay',
            self::PayPal => 'Wallet / Pay in 4',
        };
    }

    /**
     * Get the Tailwind CSS badge class string for UI display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Stripe => 'bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300',
            self::PayPal => 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300',
        };
    }
}
