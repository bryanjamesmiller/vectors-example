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

While [ADR-0002](./ADR-0002-article-semantic-recommendations-via-vector-proximity.md) established the automated article recommendation engine, developers, administrators, and evaluators needed an interactive playground (`/vector-lab`) to experiment with custom texts, inspect raw 512-dimension Float32 vector representations, observe exact microsecond execution latencies, evaluate in-database cosine proximity queries against published articles, and automatically persist tested trade articles directly into PostgreSQL.

---

## 2. Architecture Decisions

### 2.1 Interactive AI Vector Lab (`app/Livewire/VectorLab.php`)
* **Real-Time Telemetry Capture:** Calls `EmbeddingService::generateWithTelemetry()` capturing provider name, model, dimensions, roundtrip latency (measured with `hrtime(true)`), cache status, endpoint URL, and character count.
* **1-Click Presets & Quick Testing:** Pre-populates realistic trade scenarios (Welding, Electrical, Financial Aid, Safety) for rapid demonstration.
* **Full 512d Vector Inspection & Clipboard Export:** Provides an expandable matrix and a 1-click **"📋 Copy Full 512d JSON"** action powered by Alpine.js.
* **In-Database Similarity Ranking:** Queries PostgreSQL using the `<=>` cosine distance operator to calculate percentage match scores:
  $$\text{Match Percentage} = \max(0, \min(100, (1.0 - \text{distance}) \times 100))$$
* **Dynamic Cache Detection & Locked Bypass State:** Detects in real time whether current inputs exist in cache (`Cache::has($cacheKey)`). For new/uncached content, the cache bypass toggle is locked (`checked disabled`) since a live API call is mandatory; for cached content, it enables an interactive toggle (`forceLiveCall`) to allow bypassing the cache on demand.
* **Automatic In-Database Persistence & Publishing:** Calculating vectors in the Vector Lab automatically creates and persists articles directly into PostgreSQL (or updates the existing article without recursive observer re-embeddings via `Article::withoutEvents()`). Newly saved articles immediately appear in the public catalog and participate in semantic recommendations.
* **Live API Rate Limiting:** Enforces per-IP throttling (15 requests per minute) via `RateLimiter::attempt()` across all uncached outbound live API calls to protect provider quotas.

---

## 3. Verification & Testing

* **Feature Tests (`tests/Feature/VectorLabTest.php`):**
  * Verifies preset loading, vector calculation, microsecond telemetry capture, dynamic cache detection, locked checkbox assertions, duplicate title handling, rate limiting, and in-database persistence.
* **Quality Standard:** Passed 100% with Laravel Pint, PHPStan Level 8 (0 errors), and the full Pest test suite.
