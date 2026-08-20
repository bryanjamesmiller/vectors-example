<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\PaymentCharge;
use App\DTOs\PaymentResponse;
use App\Enums\PaymentProvider;

interface PaymentGatewayInterface
{
    /**
     * Process and charge the given payment details through the payment provider.
     */
    public function charge(PaymentCharge $charge): PaymentResponse;

    /**
     * Return the associated payment provider enum.
     */
    public function provider(): PaymentProvider;
}
