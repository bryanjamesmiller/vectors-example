---
name: ship
description: >-
  Runs formatters (Pint), linters/static analysis (Larastan/PHPStan), and Pest tests,
  auto-fixes any detected errors, verifies work, stages and commits code, and creates or
  updates a GitHub Pull Request with a structured technical summary and proof.
---

# Ship Workflow Runbook

Execute the following phases sequentially. If any step fails and cannot be automatically resolved after 3 attempts, halt and report to the user.

---

## Phase 0: Pre-Flight Safety Checks

1. **Branch Guardrail:** Verify the current branch is not `main` or `master`:
   ```bash
   git branch --show-current
   ```
   *If on `main`/`master`, stop immediately and ask the user for a feature branch name.*

2. **Debug / Leak Scanner:** Check for stray debug statements or exposed secrets:
   ```bash
   git diff | grep -E "(dd\(|dump\(|ray\(|console\.log\(|var_dump\(|OPENAI_API_KEY=sk-)"
   ```
   *Remove any stray debug calls before proceeding.*

3. **Environment Sync:** If new `.env` variables were introduced, ensure they are documented in `.env.example`.

4. **Frontend Asset Check:** If frontend files (`resources/js`, `resources/css`, `resources/views`) were modified, verify assets build cleanly:
   ```bash
   npm run build
   ```

---

## Phase 1: Formatters, Static Analysis & Tests (Self-Healing Loop)

Run the verification pipeline in order with an automatic fix loop (up to 3 retry iterations):

### Step 1: Code Formatter (Laravel Pint)
```bash
./vendor/bin/pint --format agent
```
*If unformatted files are detected, let Pint format them automatically.*

### Step 2: Static Analysis (PHPStan / Larastan)
```bash
./vendor/bin/phpstan analyse --level=8
```
*If PHPStan reports errors:*
- Inspect the offending lines, type hints, nullability, or missing PHPDoc blocks.
- Apply the fix.
- Re-run PHPStan.

### Step 3: Automated Pest Test Suite
```bash
php artisan test --compact
```
*If any tests fail:*
- Diagnose failure causes (assertions, exceptions, unhandled states).
- Fix the implementation or update outdated test expectations.
- Re-run `php artisan test --compact`.

### Phase 1 Guardrails:
- **If fixed automatically:** Keep track of what was broken and how it was fixed to include in the final report.
- **If unfixable after 3 attempts:** Stop immediately, do not commit or push, and present the user with:
  1. The exact failed test or static analysis output.
  2. The attempted fixes and why they did not resolve the issue.

---

## Phase 2: Git Stage & Commit

1. **Review Changed Files:**
   ```bash
   git status --short
   git diff --stat
   ```

2. **Stage All Changes:**
   ```bash
   git add -A
   ```

3. **Commit with Conventional Message:**
   Generate an accurate conventional commit message summarizing the work:
   ```bash
   git commit -m "<type>(<scope>): <concise summary of work>"
   ```

---

## Phase 3: GitHub PR Creation / Update

1. **Push Branch to Remote:**
   ```bash
   git push -u origin $(git branch --show-current)
   ```

2. **Check for Existing PR:**
   ```bash
   gh pr view --json number,url,title,state
   ```

3. **If NO existing PR exists, create one via GitHub CLI:**
   ```bash
   gh pr create --title "<type>(<scope>): <Title>" --body "$(cat << 'EOF'
   ## 1. Feature / Bug Summary
   <!-- Explain the core problem or business goal this branch solves -->

   ## 2. Technical Explanation & Code Changes
   <!-- Detailed breakdown of what files were changed, new models/services created, and architectural decisions -->

   ## 3. Proof & Verification Plan
   <!-- Step-by-step instructions and automated test evidence proving the changes work -->
   - [x] `./vendor/bin/pint` passed with zero formatting issues.
   - [x] `./vendor/bin/phpstan analyse --level=8` passed with 0 errors.
   - [x] `php artisan test` passed with all assertions green.

   ### Test Results:
   ```
   <Paste summary of test output here>
   ```
   EOF
   )"
   ```

4. **If a PR ALREADY exists, update it:**
   - Append a comment on the PR detailing the new commit, what changed, and the latest passing test results:
     ```bash
     gh pr comment --body "Pushed new updates. Verified Pint formatting, PHPStan Level 8, and Pest test suite."
     ```

---

## Phase 4: Final Summary Report to User

Output a concise summary in chat containing:
- Commit hash & commit message.
- Summary of auto-fixes applied (if any).
- Direct clickable link to the GitHub Pull Request.
