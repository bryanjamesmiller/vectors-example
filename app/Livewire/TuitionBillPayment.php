<?php

declare(strict_types=1);

namespace App\Livewire;

use App\DTOs\PaymentCharge;
use App\Enums\PaymentProvider;
use App\Services\Payments\PaymentProcessor;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Trade School Tuition & Bill Payment — Multi-Gateway Demo')]
class TuitionBillPayment extends Component
{
    /**
     * Currently selected payment provider value ('stripe' or 'paypal').
     */
    public string $selectedProvider = 'stripe';

    /**
     * Student details for the tuition bill.
     *
     * @var array{
     *     name: string,
     *     student_id: string,
     *     email: string,
     *     program: string,
     *     term: string,
     *     invoice_number: string,
     *     due_date: string,
     *     amount_in_cents: int,
     *     currency: string,
     *     items: list<array{description: string, category: string, amount_in_cents: int}>
     * }
     */
    public array $bill = [
        'name' => 'Marcus Vance',
        'student_id' => 'TS-88421',
        'email' => 'marcus.vance@tradeschool.test',
        'program' => 'Industrial Welding & Metallurgy Apprenticeship',
        'term' => 'Fall 2026 Term',
        'invoice_number' => 'INV-2026-0884',
        'due_date' => 'August 31, 2026 (End of Month)',
        'amount_in_cents' => 125000,
        'currency' => 'USD',
        'items' => [
            [
                'description' => 'Industrial Pipe & SMAW / TIG Practical Lab Tuition',
                'category' => 'Core Tuition',
                'amount_in_cents' => 115000,
            ],
            [
                'description' => 'OSHA Safety Helmet, Shade-11 Lens & Consumables Kit',
                'category' => 'Materials & PPE',
                'amount_in_cents' => 10000,
            ],
        ],
    ];

    /**
     * Whether the bill has been paid in the current session.
     */
    public bool $isPaid = false;

    /**
     * Data from the last processed payment.
     *
     * @var array{
     *     transaction_id: string,
     *     provider: string,
     *     provider_label: string,
     *     amount_formatted: string,
     *     message: string,
     *     processed_at: string,
     *     receipt_url: ?string,
     *     raw_reference: array<string, mixed>
     * }|null
     */
    public ?array $paymentReceipt = null;

    /**
     * Validation rules.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'selectedProvider' => ['required', 'string', Rule::enum(PaymentProvider::class)],
        ];
    }

    /**
     * Process the simulated tuition payment via the selected swappable gateway.
     */
    public function processPayment(PaymentProcessor $processor): void
    {
        $this->validate();

        $providerEnum = PaymentProvider::from($this->selectedProvider);

        $charge = new PaymentCharge(
            amountInCents: $this->bill['amount_in_cents'],
            currency: $this->bill['currency'],
            description: "Tuition payment for {$this->bill['program']} ({$this->bill['term']})",
            customerName: $this->bill['name'],
            customerEmail: $this->bill['email'],
            invoiceId: $this->bill['invoice_number'],
            metadata: [
                'student_id' => $this->bill['student_id'],
                'term' => $this->bill['term'],
                'program' => $this->bill['program'],
            ]
        );

        $response = $processor->process($providerEnum, $charge);

        if ($response->successful) {
            $this->isPaid = true;
            $this->paymentReceipt = [
                'transaction_id' => (string) $response->transactionId,
                'provider' => $response->provider->value,
                'provider_label' => $response->provider->label(),
                'amount_formatted' => '$'.number_format($response->amountInCents / 100, 2),
                'message' => $response->message,
                'processed_at' => $response->processedAt->format('M d, Y — h:i:s A'),
                'receipt_url' => isset($response->rawReference['receipt_url']) ? (string) $response->rawReference['receipt_url'] : null,
                'raw_reference' => $response->rawReference,
            ];

            Flux::toast(
                text: "Payment of \${$this->paymentReceipt['amount_formatted']} successfully processed via {$response->provider->label()}!",
                variant: 'success'
            );
        } else {
            Flux::toast(
                text: "Payment failed: {$response->message}",
                variant: 'danger'
            );
        }
    }

    /**
     * Reset payment status so the user can test different providers repeatedly.
     */
    public function resetPayment(): void
    {
        $this->isPaid = false;
        $this->paymentReceipt = null;

        Flux::toast(
            text: 'Bill state reset. You can now test a different payment gateway.',
            variant: 'info'
        );
    }

    /**
     * Render the tuition bill Livewire component.
     */
    public function render(): View
    {
        return view('livewire.tuition-bill-payment', [
            'providers' => PaymentProvider::cases(),
        ])->layout('layouts.app.header', ['title' => 'Trade School Tuition & Multi-Gateway Payment']);
    }
}
