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

### 1. Semantic Vector Search (vs. Lexical String Matching)

Traditional keyword searches rely on exact substring matching (`WHERE title ILIKE '%welding%'`), which fails when users search with synonyms or conceptual descriptions (e.g., searching for *"financial aid for apprentices"* misses articles titled *"Trade Tool Grants & Fee Waivers"*).

* **On-the-Fly Query Vectorization:** Incoming search queries (`/articles?q=...`) are converted into 512-dimension vector embeddings at runtime.
* **In-Database Cosine Ordering:** Queries PostgreSQL directly using the `<=>` cosine distance operator against indexed `vector(512)` columns.
* **Normalized Match Scores:** Converts raw distance into human-readable relevance percentage scores on result cards:
  $$\text{Match Percentage} = \max(0, \min(100, (1.0 - \text{distance}) \times 100))$$

### 2. In-Database Proximity Recommendations

Generates related trade school article recommendations natively within PostgreSQL without external vector databases (Pinecone/Milvus) or runtime LLM inference latency.

* **HNSW Indexing:** Employs hierarchical navigable small world (`USING hnsw (embedding vector_cosine_ops)`) indexes for sub-millisecond nearest-neighbor retrieval.
* **Hybrid Relational Filtering:** Blends vector proximity with standard relational SQL constraints (e.g., audience scoping for students, teachers, or alumni).
* **Zero External Dependencies:** Eliminates external vector synchronization pipelines, network hops, and third-party SaaS billing.

### 3. Interactive AI Vector Lab & Live Vector Generation (`/vector-lab`)

An interactive workbench to generate embeddings, inspect raw vector representations, and persist new articles directly to the database.

* **Dual-Engine Pipeline:** Runs offline via local **Ollama** (`nomic-embed-text`) in development, and cloud **OpenAI** (`text-embedding-3-small`, 512 Matryoshka dimensions) in production.
* **SHA-256 Fingerprint Caching:** Caches vectors by content hash for instant cache hits, backed by per-IP rate limiting (15 req/min) on outbound live API calls.
* **Vector Generation & Publishing:** Calculating vectors in the lab persists articles to PostgreSQL, immediately indexing them and linking them to neighboring articles via vector proximity.

### 4. Swappable Multi-Payment Gateway Architecture (`/payments`)

An enterprise tuition billing portal demonstrating SOLID principles and the Strategy pattern for decoupled third-party integrations.

* **Strategy & Factory Pattern:** Decouples gateway providers (Stripe, PayPal) behind `PaymentGatewayInterface` and an Enum-driven `PaymentGatewayFactory` for zero-modification extensibility.
* **Transactional Integrity:** Wraps payment processing and ledger updates in `DB::transaction` to prevent out-of-sync financial records.
* **Liskov Substitution & DTOs:** Strongly-typed input (`PaymentCharge`) and output (`PaymentResponse`) DTOs ensure all gateway drivers are interchangeable without provider-specific exception leaks.

---

## 🛠 Tech Stack

* **Framework:** [Laravel](https://laravel.com)
* **Frontend:** [Livewire](https://livewire.laravel.com), [Flux UI](https://flux.laravel.com), [Tailwind CSS](https://tailwindcss.com)
* **Database & Vectors:** PostgreSQL (16/17) with [`pgvector`](https://github.com/pgvector/pgvector) and `pgvector/pgvector-php`
* **AI & Embeddings:** [OpenAI PHP](https://github.com/openai-php/laravel) & [Ollama](https://ollama.com)
* **Testing & Quality:** [Pest PHP](https://pestphp.com), [Larastan / PHPStan (Level 8)](https://github.com/larastan/larastan), [Laravel Pint](https://laravel.com/docs/pint)
* **Hosting:** [Laravel Cloud](https://cloud.laravel.com) (Serverless PostgreSQL 17)

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
