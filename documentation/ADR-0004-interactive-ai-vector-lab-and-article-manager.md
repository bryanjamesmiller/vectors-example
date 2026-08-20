# ADR 0004: Interactive AI Vector Lab & In-Database Persistence

* **Status:** Accepted (2026-08-20)
* **Author:** Architecture & Engineering Team
* **Target Stack:** PHP 8.3+ (Active runtime: PHP 8.5), Laravel 12+, Livewire, PostgreSQL 16 with \`pgvector\`, Tailwind CSS, Pest 5.x, PHPStan (Level 8)
* **Related Records:**
  * [ADR-0001: Multi-Tenant AI Assistant & Document RAG Architecture](./ADR-0001-vector-embeddings-and-rag-architecture.md)
  * [ADR-0002: Semantic Article Recommendations via In-Database Vector Proximity](./ADR-0002-article-semantic-recommendations-via-vector-proximity.md)
  * [ADR-0003: Environment Setup, Local Onboarding, and Vector Operations](./ADR-0003-environment-setup-and-local-onboarding.md)

---

## 1. Context & Problem Statement

While [ADR-0002](./ADR-0002-article-semantic-recommendations-via-vector-proximity.md) established the automated article recommendation engine, developers, administrators, and evaluators needed:
1. **An Interactive Inspection Playground (\`/vector-lab\`):** An interface to experiment with custom texts, inspect raw 512-dimension Float32 vector representations, observe exact microsecond execution latencies, and evaluate in-database cosine proximity queries against published articles.
2. **An Administrative Articles Manager (\`/dashboard\`):** A centralized Livewire dashboard to create, edit, filter, delete, and re-embed articles with real-time vector status indicators.

---

## 2. Architecture Decisions

### 2.1 Interactive AI Vector Lab (\`app/Livewire/VectorLab.php\`)
* **Real-Time Telemetry Capture:** Calls \`EmbeddingService::generateWithTelemetry()\` capturing provider name, model, dimensions, roundtrip latency (measured with \`hrtime(true)\`), cache status, endpoint URL, and character count.
* **1-Click Presets & Quick Testing:** Pre-populates realistic trade scenarios (Welding, HVAC, Financial Aid) for rapid demonstration.
* **Full 512d Vector Inspection & Clipboard Export:** Provides an expandable matrix and a 1-click **"📋 Copy Full 512d JSON"** action powered by Alpine.js.
* **In-Database Similarity Ranking:** Queries PostgreSQL using the \`<=>\` cosine distance operator to calculate percentage match scores:
  $$\\text{Match Percentage} = \\max(0, \\min(100, (1.0 - \\text{distance}) \\times 100))$$
* **1-Click Publishing:** Allows saving the experiment directly to PostgreSQL as an active trade article.
* **Live API Rate Limiting:** Enforces per-IP throttling (15 requests per minute) via `RateLimiter::attempt()` across all uncached outbound live API calls to protect provider quotas.

### 2.2 Articles Manager Component (`app/Livewire/ArticlesManager.php`)
* **Live CRUD Table:** Displays article titles, audiences, vector status badges (`Indexed (512d)` vs `Pending Embedding`), and publication states with Livewire pagination.
* **PostgreSQL Case-Insensitive Search:** Uses Laravel's `whereLike(..., caseSensitive: false)` across title, summary, and content.
* **On-Demand Re-Embedding:** Provides a 1-click button to re-dispatch `GenerateArticleEmbeddingJob` for any existing article.
* **Safe Public Navigation:** Renders disabled indicators for draft articles to eliminate 404 navigation errors.
* **Accessible Modals:** Full dialog semantics (`role="dialog"`, `aria-modal="true"`, `wire:keydown.window.escape` dismissal).

---

## 3. Verification & Testing

* **Feature Tests (`tests/Feature/VectorLabTest.php`):**
  * Verifies preset loading, vector calculation, microsecond telemetry capture, rate limiting, and 1-click publishing.
* **Feature Tests (`tests/Feature/ArticlesManagerTest.php`):**
  * Verifies guest authentication barriers, metrics calculation, article creation, mixed-case search, and re-embedding job dispatch.
* **Quality Standard:** Passed 100% with Laravel Pint, PHPStan Level 8 (0 errors), and the full Pest test suite.
