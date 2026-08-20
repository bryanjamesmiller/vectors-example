# ADR 0002: Semantic Article Recommendations via In-Database Vector Proximity

* **Status:** Approved
* **Date:** 2026-08-19
* **Author:** Architecture & Engineering Team
* **Target Stack:** PHP 8.3+ (Active runtime: PHP 8.5), Laravel 12+, PostgreSQL with `pgvector` (512 dimensions), Blade / Livewire, Pest 5.x, PHPStan (Level 8)

---

## 1. Context & Problem Statement

### 1.1 The Business Need
The Trade School platform hosts generic articles and guides that apply across all schools, serving six distinct audiences:
1. **Prospective Student Recruits** (admissions, program comparisons, career outlooks)
2. **Current Students** (lab safety, financial aid, workshop access, tutoring)
3. **Alumni** (job placement, journeyman licensing, alumni network)
4. **Teaching Assistants (TAs)** (lab equipment management, grading assistance)
5. **Teachers / Instructors** (curriculum planning, apprentice evaluations)
6. **School Administrators** (compliance guidelines, accreditation, retention)

When a user reads an article, the bottom of that page must automatically recommend **3 highly relevant related articles** that share conceptual, topical, and audience relevance.

### 1.2 The Problem with Traditional Approaches
* **Manual Tagging:** Requires authors to manually cross-link articles or maintain rigid taxonomies of tags. When new articles are added, older articles are never updated with new links.
* **Lexical Keyword Search (SQL `LIKE` or Full-Text):** Only matches exact shared keywords. An article titled *"Scholarships for High Voltage Electrical Students"* will fail to recommend an article titled *"Applying for Tool Grant Waivers"* because they share zero keywords, despite being topically and contextually identical.

---

## 2. Decision: In-Database Vector Proximity Recommendations

We implement a native, lightweight vector recommendation engine built directly into the `articles` table using PostgreSQL and `pgvector`:

1. **Zero-LLM Read Latency:** Recommending related articles requires **no prompt engineering and zero external LLM completion API calls at runtime**. 
2. **Single-Write Ingestion:** The article's semantic embedding vector (512 dimensions) is generated once when the article is created or updated.
3. **Sub-Millisecond Nearest-Neighbor Query:** When viewing an article, PostgreSQL's HNSW index calculates the cosine distance (`<=>`) against all other published articles and retrieves the Top-3 nearest neighbors in $<2\text{ms}$.

```
                                  [ Current Article ]
                     "Applying for Trade Tool Grants & Waivers"
                                          │
                                          │ pgvector: embedding <=> current.embedding
                     ┌────────────────────┼────────────────────┐
                     ▼                    ▼                    ▼
             [ Related #1 ]       [ Related #2 ]       [ Related #3 ]
       "Veteran Financial Aid"  "Lab Safety Gear"   "Electrical Grants"
          (94% similarity)      (88% similarity)     (85% similarity)
```

---

## 3. Schema & Model Design

### 3.1 `Audience` Enum (`app/Enums/Audience.php`)
```php
namespace App\Enums;

enum Audience: string
{
    case Recruits = 'recruits';
    case Students = 'students';
    case Alumni = 'alumni';
    case TeachingAssistants = 'teaching_assistants';
    case Teachers = 'teachers';
    case Administrators = 'administrators';
}
```

### 3.2 `articles` Table Schema
```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->string('audience'); // App\Enums\Audience
    $table->text('summary')->nullable();
    $table->longText('content');
    $table->boolean('is_published')->default(true)->index();
    
    // 512-dimension vector column
    $table->vector('embedding', 512)->nullable();
    
    $table->timestamps();
});

// HNSW Cosine Distance Index
DB::statement('CREATE INDEX articles_embedding_hnsw_idx ON articles USING hnsw (embedding vector_cosine_ops)');
```

---

## 4. Query & Recommendation Logic

In the `Article` model, retrieving the Top-3 related articles is an Eloquent query using pgvector's cosine distance operator (`<=>`):

```php
public function relatedArticles(int $limit = 3): Collection
{
    if (empty($this->embedding)) {
        return new Collection();
    }

    return Article::query()
        ->where('id', '!=', $this->id)
        ->where('is_published', true)
        ->whereNotNull('embedding')
        ->orderByRaw('embedding <=> ?', [new Vector($this->embedding)])
        ->limit($limit)
        ->get();
}
```

---

## 5. Verification & Testing Plan

1. **Unit Tests:** Verify `Audience` enum cases and `Article` attribute casting.
2. **Feature Tests (`Pest`):**
   - Seed diverse articles across Welding, Electrical, Safety, and Admin topics.
   - Verify that an Electrical Grant article automatically recommends Electrical and Tuition articles as the Top-3 nearest neighbors.
   - Verify that the current article is excluded from its own related list (`id != $this->id`).
   - Verify that unpublished articles (`is_published = false`) are excluded.
3. **Formatting & Static Analysis:** Ensure 100% pass rate with Laravel Pint and PHPStan Level 8.
