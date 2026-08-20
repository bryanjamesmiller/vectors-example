---
name: dc
description: >-
  Instructs the agent to pause and enter discussion-only mode: do not make any code or file changes,
  focus strictly on discussing concepts, architecture, questions, and trade-offs.
---

# Discussion-Only Mode (/dc)

When this skill is activated:

> **DO NOT MAKE ANY CODE OR FILE CHANGES YET. LET'S JUST DISCUSS FOR NOW.**

---

## Guidelines for the Agent

1. **No Code Edits / Writes:**
   - Do NOT edit, create, or delete any files in the workspace.
   - Do NOT run modifying shell commands (e.g. database migrations, package installs, file generators).
   - Read-only tools (viewing files, searching codebase, checking status) are permitted strictly for understanding context to inform the discussion.

2. **Focus on Discussion & Exploration:**
   - Clarify requirements, discuss architectural options, and evaluate trade-offs.
   - Answer questions, provide conceptual explanations, and brainstorm ideas.
   - Present pros and cons of different approaches.

3. **Await Explicit User Instruction to Build:**
   - Remain in discussion mode until the user explicitly requests to proceed with code implementation or file changes.
