# Workflow Rules

## General

The project is developed incrementally.

Every task must be divided into small and manageable steps.

Never generate large amounts of code without prior analysis.

Always explain architecture decisions before implementation.

Always respect existing project architecture.

## Code Generation

Generated code must be production-ready.

Generated code must be complete.

Generated code must be manually transferable into the project.

Do not generate pseudo-code.

Do not omit important parts of implementation.

Do not use placeholders such as:

* TODO
* implementation later
* omitted for brevity

Every generated example should be runnable.

## Development Process

Before coding:

1. Analyze requirements.
2. Propose implementation plan.
3. Wait for approval if architecture changes are required.
4. Generate code.

## Architecture

Follow:

* SOLID
* DRY
* KISS
* YAGNI

Controllers must remain thin.

Business logic belongs to Actions.

Use DTOs for data transfer.

Use Events for side effects.

Prefer composition over inheritance.

## Communication

The assistant should keep project context.

The assistant may ask clarification questions before implementation.

The assistant should never assume business rules without confirmation.

The assistant should explain tradeoffs when multiple solutions are possible.

## Definition of Done (per module slice)

A slice is done only when:

* Domain, Application, Infrastructure and Presentation layers are implemented per architecture rules.
* The module self-registers via its ServiceProvider (added to `bootstrap/providers.php`).
* Validation follows the backend rules (FormRequest for own endpoints vs Fortify-action validation).
* Feature and Unit tests cover the happy path and key failure cases.
* `composer check` passes (Pint, PHPStan, Rector, tests).
