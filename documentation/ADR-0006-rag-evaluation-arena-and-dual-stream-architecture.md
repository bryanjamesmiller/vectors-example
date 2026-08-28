# ADR 0006: Retrieval-Augmented Generation (RAG) Evaluation Arena & Dual-Stream Benchmark Architecture

* **Status:** Accepted (2026-08-27)
* **Author:** Architecture & Engineering Team
* **Target Stack:** PHP 8.3+ (Active runtime: PHP 8.5), Laravel 12+, Livewire 4.x / 3.x, Tailwind CSS, PostgreSQL 16 + `pgvector` (HNSW indexing), Ollama (`nomic-embed-text`, `llama3.2`) / OpenAI (`text-embedding-3-small`, `gpt-4o-mini`), Pest 5.x, PHPStan (Level 8)
* **Related Records:**
  * [ADR-0001: Multi-Tenant AI Assistant & Document RAG Architecture](./ADR-0001-vector-embeddings-and-rag-architecture.md)
  * [ADR-0002: Semantic Article Recommendations via In-Database Vector Proximity](./ADR-0002-article-semantic-recommendations-via-vector-proximity.md)
  * [ADR-0003: Environment Setup, Local Onboarding, and Vector Operations](./ADR-0003-environment-setup-and-local-onboarding.md)
  * [ADR-0004: Interactive AI Vector Lab & In-Database Persistence](./ADR-0004-interactive-ai-vector-lab-and-in-database-persistence.md)

---

## 1. Context & Problem Statement

### 1.1 The Evaluation Challenge
In enterprise and vocational software, Retrieval-Augmented Generation (RAG) is frequently claimed as an essential architecture for preventing hallucinations and grounding AI responses in authoritative internal records. However, demonstrating the tangible business and engineering value of RAG poses a distinct challenge:
* **The "Black Box" Perception:** When users interact with a typical single-window AI chatbot, they cannot easily tell whether an accurate answer came from in-database retrieval or from the model's pre-trained parametric weights.
* **Invisible Hallucinations:** When a raw LLM invents plausible-sounding trade policies, tuition fee schedules, or safety protocols, non-expert users cannot detect the hallucination without tedious manual fact-checking.
* **Lack of Direct Comparative Proof:** Without seeing an ungrounded baseline side-by-side on the exact same question, stakeholders and technical evaluators cannot measure the impact of vector search, grounding thresholds, and source citation fidelity.

### 1.2 The Solution
We architect an interactive **Side-by-Side RAG Evaluation Arena** (`/rag`) that dispatches every user question into **two parallel, isolated pipelines** executed against the exact same underlying LLM (e.g. `llama3.2` locally via Ollama or `gpt-4o-mini` in cloud):
1. **🟢 Left Pipeline — Grounded RAG:** Queries PostgreSQL 16 using `pgvector` HNSW cosine distance (`<=>`), verifies a strict cosine similarity cutoff ($\ge 75\%$), injects retrieved article snippets into the prompt, and streams verified answers with inline source citations.
2. **⚪ Right Pipeline — Raw LLM Baseline:** Queries the exact same model relying solely on pre-trained parametric memory (zero database context), exposing generic advice, omissions, or factual drift.

---

## 2. Dual-Pipeline System Architecture

```
                                  ┌───────────────────────────┐
                                  │      User Chat Input      │
                                  │   (e.g., "Welding PPE")   │
                                  └─────────────┬─────────────┘
                                                │
                                  ┌─────────────▼─────────────┐
                                  │      RagChat Livewire     │
                                  │         Component         │
                                  └──────┬─────────────┬──────┘
                                         │             │
                    ┌────────────────────┘             └────────────────────┐
                    ▼                                                       ▼
      ┌───────────────────────────┐                           ┌───────────────────────────┐
      │   Grounded RAG Pipeline   │                           │    Raw Baseline Pipeline  │
      └─────────────┬─────────────┘                           └─────────────┬─────────────┘
                    │                                                       │
      ┌─────────────▼─────────────┐                           ┌─────────────▼─────────────┐
      │  EmbeddingService Vector  │                           │  buildRawSystemPrompt()   │
      │  (nomic-embed-text 512d)  │                           │    (Zero DB Context)      │
      └─────────────┬─────────────┘                           └─────────────┬─────────────┘
                    │                                                       │
      ┌─────────────▼─────────────┐                                         │
      │  PostgreSQL (pgvector)    │                                         │
      │  WHERE 1 - (embedding <=> │                                         │
      │  :q) >= 0.75 LIMIT 3      │                                         │
      └─────────────┬─────────────┘                                         │
                    │                                                       │
           Matched? │                                                       │
         ┌──────────┴──────────┐                                            │
      Yes│                   No│                                            │
         ▼                     ▼                                            │
┌──────────────────┐  ┌──────────────────┐                                  │
│ Ingest Articles  │  │ Refuse / Bypass  │                                  │
│ & Build System   │  │ ("Not in school  │                                  │
│ Prompt Context   │  │  knowledgebase") │                                  │
└────────┬─────────┘  └────────┬─────────┘                                  │
         │                     │                                            │
         │ Stream Answer       │ Set Refusal                                │ Stream Baseline
         ▼                     ▼                                            ▼
┌─────────────────────────────────┐                       ┌─────────────────────────────────┐
│   wire:stream="rag-response"    │                       │   wire:stream="raw-response"    │
│  - In-Database Grounded Badge   │                       │  - Parametric Memory Only Badge │
│  - Cosine Distance & Latency    │                       │  - Unassisted Warning Banner    │
│  - Collapsible Source Drawer    │                       │  - Raw Prompt Inspector         │
└─────────────────────────────────┘                       └─────────────────────────────────┘
```

---

## 3. Core Architectural Mechanisms

### 3.1 Strict Grounding & Hallucination Prevention
To prevent hallucinations on out-of-domain queries (e.g. asking for chocolate cake recipes or medical diagnoses):
* Cosine distance between the query vector $u$ and document vector $v$ is computed in PostgreSQL via `$similarity = 1 - ($distance)`.
* A hard minimum similarity threshold of **$0.75$** (cosine distance $\le 0.25$) is enforced.
* If no articles exceed this threshold, the RAG pipeline **bypasses the LLM completely**, returning a clear refusal statement. This saves API costs and guarantees zero hallucination for unverified topics.

### 3.2 Context Isolation Across Turns
Because users converse over multiple turns, conversation history must be preserved. However, if the Raw Baseline had access to previous RAG assistant responses (which contain retrieved database text), database knowledge would leak into the baseline stream.
* **Separated Histories:** The `RagChat` component builds two distinct conversation arrays:
  * `$ragHistory`: Contains user questions and Grounded RAG assistant answers.
  * `$rawHistory`: Contains user questions and Raw Baseline assistant answers (`$msg['raw_details']['content']`).
* This guarantees the baseline model remains strictly unassisted across all conversational turns.

### 3.3 Multi-Turn Pronoun Resolution & Cumulative Telemetry
When a user asks a pronoun follow-up (e.g., Turn 1: *"Tell me about hyperbaric welding"* $\rightarrow$ Turn 2: *"What are its prerequisites?"*), an isolated pronoun query will fail the cosine threshold against the database.
* **Contextual Retry:** The system detects ungrounded queries on turn $> 1$, concatenates the previous user question (`"Tell me about hyperbaric welding — What are its prerequisites?"`), and re-executes the vector search.
* **Cumulative Latency Accounting:** Telemetry captures the true duration of both operations:
  $$\text{latency\_ms} = \text{latency}_{\text{initial}} + \text{latency}_{\text{retry}}$$
* The grounded result is only updated if the retry passes the grounding threshold (`if ($retryRetrieval['grounded'])`), preventing degradation.

### 3.4 Stream Reliability & Failure Fallbacks
* If an upstream provider completes a stream without yielding tokens (e.g. content moderation trigger or token cutoff), the component checks `if (trim($content) === '')` and throws a `\RuntimeException`.
* The `catch (Throwable)` handler automatically sets `has_error = true`, resets `grounded = false`, and notifies the user via Flux UI toasts.

---

## 4. Telemetry & Observability Matrix

Every conversation turn records structured telemetry stored in the Livewire message state:

| Telemetry Property | Grounded RAG Perspective | Raw Baseline Perspective |
| :--- | :--- | :--- |
| **Grounding Status** | `true` (if verified articles retrieved & streamed) | `false` (always ungrounded) |
| **Injected Articles** | List of matched models: ID, title, slug, audience, cosine distance, match percentage | Empty array (`[]`) |
| **Latency Tracking** | `latency_ms` for vector retrieval + generation | `latency_ms` for generation only |
| **System Prompt** | Inspected via collapsible drawer containing injected Markdown sources | Inspected via collapsible drawer containing baseline system prompt |
| **Model** | Active model identifier (`llama3.2` or `gpt-4o-mini`) | Identical active model identifier |

---

## 5. Verification & Testing Standards

1. **Automated Dual-Stream Mocks:** Feature tests in `tests/Feature/RagChatTest.php` queue pairs of streamed responses using `OpenAI::fake()`, validating that both streams resolve accurately.
2. **Contextual History Verification:** Tests verify pronoun questions trigger contextual retry and yield grounded answers with accumulated latency.
3. **Empty Stream Fallback:** Dedicated test proves empty streams are caught and flagged with `has_error: true`.
4. **Rate Limiting:** Enforced via Laravel `RateLimiter` allowing up to 20 messages per minute per IP address.
5. **Static Analysis & Formatting:** Strict PHPStan Level 8 conformance with zero errors, and code formatting enforced via Laravel Pint.
