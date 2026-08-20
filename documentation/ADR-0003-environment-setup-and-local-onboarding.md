# ADR-0003: Environment Setup, Local Onboarding, and Vector Operations

## Status
**Accepted** (2026-08-20)

## Context
This project implements in-database vector embeddings and semantic recommendations using Laravel, PostgreSQL with the `pgvector` extension, and an AI embedding provider. 

To ensure friction-free onboarding for developers and CI/CD pipelines without incurring cloud API costs or requiring active third-party credentials, the project supports a dual-engine architecture:
1. **Local Development (Default):** 100% free, offline, private embeddings powered by a local **Ollama** daemon using `nomic-embed-text`.
2. **Production / Cloud (Configurable):** Managed cloud embeddings via the **OpenAI API** (`text-embedding-3-small`).

This document records the exact setup sequence, container requirements, configuration options, and day-to-day operational commands.

---

## Architecture Decisions

### 1. Database: PostgreSQL 16 with `pgvector`
* The application requires PostgreSQL with the `vector` extension enabled.
* Standard local development runs via Docker container `pgvector-db` (`pgvector/pgvector:pg16`).
* HNSW cosine distance indexing (`hnsw (embedding vector_cosine_ops)`) is used for sub-millisecond similarity queries (`<=>`).

### 2. Local AI Engine: Ollama
* Developers run Ollama locally (`http://localhost:11434/v1`), which exposes an OpenAI-compatible API format.
* The `nomic-embed-text` model outputs 512-dimension Matryoshka embeddings natively matching our PostgreSQL schema.

### 3. Caching & Seeding Resilience
* **Content-Hash Caching:** `EmbeddingService` hashes `model:dimensions:content` via SHA-256. Repeated texts resolve in `0.1ms` from cache without calling the AI model.
* **JSON Fixtures:** `database/seeders/data/articles.json` stores pre-calculated 512d vectors so `php artisan migrate:fresh --seed` runs in `10ms` with zero AI calls or network dependencies.

---

## System Prerequisites & Compatibility

* **PHP:** `^8.3` (Supports **PHP 8.3, 8.4, and 8.5**; active CI/dev runtime: `8.5`)
* **Composer:** `v2.x`
* **Node.js:** `v20.x` or `v22.x`
* **Docker:** For running PostgreSQL + `pgvector` (`pgvector/pgvector:pg16`)
* **Ollama (Optional for local AI):** For free, offline vector generation (`nomic-embed-text`)

---

## Quick-Start Onboarding Runbook (5 Minutes)

### Step 1: Clone & Install Dependencies
```bash
# 1. Install PHP dependencies
composer install

# 2. Install and compile frontend assets
npm install
npm run build

# 3. Create your local environment file and generate app key
cp .env.example .env
php artisan key:generate
```

---

### Step 2: Start PostgreSQL with `pgvector` (Docker)
Start the PostgreSQL container with the `pgvector` extension pre-installed:

```bash
docker run -d \
  --name pgvector-db \
  -e POSTGRES_PASSWORD=secret \
  -e POSTGRES_DB=vectors_postgres_slow \
  -p 5432:5432 \
  pgvector/pgvector:pg16

# Wait for PostgreSQL to be ready, then provision the dedicated testing database:
until docker exec pgvector-db pg_isready -U postgres >/dev/null 2>&1; do sleep 1; done
docker exec pgvector-db psql -U postgres -tAc "SELECT 1 FROM pg_database WHERE datname = 'vectors_postgres_testing'" | grep -q 1 \
  || docker exec pgvector-db createdb -U postgres vectors_postgres_testing
```

*(If the container is already created, start it with `docker start pgvector-db`.)*

---

### Step 3: Setup Local Ollama AI Engine
Install Ollama and pull the free 512-dimension embedding model:

```bash
# 1. Install Ollama via Homebrew
brew install ollama

# 2. Start the background service
brew services start ollama

# 3. Pull the embedding model (274 MB)
ollama pull nomic-embed-text
```

---

### Step 4: Run Migrations and Seed Articles
```bash
php artisan migrate:fresh --seed
```

This runs all table migrations, enables the PostgreSQL `vector` extension, and populates the 18 trade school articles along with their pre-indexed 512d vector embeddings from [`database/seeders/data/articles.json`](../database/seeders/data/articles.json).

---

### Step 5: Verify the Application
Visit the knowledge base in your browser:
* **Index:** `http://wt-vectors-example-slow.test/articles` (or `http://localhost:8000/articles`)
* **Article & Related Recommendations:** Click any article (e.g. *"Personal Protective Equipment (PPE) Guidelines for Welding Labs"*).
* At the bottom of the article, you will see the 3 related articles automatically matched via PostgreSQL cosine distance with their similarity match badges (e.g., `81% match`).

---

## Operational CLI Commands Reference

| Command | Purpose |
| :--- | :--- |
| `php artisan articles:re-embed --sync` | Re-generates 512d AI embeddings for all articles in the database using the active provider (Ollama or OpenAI). |
| `php artisan articles:export-fixtures` | Exports all database articles and their 512d embeddings to `database/seeders/data/articles.json`. |
| `php artisan test --compact` | Runs the 32-test Pest suite verifying vector geometry, observers, and hybrid filtering. |
| `./vendor/bin/pint --format agent` | Formats PHP code to project conventions. |
| `./vendor/bin/phpstan analyse` | Runs PHPStan Level 8 static analysis. |

---

## Environment Configuration Guide (`.env`)

### Local Ollama Setup (Default)
```env
OPENAI_BASE_URL=http://localhost:11434/v1
OPENAI_API_KEY=ollama
AI_EMBEDDING_MODEL=nomic-embed-text
AI_EMBEDDING_DIMENSIONS=512
```

### Production Cloud OpenAI Setup
```env
OPENAI_BASE_URL=https://api.openai.com/v1
OPENAI_API_KEY=sk-proj-your-production-key-here
AI_EMBEDDING_MODEL=text-embedding-3-small
AI_EMBEDDING_DIMENSIONS=512
```

### Cache & Session Setup
```env
# Uses PostgreSQL cache and session tables out of the box
CACHE_STORE=database
CACHE_PREFIX=slow_lane_
SESSION_DRIVER=database
SESSION_COOKIE=slow_session
QUEUE_CONNECTION=database
```

---

## CI/CD Pipeline Configuration
GitHub Actions (`.github/workflows/tests.yml`) spins up a dedicated `pgvector/pgvector:pg16` service container on every commit and PR. All migrations, vector math, and Pest tests execute natively against real PostgreSQL pgvector instances during CI runs.
