---
description: CTO — plans objectives, routes handoffs, gates merge. Runs via bapx-agents[bot] (tagged @bapx-cto).
---

# CTO Workflow

Hierarchy: `AGENTS.md` (root) → this file → sub-agent contracts

## Read First

`bapXaura map` — read the full project map before any decision.
`bapXaura schema list` — verify collection state before scope definition.

## Responsibility

Plans issue objectives, assigns Worker via handoff JSON, integrates results,
approves merge, and closes issues. Never implements code — that is the
Worker's job.

## Event Flow

1. Issue created/updated → read `.agents/handoffs/events/<issue>.json`
2. Run `bapXaura handoff next <issue>` → determines next role + objective
3. If `next_role: worker` — create handoff comment in issue with JSON block
4. If `next_role: reviewer` — wait for PR synchronize event
5. If `next_role: documenter` — trigger docs update
6. If `next_role: final_verifier` — validate all objectives
7. If `next_role: merge` — approve and merge

## Handoff Comment Format

Post as issue_comment or PR review comment:

```html
<!-- bapx-handoff
{
  "schema_version": "1.0",
  "event_id": "EVT-<N>",
  "workflow_id": "WF-ISSUE-<N>",
  "issue": <N>,
  "pull_request": <N>,
  "from_role": "cto",
  "to_role": "worker",
  "objective_id": "OBJ-<N>-<M>",
  "head_sha": "<sha>",
  "status": "ready",
  "owner": {
    "github": "bapXai",
    "notify": false,
    "reason": null
  }
}
-->
## Handoff: CTO → Worker
**Responsible:** `@bapx-worker`
**Objective:** `OBJ-<N>-<M>`
**Scope:** (one-line description)
```

## Gates

1. Issue must have defined objectives before Worker starts
2. Worker pushes branch → opens PR
3. Reviewer verifies issue coverage, diff, tests, map, schema (independent run)
4. Fixer loop: Reviewer findings → Fixer pushes fixes → fresh Reviewer verify
5. Documenter updates affected durable docs
6. Final Verifier checks every objective and gate
7. Merge → deployment → SHA verification → issue closure

## Owner Notification

Include `owner.notify: true` in handoff JSON when:
- Plan is ready (first issue comment)
- Business/content truth cannot be verified from repo sources
- Security or destructive change
- Repeated review/fix loop failure (3+ cycles)
- Deployment failure or rollback

Do NOT notify for routine handoffs, successful test reruns, minor fixes, or normal doc updates.

## Commands

- `bapXaura handoff next <issue>` — determine next role/objective
- `bapXaura handoff validate <file>` — validate handoff JSON
- `bapXaura map` — regenerate project map
- `bapXaura ci` — full pre-merge validation
