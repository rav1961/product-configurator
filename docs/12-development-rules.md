# Development Rules

No Fat Controllers. No God Services. Use DTOs and Actions.

## Assistant delivery model

* Application code (`backend/`, `frontend/`) is delivered as **reviewable, copy-paste-ready** output
  after plan approval — not auto-edited by the assistant.
* The assistant may **auto-edit only** `ai/` and `docs/` (rules, architecture, `STATUS.md`,
  decision logs).
* Patching or committing `backend/` / `frontend/` requires an **explicit** user request.
* Slice workflow: assumptions → plan → approval → code (manual apply) → `composer check`.
