<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class PaymentCharge
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public int $amountInCents,
        public string $currency,
        public string $description,
        public string $customerName,
        public string $customerEmail,
        public string $invoiceId,
        public array $metadata = [],
    ) {}
}
