---
description: Operating guide for the AuraEdu PHP/MySQL application.
globs: *
alwaysApply: true
---

# AuraEdu Agent Guide

## Application

- PHP templates in `views/`; customer UI follows `Design.md`.
- Route → controller → service → `DatabaseService` in `app/`.
- Remote MySQL is runtime storage; declare persisted fields in `storage/schema/collections.php`.
- Blog and help content is Markdown with YAML frontmatter in `content/blog/posts/`.
- Secrets belong in Admin → Integrations / MySQL `secrets`, never in Git.

## Retained agent roles

Only two application roles are supported:

- `.agents/roles/support.md` for customer support, booking, and escalation.
- `.agents/roles/admin-blog.md` for drafting and publishing admin blog/help content.

Do not add subagent handoffs, scheduled agent jobs, role routers, tool registries, or evaluation loops.

## GitHub workflow

1. Reproduce the problem and open an evidence-backed issue with `gh issue create`.
2. Branch from `main` as `issue-<number>-<summary>`.
3. Make the smallest safe change and update directly affected documentation.
4. Run PHP lint and `php tests/run.php`.
5. Push and open a pull request with `gh pr create`.
6. Merge after the PHP CI workflow passes.

For owner-approved urgent production changes, a direct commit to `main` is allowed after the same validation.

## Hosting

Hostinger controls public ports, TLS, and the `/public_html` document root. GitHub Actions validates code but cannot restart shared hosting. Verify live behaviour after Hostinger deploys `main`.

## Safety

- Never commit credentials or customer data.
- Preserve CSRF protection for web POST routes.
- Audit admin mutations.
- Keep changes within this repository unless the owner explicitly expands scope.
