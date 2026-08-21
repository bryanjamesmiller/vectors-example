<?php

declare(strict_types=1);

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentCharge;
use App\DTOs\PaymentResponse;
use App\Enums\PaymentProvider;
use App\Livewire\TuitionBillPayment;
use App\Services\Payments\PaymentGatewayFactory;
use App\Services\Payments\PaymentProcessor;
use App\Services\Payments\PaypalPaymentGateway;
use App\Services\Payments\StripePaymentGateway;
use Livewire\Livewire;

test('payments page is publicly accessible without login', function () {
    $response = $this->get(route('payments'));

    $response->assertOk()
        ->assertSee('Trade School Student Billing and Payment Portal')
        ->assertSee('Marcus Vance')
        ->assertSee('$1,250.00')
        ->assertSee('Industrial Welding &amp; Metallurgy Apprenticeship', false)
        ->assertSee('August 31, 2026 (End of Month)');
});

test('payments page displays itemized schedule and payment provider options', function () {
    Livewire::test(TuitionBillPayment::class)
        ->assertSee('Industrial Pipe & SMAW / TIG Practical Lab Tuition')
        ->assertSee('$1,150.00')
        ->assertSee('OSHA Safety Helmet, Shade-11 Lens & Consumables Kit')
        ->assertSee('$100.00')
        ->assertSee('Credit / Debit Card (Stripe)')
        ->assertSee('PayPal / Pay in 4')
        ->assertSet('selectedProvider', 'stripe')
        ->assertSet('isPaid', false);
});

test('student can successfully process tuition payment using stripe provider', function () {
    Livewire::test(TuitionBillPayment::class)
        ->set('selectedProvider', 'stripe')
        ->call('processPayment')
        ->assertSet('isPaid', true)
        ->assertSet('paymentReceipt.provider', 'stripe')
        ->assertSee('Payment Confirmed and Settled')
        ->assertSee('PAID IN FULL')
        ->assertSee('ch_stripe_');
});

test('student can successfully process tuition payment using paypal provider', function () {
    Livewire::test(TuitionBillPayment::class)
        ->set('selectedProvider', 'paypal')
        ->call('processPayment')
        ->assertSet('isPaid', true)
        ->assertSet('paymentReceipt.provider', 'paypal')
        ->assertSee('Payment Confirmed and Settled')
        ->assertSee('PAID IN FULL')
        ->assertSee('PAYID-');
});

test('validation rejects invalid payment provider selection', function () {
    Livewire::test(TuitionBillPayment::class)
        ->set('selectedProvider', 'invalid_crypto_gateway')
        ->call('processPayment')
        ->assertHasErrors(['selectedProvider'])
        ->assertSet('isPaid', false);
});

test('student can reset the bill state to test multiple gateways in sequence', function () {
    Livewire::test(TuitionBillPayment::class)
        ->set('selectedProvider', 'stripe')
        ->call('processPayment')
        ->assertSet('isPaid', true)
        ->call('resetPayment')
        ->assertSet('isPaid', false)
        ->assertSet('paymentReceipt', null)
        ->set('selectedProvider', 'paypal')
        ->call('processPayment')
        ->assertSet('isPaid', true)
        ->assertSet('paymentReceipt.provider', 'paypal');
});

test('PaymentGatewayFactory resolves swappable strategy implementations conforming to contract (LSP)', function () {
    $factory = app(PaymentGatewayFactory::class);

    $stripeGateway = $factory->make(PaymentProvider::Stripe);
    expect($stripeGateway)->toBeInstanceOf(StripePaymentGateway::class)
        ->and($stripeGateway)->toBeInstanceOf(PaymentGatewayInterface::class)
        ->and($stripeGateway->provider())->toBe(PaymentProvider::Stripe);

    $paypalGateway = $factory->make(PaymentProvider::PayPal);
    expect($paypalGateway)->toBeInstanceOf(PaypalPaymentGateway::class)
        ->and($paypalGateway)->toBeInstanceOf(PaymentGatewayInterface::class)
        ->and($paypalGateway->provider())->toBe(PaymentProvider::PayPal);

    $defaultGateway = $factory->default();
    expect($defaultGateway)->toBeInstanceOf(PaymentGatewayInterface::class);
});

test('PaymentProcessor wraps charge execution and returns uniform response DTO', function () {
    $processor = app(PaymentProcessor::class);

    $charge = new PaymentCharge(
        amountInCents: 125000,
        currency: 'USD',
        description: 'Test Welding Lab Tuition',
        customerName: 'Marcus Vance',
        customerEmail: 'marcus@test.com',
        invoiceId: 'INV-TEST-001',
    );

    $response = $processor->process(PaymentProvider::Stripe, $charge);

    expect($response)->toBeInstanceOf(PaymentResponse::class)
        ->and($response->successful)->toBeTrue()
        ->and($response->amountInCents)->toBe(125000)
        ->and($response->currency)->toBe('USD')
        ->and($response->provider)->toBe(PaymentProvider::Stripe)
        ->and($response->transactionId)->toStartWith('ch_stripe_');
});

test('TuitionBillPayment initializes default provider and currency from configuration', function () {
    config()->set('payments.default', 'paypal');
    config()->set('payments.currency', 'CAD');

    Livewire::test(TuitionBillPayment::class)
        ->assertSet('selectedProvider', 'paypal')
        ->assertSet('bill.currency', 'CAD');
});

test('payment failure response keeps bill in unpaid state and reports error', function () {
    $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
    $mockGateway->shouldReceive('charge')
        ->once()
        ->andReturn(PaymentResponse::failure(
            provider: PaymentProvider::Stripe,
            amountInCents: 125000,
            currency: 'USD',
            message: 'Card declined: Insufficient funds.'
        ));
    $mockGateway->shouldReceive('provider')
        ->andReturn(PaymentProvider::Stripe);

    $mockFactory = Mockery::mock(PaymentGatewayFactory::class);
    $mockFactory->shouldReceive('make')
        ->with(PaymentProvider::Stripe)
        ->andReturn($mockGateway);
    $mockFactory->shouldReceive('default')
        ->andReturn($mockGateway);

    $this->app->instance(PaymentGatewayFactory::class, $mockFactory);

    Livewire::test(TuitionBillPayment::class)
        ->set('selectedProvider', 'stripe')
        ->call('processPayment')
        ->assertSet('isPaid', false)
        ->assertSet('paymentReceipt', null)
        ->assertDontSee('PAID IN FULL');
});

test('PaymentGatewayFactory throws ValueError when default configuration provider is invalid', function () {
    config()->set('payments.default', 'invalid_provider_name');

    $factory = app(PaymentGatewayFactory::class);

    expect(fn () => $factory->default())->toThrow(ValueError::class);
});
