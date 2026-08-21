# Trade School AI — In-Database Vector Search & Semantic Recommendations

[![Live Demo](https://img.shields.io/badge/Live%20Demo-Laravel%20Cloud-blue?style=for-the-badge&logo=laravel)](https://vectors-example-production-jdol4y.laravel.cloud/)
[![PHP](https://img.shields.io/badge/PHP-8.3%20%7C%208.4%20%7C%208.5-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16%20%7C%2017%20with%20pgvector-4169E1?style=flat-square&logo=postgresql)](https://github.com/pgvector/pgvector)
[![Pest Tests](https://img.shields.io/badge/Tests-Pest%20%E2%9C%94-brightgreen?style=flat-square)](https://pestphp.com)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%208-blue?style=flat-square)](https://phpstan.org)

A trade school knowledge base demonstrating **in-database vector embeddings**, **semantic similarity search**, and **RAG-ready article recommendations** inside PostgreSQL using `pgvector` and Laravel.

---

## 🌐 Live Demo

The application is deployed live on **Laravel Cloud**:

🔗 **[https://vectors-example-production-jdol4y.laravel.cloud/](https://vectors-example-production-jdol4y.laravel.cloud/)**

---

## ⚡ Key Architectural Features

* **In-Database Semantic Vector Search (`/articles`):** Natural language search powered by 512-dimension vector embeddings and PostgreSQL `<=>` cosine distance rather than SQL `LIKE`/`ILIKE` substring matching. User queries are embedded on the fly and matched against the knowledge base by conceptual meaning, displaying real-time **`% Match`** scores on each result card.
* **Interactive AI Vector Lab (`/vector-lab`):** An interactive playground to test live AI vector embeddings, inspect roundtrip latency, copy 512d JSON matrices, and evaluate PostgreSQL `<=>` cosine distance proximity rankings in real time.
* **Swappable Multi-Payment Gateway Architecture (`/payments`):** Demonstrates SOLID principles and the Strategy pattern with an Enum-driven factory for trade school tuition billing, supporting swappable payment gateways (Stripe, PayPal) wrapped in database transactions.
* **In-Database Vector Proximity Recommendations (`pgvector`):** Uses PostgreSQL vector cosine distance (`<=>`) with HNSW indexing for real-time automated recommendations on article pages without external vector database dependencies.
* **Dual-Engine AI Embeddings:**
  * **Local Development (Default):** Offline embeddings powered by a local **Ollama** instance (`nomic-embed-text`).
  * **Cloud / Production:** Managed cloud embeddings via **OpenAI** (`text-embedding-3-small`, 512 Matryoshka dimensions) with support for any OpenAI-compatible provider.
* **SHA-256 Content Caching & Quota Protection:** Fingerprints text inputs using SHA-256 hashes for instant cache hits, backed by per-IP rate limiting guards (15 req/min) on outbound live API calls.
* **Vector Persistence & Publishing:** Calculating vectors in the Vector Lab persists articles to PostgreSQL, displaying them in the public Articles catalog and indexing them for recommendations.
* **Deterministic Seeding:** Pre-computed 512-dimension vectors in seed fixtures allow instant environment provisioning with zero network or AI model calls.
* **Modern UI:** Built with Laravel, Livewire 4, Flux UI, and Tailwind CSS.
* **Serverless Hosting:** Deployed on Laravel Cloud with PostgreSQL.

---

### 🔍 How In-Database Vector Search Works (vs. SQL `LIKE`)

Traditional keyword searches rely on exact substring matching (`WHERE title ILIKE '%welding%'`), which fails when users search with synonyms or conceptual descriptions (e.g., searching for *"financial aid for apprentices"* misses articles titled *"Trade Tool Grants & Fee Waivers"*).

With native `pgvector`, the search query is converted into a 512-dimension vector embedding and ordered in PostgreSQL using cosine distance (`<=>`):

```php
// 1. Convert natural language user search query into 512d vector
$queryVector = $embeddingService->generateEmbedding($userSearchQuery);

// 2. Query PostgreSQL pgvector index by cosine proximity (<=>)
$articles = Article::query()
    ->where('is_published', true)
    ->whereNotNull('embedding')
    ->selectRaw('articles.*, (articles.embedding <=> ?) as neighbor_distance', [new Vector($queryVector)])
    ->orderBy('neighbor_distance')
    ->paginate(9);
```

Each result calculates an intuitive similarity match percentage:
$$\text{Match Percentage} = \max(0, \min(100, (1.0 - \text{distance}) \times 100))$$

---

## 🛠 Tech Stack

* **Framework:** [Laravel](https://laravel.com)
* **Frontend:** [Livewire](https://livewire.laravel.com), [Flux UI](https://flux.laravel.com), [Tailwind CSS](https://tailwindcss.com)
* **Database & Vectors:** PostgreSQL (16/17) with [`pgvector`](https://github.com/pgvector/pgvector) and `pgvector/pgvector-php`
* **AI & Embeddings:** [OpenAI PHP](https://github.com/openai-php/laravel) & [Ollama](https://ollama.com)
* **Testing & Quality:** [Pest PHP](https://pestphp.com), [Larastan / PHPStan (Level 8)](https://github.com/larastan/larastan), [Laravel Pint](https://laravel.com/docs/pint)
* **Hosting:** [Laravel Cloud](https://cloud.laravel.com)

---

## 🚀 Local Quick-Start Guide

### Prerequisites
* PHP `^8.3` (Supports PHP 8.3, 8.4, and 8.5)
* Composer 2.x & Node.js 20+
* Docker (for PostgreSQL + `pgvector`)

### 1. Clone & Install Dependencies
```bash
git clone https://github.com/bryanjamesmiller/vectors-example.git
cd vectors-example

# Install PHP dependencies
composer install

# Install and build frontend assets
npm install
npm run build

# Set up environment
cp .env.example .env
php artisan key:generate
```

### 2. Start PostgreSQL with `pgvector`
Start a PostgreSQL container with `pgvector` enabled:

```bash
docker run -d \
  --name pgvector-db \
  -e POSTGRES_PASSWORD=secret \
  -e POSTGRES_DB=vectors_postgres_fast \
  -p 5432:5432 \
  pgvector/pgvector:pg16

# Create the testing database
docker exec pgvector-db createdb -U postgres vectors_postgres_testing
```

*(If the container already exists, run `docker start pgvector-db`)*

### 3. Run Migrations & Seed Articles
```bash
php artisan migrate --force && php artisan db:seed --force
```

### 4. (Optional) Run Local AI Embedding Engine (Ollama)
For free local vector generation without an OpenAI API key:

```bash
brew install ollama
brew services start ollama
ollama pull nomic-embed-text
```

### 5. Start Development Server
```bash
php artisan serve
```
Visit **[http://localhost:8000](http://localhost:8000)** (or your local Herd/Valet domain) to explore the Vector Lab (`/vector-lab`), articles (`/articles`), and tuition payments (`/payments`)!

---

## 🧪 Testing & Code Quality

Run static analysis, code formatting checks, and automated Pest tests:

```bash
# Code Formatter
./vendor/bin/pint --format agent

# PHPStan Static Analysis (Level 8)
./vendor/bin/phpstan analyse --level=8

# Pest Test Suite
php artisan test --compact
```

---

## 📚 Architecture Decision Records (ADRs)

For deep dives into design decisions, vector indexing trade-offs, and schema architecture:

* [ADR-0001: Multi-Tenant AI Assistant & Document RAG Architecture](./documentation/ADR-0001-vector-embeddings-and-rag-architecture.md)
* [ADR-0002: Semantic Article Recommendations via In-Database Vector Proximity](./documentation/ADR-0002-article-semantic-recommendations-via-vector-proximity.md)
* [ADR-0003: Environment Setup, Local Onboarding, and Vector Operations](./documentation/ADR-0003-environment-setup-and-local-onboarding.md)
* [ADR-0004: Interactive AI Vector Lab & In-Database Persistence](./documentation/ADR-0004-interactive-ai-vector-lab-and-in-database-persistence.md)
* [ADR-0005: Swappable Multi-Payment Gateway Architecture & Enum-Driven Strategy Pattern](./documentation/ADR-0005-swappable-multipayment-gateway-architecture.md)
