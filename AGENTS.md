---
description: Working contract for AuraEdu contributors and coding agents.
globs: '*'
alwaysApply: true
---

# AuraEdu

## Architecture

- PHP templates live in `views/`; use `Design.md` for customer-facing UI.
- Keep backend changes on the route → controller → service → `DatabaseService` path in `app/`.
- `storage/schema/collections.php` declares persisted data. Remote MySQL is runtime storage; `storage/data/` is import-only.
- Blog/help content is Markdown with YAML frontmatter in `content/blog/posts/`.
- Secrets are managed through Admin → Integrations and must never be committed.

## Map and tests

- `map.mmd` is the repository-wide file index. Regenerate it with `php cli/generate-code-map.php` after adding, moving, or removing files.
- Use `php cli/generate-code-map.php --check`, PHP lint, and `php tests/run.php` before opening a pull request.
- Inspect the source path that owns a behaviour before changing it; do not create parallel controllers, services, or templates.

## GitHub workflow

1. Reproduce meaningful defects and open an evidence-backed issue with `gh issue create`.
2. Create `issue-<number>-<summary>` from `main`.
3. Implement the smallest complete change, regenerate `map.mmd`, and update durable docs.
4. Run validation, push, and open a PR with `gh pr create`.
5. Merge after PHP CI passes.

## Agent scope

The project has two product agents only: Support and Admin Blog. Do not introduce autonomous handoff chains, scheduled agent workflows, or duplicate task runners.

## Hosting

Hostinger controls public ports, TLS, and `/public_html`. A browser timeout before an HTTP response is hosting/network infrastructure, not a PHP routing error. GitHub CI validates source only; verify the deployed `main` revision and live response after a Hostinger deploy.
