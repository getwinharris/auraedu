---
description: CTO orchestration: spawns sub-agents via bapXaura handoff next, gates merge.
---

# CTO Workflow

Hierarchy: `AGENTS.md` (root) → this file → sub-agent contracts

## Read First

`bapXaura map` — read the full project map before any decision.
`bapXaura schema list` — verify collection state before scope definition.

## Responsibility

The root agent IS the CTO. Owns: issue scope, objective IDs, sub-agent
assignment, result integration, final acceptance, merge approval,
live verification, issue closure. Never implements — that is the
Worker's job.

## Sub-Agent Trigger Chain

Do NOT give blind prompts. Use `bapXaura handoff next <issue>` to
determine what role should act and which objective to start.

1. Run `bapXaura handoff next <issue>` → read JSON output
2. If `status: "ready"` → spawn `next_role` with `next_objective`
3. If `status: "complete"` → issue is done, close it
4. If no matching handoff exists → `next_role`: `worker` with first
   objective — spawn Worker

Sub-agent prompt template:

```
Role: <worker|reviewer|browser-tester>
Issue: #<N> — <title>
Objective: <ID> — <description>
Read: AGENTS.md + .agents/workflows/<role>.md
Pre-flight: bapXaura map && bapXaura schema list
Allowed tools: <list>
Owned paths: <list>
Handoff: write <file> matching handoff.schema.json
```

## Gates

1. Issue objectives exist before implementation
2. Worker returns schema-valid handoff with per-objective evidence
3. `bapXaura ci` + PR validation pass
4. Reviewer independently reports pass|gap|blocked for every objective
5. CTO dispositions CodeRabbit findings; Browser Tester checks UI
6. Merge → deployment → fork SHA verification → issue closure

Tests passing does NOT prove issue completion. Compare issue, diff,
rendered behavior, Worker evidence, and Reviewer findings.

## Handoff

`.agents/workflows/handoff.schema.json` governs all handoffs.
Active artifacts: `.agents/handoffs/<issue>-worker.json` and
`.agents/handoffs/<issue>-reviewer.json`.

## Next-Role Commands

- `bapXaura handoff next <issue>` — print JSON with next role/objective
- `bapXaura handoff template <issue>` — generate empty Worker handoff
- `bapXaura handoff validate <file> [--issue]` — validate + coverage
- GitHub Actions publishes validated handoff evidence to the matching issue or PR
