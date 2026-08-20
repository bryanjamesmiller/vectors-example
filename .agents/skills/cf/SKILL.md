---
name: cf
description: >-
  Critically analyzes and triages GitHub Copilot pull request feedback: parses suggestions from prompt text or PR comments,
  checks actual codebase context to identify false positives, rollback traps on incomplete features, or valid issues, provides deep rationale, and outputs a concise decision table.
---

# Copilot Feedback Analyzer (/cf)

Use this skill whenever the user triggers `/cf` or asks to analyze, triage, or review GitHub Copilot comments/feedback on code or pull requests.

---

## 🎯 Primary Purpose

GitHub Copilot automated reviews frequently generate a mix of valuable catches, harmless style nitpicks, over-engineered suggestions, and outright false positives due to incomplete repository context or architectural misunderstandings.

Critically:
1. **Incomplete Changes vs. Rollback Traps:** Copilot often seeks to reverse or roll back a change simply because it detects that the change was left incomplete, partial, or inconsistent with the rest of the codebase (e.g., a heading changed in a document without updating every downstream section, or an enum/method updated in one place but not across all callers). Undoing the change is often a step backward if the change was the intended forward direction. In these situations, `/cf` frames the choice as **Complete or Roll Back**, identifying the missing follow-through steps needed to complete the change and asking the user to confirm their preferred scope.
2. **Handling Uncertainty:** When feedback touches on undocumented domain logic, ambiguous requirements, or situations where codebase evidence is insufficient to make a confident recommendation, `/cf` explicitly classifies the item as **User Analysis Required** instead of making unfounded assumptions.

When `/cf` is invoked:
1. **Extract & Parse Feedback:** Extract all feedback items from the text provided with or after `/cf` (or fetch from the active PR if requested).
2. **Deep Codebase & Context Verification:** Inspect the actual files, types, configurations, frameworks, and tests referenced in the feedback before making any determination. Never take Copilot's assumptions at face value.
3. **Critical Assessment & Incompleteness Detection:**
   - Determine whether each item is **Worth Implementing**, **False Positive**, **Over-Engineering**, **Harmful / Regression Risk**, an **Incomplete Change (Complete or Roll Back)**, or **Uncertain (User Analysis Required)**.
   - Check if Copilot is recommending undoing something that was the desired forward direction simply because it was partially finished.
4. **Scope & Intent Disambiguation:** When an incomplete change carries cascading consequences or when domain certainty is lacking, clearly lay out the trade-offs and proactively ask the user for direction.
5. **Structured Output:** Present an item-by-item technical breakdown followed by a compact **Executive Decision Summary** at the end.

---

## 🔍 Evaluation Criteria

For each feedback item, evaluate:
1. **Correctness & Accuracy:** Is Copilot's assertion factually correct in this specific framework, PHP/JS version, and environment?
2. **Actual Codebase Context:** Does existing logic (middleware, models, DB constraints, upstream validation, caching, queue workers) already handle what Copilot claims is missing?
3. **Forward Intent vs. Rollback Trap (Incompleteness Detection):** Is Copilot recommending undoing, reverting, or restoring legacy state simply because a change was only partially applied?
   - Determine whether the partial change was the intended forward direction.
   - If forward progress is intended, do NOT blindly roll back to match the old state. Instead, identify the specific missing follow-through steps required across the codebase or documentation to complete the feature cleanly, and present the choice (**Complete or Roll Back**) to the user.
4. **Certainty & Domain Ambiguity:** Does the item involve business rules, architectural trade-offs, or user intent that cannot be verified from code alone? If there is general uncertainty, classify as **User Analysis Required** with specific clarifying questions.
5. **Scope & Cascading Consequences:** Does completing an incomplete feature entail broader scope (e.g., migrations, deprecations, rewriting related tests, synchronizing multiple docs) that the author may not have anticipated?
6. **Value vs. Complexity:** Does the suggested change meaningfully improve security, reliability, performance, or clarity—or does it add unnecessary boilerplate / premature optimization?
7. **Trade-Offs & Potential Regressions:** Could applying Copilot's change introduce subtle bugs, break existing tests, violate project conventions, or degrade UX?

---

## 📋 Required Output Format

Structure the response with the following format:

### 1. Item-by-Item Critical Breakdown

For each item:

#### **Item N: [Short Descriptive Title]**
* **File & Target:** `path/to/file.php` (Line numbers / Symbol)
* **Copilot's Suggestion:** Summary of what Copilot recommended and why.
* **Context & Reality Check:** What the codebase actually does, the PR's intended direction, and how the surrounding architecture works.
* **Verdict:**
  * 🟢 **Fix / Implement:** Legitimate issue, bug, security hole, missing test, or valuable optimization.
  * 🔴 **Reject / Ignore:** False positive, misunderstanding of framework/context, or harmful change.
  * 🟡 **Modify / Alternative:** Valid underlying concern, but Copilot's proposed fix is suboptimal, over-engineered, or misdirected.
  * 🔵 **Complete or Roll Back:** An incomplete change or refactor was detected. Outline the remaining steps to finish the change forward vs. reverting to the previous state, and ask the user to decide.
  * ❓ **User Analysis Required:** Genuine uncertainty regarding Copilot's comment, ambiguous domain requirements, or inability to draw a definitive conclusion without user input.
* **Rationale:** Clear technical explanation of why this verdict was reached.
* **Follow-Through Requirements / User Decision (if applicable):**
  * If **Complete or Roll Back**: Detail **Option A (Complete Forward & Full Scope)** vs. **Option B (Revert / Rollback)** and ask the user to confirm their intent.
  * If **User Analysis Required**: State the specific open questions or missing context needed from the user.

---

### 2. Executive Decision Summary

Provide a clean markdown table summarizing all items at the end:

| # | Item Summary | Severity / Category | Verdict | Recommended Action |
| :-: | :--- | :--- | :-: | :--- |
| **1** | [Short Title] | Security / Logic / Test Gap | 🟢 Fix | [1-sentence actionable conclusion] |
| **2** | [Short Title] | False Positive / Framework Mismatch | 🔴 Reject | [1-sentence actionable conclusion] |
| **3** | [Short Title] | Suboptimal Solution / Refactor | 🟡 Modify | [1-sentence actionable conclusion] |
| **4** | [Short Title] | Incomplete Feature / Scope Decision | 🔵 Complete or Roll Back | [1-sentence conclusion + choice for user] |
| **5** | [Short Title] | Ambiguous Intent / Domain Logic | ❓ User Analysis Required | [1-sentence explanation + question for user] |

---

## ⚡ Mode Rule

Unless the user explicitly asks to apply the fixes right away, `/cf` operates in **triage & recommendation mode** first, allowing the user to approve the action plan or answer scope/uncertainty questions before making code modifications.
