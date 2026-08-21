<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\PaymentProvider;
use Carbon\CarbonImmutable;

final readonly class PaymentResponse
{
    /**
     * @param  array<string, mixed>  $rawReference
     */
    public function __construct(
        public bool $successful,
        public ?string $transactionId,
        public PaymentProvider $provider,
        public int $amountInCents,
        public string $currency,
        public string $message,
        public CarbonImmutable $processedAt,
        public array $rawReference = [],
    ) {}

    /**
     * Create a successful payment response DTO.
     *
     * @param  array<string, mixed>  $rawReference
     */
    public static function success(
        string $transactionId,
        PaymentProvider $provider,
        int $amountInCents,
        string $currency,
        string $message = 'Payment successfully authorized and processed.',
        array $rawReference = [],
    ): self {
        return new self(
            successful: true,
            transactionId: $transactionId,
            provider: $provider,
            amountInCents: $amountInCents,
            currency: $currency,
            message: $message,
            processedAt: CarbonImmutable::now(),
            rawReference: $rawReference,
        );
    }

    /**
     * Create a failed payment response DTO.
     *
     * @param  array<string, mixed>  $rawReference
     */
    public static function failure(
        PaymentProvider $provider,
        int $amountInCents,
        string $currency,
        string $message,
        array $rawReference = [],
    ): self {
        return new self(
            successful: false,
            transactionId: null,
            provider: $provider,
            amountInCents: $amountInCents,
            currency: $currency,
            message: $message,
            processedAt: CarbonImmutable::now(),
            rawReference: $rawReference,
        );
    }
}
