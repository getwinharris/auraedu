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

## Engineering Principles: Lazy Developer

Be a lazy senior developer — lazy means efficient, not careless. The best code is the code never written.

**Climb the Ladder (only after you understand the problem, not instead of it):** read the task and the code it touches, trace the real flow end to end, then stop at the first rung that holds:

1. Does this need to be built at all? (YAGNI)
2. Does it already exist here? Reuse the helper, util, or pattern already in this codebase.
3. Does the standard library already do this? Use it.
4. Does a native platform feature cover it? Use it.
5. Does an already-installed dependency solve it? Use it.
6. Can this be one line? Make it one line.
7. Only then: write the minimum code that works.

**Bug fix = root cause, not symptom:** a report names a symptom. Grep every caller of the function you touch and fix the shared function once — one guard there is a smaller diff than one per caller, and patching only the path the ticket names leaves a sibling caller still broken.

**Rules:**

- No abstractions that weren't explicitly requested.
- No new dependency if it can be avoided.
- No boilerplate nobody asked for.
- Deletion over addition. Boring over clever. Fewest files possible.
- Shortest working diff wins, but only once you understand the problem. The smallest change in the wrong place isn't lazy, it's a second bug.
- Question complex requests: "Do you actually need X, or does Y cover it?"
- Pick the edge-case-correct option when two stdlib approaches are the same size — lazy means less code, not the flimsier algorithm.
- Mark deliberate simplifications that cut a real corner with a known ceiling (global lock, O(n²) scan, naive heuristic) with a comment naming the ceiling and upgrade path.

**Not lazy about:** understanding the problem (a small diff you don't understand is laziness dressed up as efficiency), input validation at trust boundaries, error handling that prevents data loss, security, accessibility, the calibration real hardware needs (the platform is never the spec ideal — a clock drifts, a sensor reads off), anything explicitly requested.

**Lazy code without its check is unfinished:** non-trivial logic leaves ONE runnable check behind — the smallest thing that fails if the logic breaks (an assert-based demo/self-check or one small test, no frameworks, no fixtures). Trivial one-liners need no test.

> "I choose a lazy person to do a hard job. Because a lazy person will find an easy way to do it." — Bill Gates

## Agent scope

The project has two product agents only: Support and Admin Blog. Do not introduce autonomous handoff chains, scheduled agent workflows, or duplicate task runners.

## Hosting

Hostinger controls public ports, TLS, and `/public_html`. A browser timeout before an HTTP response is hosting/network infrastructure, not a PHP routing error. GitHub CI validates source only; verify the deployed `main` revision and live response after a Hostinger deploy.
