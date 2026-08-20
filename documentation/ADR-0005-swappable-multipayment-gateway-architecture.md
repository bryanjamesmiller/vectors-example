# ADR 0005: Swappable Multi-Payment Gateway Architecture & Enum-Driven Strategy Pattern

* **Status:** Accepted (2026-08-20)
* **Author:** Architecture & Engineering Team
* **Target Stack:** PHP 8.3+ (Active runtime: PHP 8.5), Laravel 12+, Livewire 4.x, Flux UI, Pest 5.x, PHPStan (Level 8)
* **Related Records:**
  * [ADR-0001: Multi-Tenant AI Assistant & Document RAG Architecture](./ADR-0001-vector-embeddings-and-rag-architecture.md)
  * [ADR-0004: Interactive AI Vector Lab & In-Database Persistence](./ADR-0004-interactive-ai-vector-lab-and-in-database-persistence.md)

---

## 1. Context & Problem Statement

### 1.1 The Operational Challenge
Trade schools must collect tuition, lab consumable fees, and equipment deposits from apprentices and students. Different payment processors (such as Stripe, PayPal, Square, or regional bank integrations) offer varied fee structures, settlement schedules, and payment methods (credit/debit cards, Apple Pay, PayPal Balance, Pay in 4).

Hardcoding a single third-party payment gateway directly inside controllers or Livewire components introduces severe technical debt:
1. **Vendor Lock-In & Tight Coupling:** Swapping or adding a secondary provider requires invasive changes across controllers, views, and tests.
2. **Fragile Error Handling:** Vendor SDKs throw disparate exceptions and return proprietary response structures.
3. **Data Integrity Hazards:** Payment transactions executed without transactional boundaries risk out-of-sync database records if post-charge bookkeeping fails.

### 1.2 The Solution
We adopt the **Strategy Pattern** combined with an **Enum-Driven Factory** and **Strongly-Typed Data Transfer Objects (DTOs)**. This architecture allows payment providers (Stripe, PayPal, or future services) to be 100% swappable at runtime while enforcing compile-time type safety and strict database transactional integrity.

---

## 2. SOLID Architectural Principles Applied

```
                  ┌──────────────────────────────┐
                  │      TuitionBillPayment      │
                  │     (Livewire Component)     │
                  └──────────────┬───────────────┘
                                 │
                                 ▼
                  ┌──────────────────────────────┐
                  │       PaymentProcessor       │
                  │   (DB Transaction & Audit)   │
                  └──────────────┬───────────────┘
                                 │ resolves via
                                 ▼
                  ┌──────────────────────────────┐
                  │    PaymentGatewayFactory     │
                  │    (Enum-Driven Resolver)    │
                  └──────────────┬───────────────┘
                                 │ returns
                                 ▼
                  ┌──────────────────────────────┐
                  │   PaymentGatewayInterface    │
                  │        <<Contract>>          │
                  └──────────────┬───────────────┘
                                 │
                ┌────────────────┴────────────────┐
                ▼                                 ▼
    ┌──────────────────────┐          ┌──────────────────────┐
    │ StripePaymentGateway │          │ PaypalPaymentGateway │
    │ (Stripe Integration) │          │ (PayPal Integration) │
    └──────────────────────┘          └──────────────────────┘
```

| SOLID Principle | Practical Application in this Payment Subsystem |
| :--- | :--- |
| **S — Single Responsibility** | `PaymentProcessor` handles database transactional boundaries and audit logging. `PaymentGatewayFactory` handles driver instantiation. Individual gateway classes (`StripePaymentGateway`, `PaypalPaymentGateway`) handle third-party communication. DTOs encapsulate charge payloads. |
| **O — Open / Closed** | To introduce a 3rd payment provider (e.g. Square, Apple Pay, or Klarna) 3 months down the road, developers simply add a new case to `PaymentProvider` and a new class implementing `PaymentGatewayInterface`. **Zero existing gateway classes or business logic files are modified.** |
| **L — Liskov Substitution** | All gateways conform strictly to `PaymentGatewayInterface::charge(PaymentCharge $charge): PaymentResponse`. Calling code interacts identically with every provider without provider-specific exception leaks or behavioral deviations. |
| **I — Interface Segregation** | The core `PaymentGatewayInterface` is intentionally minimal (`charge()`, `provider()`), avoiding bloated contracts that force providers to implement non-applicable methods (such as webhooks or customer portals). |
| **D — Dependency Inversion** | High-level business actions (Livewire components, checkout actions) depend solely on abstractions (`PaymentGatewayInterface`, `PaymentGatewayFactory`), never directly instantiating vendor SDK clients (`new StripeClient()`). |

---

## 3. Decision Rationale: Enum-Driven Factory vs. Laravel `Manager`

While Laravel's traditional `Illuminate\Support\Manager` is useful for third-party runtime package extensions, application-level payment routing benefits from an **Enum-Driven Factory**:

1. **Compile-Time Type Safety:** `PaymentProvider::Stripe` prevents string typos (e.g., `'strippe'`).
2. **Built-in Form Validation:** Native Laravel `Rule::enum(PaymentProvider::class)` ensures requests with invalid providers fail fast at the boundary.
3. **Exhaustive Matching:** PHP 8's `match($provider)` expressions alert static analysis tools (PHPStan / Larastan) if an enum case is unhandled.
4. **Rich Metadata Encapsulation:** Display labels, brand colors, and icon names reside cleanly within the backed Enum.

---

## 4. Implementation Blueprint

### 4.1 Enums & DTO Contracts

```php
// app/Enums/PaymentProvider.php
enum PaymentProvider: string
{
    case Stripe = 'stripe';
    case PayPal = 'paypal';

    public function label(): string
    {
        return match ($this) {
            self::Stripe => 'Credit / Debit Card (Stripe)',
            self::PayPal => 'PayPal / Pay in 4',
        };
    }
}
```

```php
// app/Contracts/PaymentGatewayInterface.php
interface PaymentGatewayInterface
{
    public function charge(PaymentCharge $charge): PaymentResponse;
    public function provider(): PaymentProvider;
}
```

```php
// app/DTOs/PaymentCharge.php
readonly class PaymentCharge
{
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
```

```php
// app/DTOs/PaymentResponse.php
readonly class PaymentResponse
{
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
}
```

### 4.2 Database Transaction & Processing Layer

```php
// app/Services/Payments/PaymentProcessor.php
class PaymentProcessor
{
    public function __construct(
        protected PaymentGatewayFactory $factory
    ) {}

    public function process(PaymentProvider $provider, PaymentCharge $charge): PaymentResponse
    {
        return DB::transaction(function () use ($provider, $charge): PaymentResponse {
            $gateway = $this->factory->make($provider);
            $response = $gateway->charge($charge);

            if (! $response->successful) {
                throw new PaymentFailedException($response->message);
            }

            // Record audit log, update student billing balance in database
            return $response;
        });
    }
}
```

---

## 5. Verification & Testing Strategy

1. **Liskov Substitution Testing:** Pest tests verify that all implementations of `PaymentGatewayInterface` return uniform `PaymentResponse` objects given identical `PaymentCharge` inputs.
2. **Provider Swapping Verification:** Test cases verify switching `selectedProvider` between `Stripe` and `PayPal` successfully dispatches to the corresponding gateway.
3. **Validation & Boundary Testing:** Livewire component tests reject invalid provider payloads and verify transactional rollback behavior.
4. **Code Quality:** All PHP files declare `strict_types=1`, format with Laravel Pint, and pass PHPStan at Level 8.
