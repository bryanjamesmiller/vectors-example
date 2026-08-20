# Trade School AI — In-Database Vector Search & Semantic Recommendations

[![Live Demo](https://img.shields.io/badge/Live%20Demo-Laravel%20Cloud-blue?style=for-the-badge&logo=laravel)](https://vectors-example-production-jdol4y.laravel.cloud/)
[![PHP](https://img.shields.io/badge/PHP-8.3%20%7C%208.4%20%7C%208.5-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16%20%7C%2017%20with%20pgvector-4169E1?style=flat-square&logo=postgresql)](https://github.com/pgvector/pgvector)
[![Pest Tests](https://img.shields.io/badge/Tests-Pest%20%E2%9C%94-brightgreen?style=flat-square)](https://pestphp.com)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%208-blue?style=flat-square)](https://phpstan.org)

An enterprise trade school knowledge base demonstrating **in-database vector embeddings**, **sub-millisecond semantic similarity search**, and **RAG-ready article recommendations** natively inside PostgreSQL using `pgvector` and Laravel.

---

## 🌐 Live Demo

The application is deployed live on **Laravel Cloud**:

🔗 **[https://vectors-example-production-jdol4y.laravel.cloud/](https://vectors-example-production-jdol4y.laravel.cloud/)**

---

## ⚡ Key Architectural Features

* **In-Database Vector Proximity (`pgvector`):** Uses native PostgreSQL vector cosine distance (`<=>`) with HNSW indexing for real-time, sub-millisecond semantic search and recommendations without external vector database dependencies.
* **Dual-Engine AI Embeddings:**
  * **Local Development (Default):** 100% free, offline, private embeddings powered by a local **Ollama** instance (`nomic-embed-text`).
  * **Cloud / Production:** Managed cloud embeddings via **OpenAI** (`text-embedding-3-small`, truncated to 512 Matryoshka dimensions).
* **Deterministic Seeding & Caching:** Pre-computed 512-dimension vectors in seed fixtures allow instant environment provisioning in milliseconds with zero network or AI model calls.
* **Modern UI & Stack:** Built with Laravel, Livewire 4, Flux UI, and Tailwind CSS.
* **Zero-Downtime Serverless Hosting:** Hosted on Laravel Cloud with Serverless Postgres (Dev configuration with Scale-to-Zero).

---

## 🛠 Tech Stack

* **Framework:** [Laravel](https://laravel.com)
* **Frontend:** [Livewire](https://livewire.laravel.com), [Flux UI](https://flux.laravel.com), [Tailwind CSS](https://tailwindcss.com)
* **Database & Vectors:** PostgreSQL (16/17) with [`pgvector`](https://github.com/pgvector/pgvector) and `pgvector/pgvector-php`
* **AI & Embeddings:** [OpenAI PHP](https://github.com/openai-php/laravel) & [Ollama](https://ollama.com)
* **Testing & Quality:** [Pest PHP](https://pestphp.com), [Larastan / PHPStan (Level 8)](https://github.com/larastan/larastan), [Laravel Pint](https://laravel.com/docs/pint)
* **Hosting:** [Laravel Cloud](https://cloud.laravel.com) (Serverless PostgreSQL 17 + Dev Compute)

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
  -e POSTGRES_DB=vectors_postgres_slow \
  -p 5432:5432 \
  pgvector/pgvector:pg16

# Create the testing database
docker exec pgvector-db createdb -U postgres vectors_postgres_testing
```

*(If the container already exists, simply run `docker start pgvector-db`)*

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
Visit **[http://localhost:8000](http://localhost:8000)** (or your local Herd/Valet domain) to explore the knowledge base and live vector recommendations!

---

## 🧪 Testing & Code Quality

Run static analysis, code formatting checks, and automated Pest tests:

```bash
# Code Formatter
./vendor/bin/pint --format agent

# PHPStan Static Analysis (Level 8)
./vendor/bin/phpstan analyse

# Pest Test Suite
php artisan test --compact
```

---

## 📚 Architecture Decision Records (ADRs)

For deep dives into design decisions, vector indexing trade-offs, and schema architecture:

* [ADR-0001: Multi-Tenant AI Assistant & Document RAG Architecture](./documentation/ADR-0001-vector-embeddings-and-rag-architecture.md)
* [ADR-0002: Semantic Article Recommendations via In-Database Vector Proximity](./documentation/ADR-0002-article-semantic-recommendations-via-vector-proximity.md)
* [ADR-0003: Environment Setup, Local Onboarding, and Vector Operations](./documentation/ADR-0003-environment-setup-and-local-onboarding.md)
