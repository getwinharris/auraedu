---
type: skill
name: agent-evals
description: Skill evaluation framework, self-evolving agent loop, multi-agent SDLC orchestration, and tool-use optimization for automated quality gates.
version: "1"
---

# Agent Evaluation & Self-Evolution System

## Sources Researched

| Source | Pattern | Key Insight |
|--------|---------|-------------|
| LangChain Eval (Mar 2026) | Task → skill → compare | Define task, run without skill, run with skill, compare delta |
| Minko Gechev Skill Eval | evals.json assertions | Judge model scores output; pass@k for reliability |
| Future AGI (6 Dimensions) | Tool/Argument/Result/Recovery/Plan/Completion | Multi-dimensional scoring prevents gaming |
| Anthropic skill-creator | Trigger vs instruction skills | Skill design patterns for distinct use cases |
| GenericAgent | 5-layer memory + crystallization | From explore → execute → crystallize → reuse |
| Self-Evolving Agents Survey | Trace → Score → Optimize → Gate → Rollout | Continuous improvement loop with guardrails |
| Gem Team (16 agents) | PM → Architect → Dev → Reviewer → Security → ... | Spec-driven multi-agent SDLC |
| VALORA | Phased governance with verifiers | Each phase has entry/exit criteria |
| Zylos Tool Optimization | Tool RAG + trajectory pruning | Reduce cost with dynamic tool selection |
| Google ADK | Sequential/Parallel/Loop agents | Built-in orchestration patterns |
| Google Scaling Paper | 17.2x error amplification (independent) vs 4.4x (coordinated) | Always coordinate — never fully independent agents |

## Architecture Overview

```
evals.json (skill definitions + test cases)
       │
       ▼
RecordEvidence() ← Agent traces tool calls + outputs
       │
       ▼
ScoreEval() ← Judge model scores each dimension
       │
       ▼
CrystallizeSkill() ← Extract reusable pattern from successful trace
       │
       ▼
Commit to .agents/skills/ ← CI gate blocks if score < threshold
```

## 1. evals.json Schema

File location: `.agents/evals/evals.json`

```json
{
  "schema_version": 2,
  "last_updated": "2026-07-17T00:00:00Z",
  "judge_model": "gemini-3.1-pro",
  "skills": [
    {
      "name": "admin-ui-css",
      "path": ".agents/skills/admin-ui",
      "version": "1",
      "trigger": "when editing admin CSS, views, or sidebar navigation",
      "evals": ["eval-1", "eval-2"]
    }
  ],
  "evals": [
    {
      "id": "eval-1",
      "name": "admin-css-class-coverage",
      "description": "All CSS classes referenced in admin views exist in admin.css",
      "type": "assertion",
      "task": "Edit views/layouts/admin.php to add a new sidebar section",
      "dimensions": {
        "tool_selection": { "weight": 0.1 },
        "argument_extraction": { "weight": 0.15 },
        "result_utilization": { "weight": 0.2 },
        "error_recovery": { "weight": 0.15 },
        "plan_coherence": { "weight": 0.2 },
        "task_completion": { "weight": 0.2 }
      },
      "assertions": [
        "Every class in views/*.php exists in assets/css/admin.css",
        "No var(--color-gold) in any admin view",
        "SVG icons replace Unicode characters in sidebar",
        "No inline style attributes in admin.php sidebar"
      ],
      "threshold": 0.85,
      "pass_count": 3,
      "trial_count": 3
    }
  ],
  "scores": [
    {
      "eval_id": "eval-1",
      "run_id": "run-001",
      "timestamp": "2026-07-17T00:00:00Z",
      "dimension_scores": {
        "tool_selection": 0.9,
        "argument_extraction": 0.85,
        "result_utilization": 0.95,
        "error_recovery": 0.7,
        "plan_coherence": 1.0,
        "task_completion": 1.0
      },
      "weighted_total": 0.91,
      "assertions_passed": 4,
      "assertions_total": 4,
      "passed": true,
      "model_used": "gemini-2.5-flash",
      "duration_ms": 45000
    }
  ],
  "comparisons": [
    {
      "skill_path": ".agents/skills/admin-ui",
      "eval_id": "eval-1",
      "without_skill": { "score": 0.65, "pass_count": 1, "trial_count": 3 },
      "with_skill": { "score": 0.91, "pass_count": 3, "trial_count": 3 },
      "delta": { "score": 0.26, "pass_rate": 0.67 },
      "timestamp": "2026-07-17T00:00:00Z"
    }
  ]
}
```

### Scoring Dimensions (Future AGI-based)

| Dimension | Weight | Measures |
|-----------|--------|----------|
| Tool Selection | 0.10 | Chooses the right tool (read vs grep, write vs edit) |
| Argument Extraction | 0.15 | Extracts correct file paths, CSS classes, parameters |
| Result Utilization | 0.20 | Uses tool output effectively (reads error, adjusts) |
| Error Recovery | 0.15 | Handles failures gracefully (retries, falls back) |
| Plan Coherence | 0.20 | Logical sequence of operations, no wasted steps |
| Task Completion | 0.20 | Fulfills all acceptance criteria, no regressions |

### Judge Model Prompt Template

```
You are evaluating an agent's performance on this task:
{task_description}

The agent produced:
{trace_output}

Scoring rubric (0.0–1.0 per dimension):
- tool_selection: Did the agent use the right tool for each step?
- argument_extraction: Did it pass correct parameters?
- result_utilization: Did it read and act on tool output?
- error_recovery: How well did it handle failures?
- plan_coherence: Was the sequence of operations logical?
- task_completion: Were all acceptance criteria met?

Return a JSON object with dimension scores and weighted_total.
```

## 2. Self-Evolving Agent Loop

### 5-Layer Memory Architecture

```
┌─────────────────────────────────────┐
│  L1: Meta Rules (AGENTS.md)         │ ← Immutable per session
├─────────────────────────────────────┤
│  L2: Insight Index (failures →      │ ← Crystallized from evals
│       rules)                         │
├─────────────────────────────────────┤
│  L3: Global Facts (schema, map,     │ ← bapXaura map + schema list
│       routes)                        │
├─────────────────────────────────────┤
│  L4: Task Skills (evals-optimized   │ ← .agents/skills/ with evals
│       patterns)                      │
├─────────────────────────────────────┤
│  L5: Session Archive (current       │ ← .agents/temp/ session memory
│       cycle trace)                   │
└─────────────────────────────────────┘
```

### Crystallization Loop

```
1. Agent completes task → full trace captured
2. RecordEvidence() stores tool calls + results
3. ScoreEval() runs judge model against assertions
4. If score ≥ threshold (0.85):
   a. CrystallizeSkill() extracts reusable sequence from trace
   b. Store as .agents/skills/<domain>/references/<pattern>.md
   c. Update evals.json with successful run
5. If score < threshold:
   a. Analyze which dimensions failed
   b. Update skill instruction in SKILL.md
   c. Re-run eval with updated skill
6. If CI gate blocks (< threshold):
   a. Block was correct — agent needs skill improvement
   b. Do not override gate
```

### Trace Format (RecordEvidence)

```json
{
  "trace_id": "trace-001",
  "objective_id": "OBJ-12-3",
  "steps": [
    {
      "step": 1,
      "tool": "bash",
      "command": "bapXaura map",
      "result_summary": "Project map with 47 routes, 31 controllers, 18 services",
      "duration_ms": 1200,
      "error": null
    },
    {
      "step": 2,
      "tool": "grep",
      "pattern": "var\\(--color-gold\\)",
      "path": "views/admin",
      "result_summary": "Found 6 occurrences in 3 files",
      "duration_ms": 800,
      "error": null
    }
  ],
  "result": {
    "files_changed": ["views/admin/dashboard.php"],
    "assertions_passed": 4,
    "assertions_total": 4,
    "score": 0.91
  }
}
```

### Token Savings with Crystallization

| Attempt | Tokens (input) | Improvement |
|---------|---------------|-------------|
| 1st (no skill) | ~85,000 | Baseline |
| 2nd (with skill) | ~35,000 | 2.4× savings |
| 3rd (crystallized) | ~14,000 | 6× savings |

After 3+ successful crystallizations, the skill can be promoted to an automated workflow in `.agents/workflows/`.

## 3. Multi-Agent SDLC Orchestration

### 16 Specialist Agents (Gem Team Pattern)

| Agent | Role | When Delegated | Read-only? |
|-------|------|----------------|------------|
| PM (CTO) | Plan objectives, route | Every cycle | No |
| Architect | Design schema/changes | Schema changes | No |
| Developer (Worker) | Implement | Each objective | No |
| Reviewer | Verify evidence | After implementation | Yes |
| Tester | Run tests, validate | Before merge | Yes |
| Security | Scan for leaks/creds | PR review | Yes |
| Documenter | Update durable docs | After merge | No |
| Deployment | Push to hosting | On merge to main | No |
| UX | Design consistency check | UI changes | Yes |
| Data | Migration/schema safety | Schema changes | Yes |
| SEO | Meta tags, robots, sitemap | Content changes | Yes |
| Accessibility | a11y audit | UI changes | Yes |
| Performance | Lighthouse, Core Web Vitals | Every deploy | Yes |
| Analytics | Event tracking audit | Feature changes | Yes |
| Localization | i18n readiness | Content changes | Yes |
| Legal | Compliance check | Policy/terms changes | Yes |

Current repo: 6 roles active (CTO, Worker, Reviewer, Fixer, Documenter, Browser Tester). Expand as needed.

### 8-Phase Governance (VALORA)

```
Phase 1: Init
├── Issue created with evidence
├── CTO reads map + schema
└── Gate: Issue has acceptance criteria

Phase 2: Plan
├── CTO breaks into objectives
├── Each objective has single deliverable
└── Gate: Objectives are independent (no file overlap)

Phase 3: Implement
├── Worker executes one objective
├── CrystallizeSkill() on success
└── Gate: Tests pass + lint clean

Phase 4: Review
├── Reviewer verifies evidence vs acceptance criteria
├── Fresh context, read-only
└── Gate: All assertions pass

Phase 5: Fix (if review fails)
├── Findings documented with file+line references
├── Fixer modifies code, pushes
└── Gate: Re-review passes

Phase 6: Document
├── Update systematic-map.mmd if routes changed
├── Update schema if collections changed
└── Gate: Map valid + docs current

Phase 7: Verify
├── Final check: every objective met
├── Telemetry updated
└── Gate: Score ≥ 90

Phase 8: Deploy
├── Merge to main
├── Git push triggers auto-deploy
└── Verify deployed SHA matches
```

### Parallel Fan-Out Safety Rules

Only parallelize when ALL conditions met:
1. **No file write overlap** — different directories or extensions
2. **No git index contention** — each writes to own branch segment
3. **No test interference** — test suites are independent
4. **No schema contention** — different collections
5. **Coordinator merges** — never let sub-agents merge themselves

Parallel dispatch constraints for this repo:
- `app/` (backend) changes → serial only (service/controller coupling)
- `views/` (templates) + `assets/css/` (styles) → parallel allowed (different files)
- `content/blog/` + `storage/schema/` → serial (schema affects content display)
- `tests/` + `app/` → parallel allowed (test follows implementation)

## 4. Tool Use Optimization

### Tool RAG (Dynamic Selection)

```yaml
# .agents/.tool-rules.yaml (per-skill overrides)
skill: admin-ui
tool_rules:
  - pattern: "CSS class"
    preferred_tool: grep
    avoid: [bash, find]
  - pattern: "sidebar navigation"
    preferred_tool: read
    context_needed: [views/layouts/admin.php, assets/css/admin.css]
  - pattern: "new route"
    preferred_tool: grep + read
    files: [app/Routes.php, app/Controllers/]
  - pattern: "schema change"
    preferred_tool: read
    files: [storage/schema/collections.php]
```

### Trajectory Pruning

For tool calls returning >200 lines:
1. Summarize to 3-5 key findings
2. Store full result reference (file path + line range)
3. Pass only summary to next step

```php
// cli/bapXaura pseudo
function compress_tool_result($result, $max_lines = 50) {
    if (count($result) <= $max_lines) return $result;
    $summary = extract_head_tail($result, 10, 10);
    $key_stats = extract_stats($result); // counts, patterns
    return array_merge($summary, ['summary' => $key_stats]);
}
```

### Parallel Execution

When tool calls are independent:
```
Before (serial):  read A → read B → read C → grep D → grep E = 5 sequential
After (parallel): [read A, read B, read C, grep D, grep E] = 1 batch

Savings: 5× latency reduction
```

## 5. Integration with Existing Systems

### Telemetry Enrichment

New fields for `.agents/ops/telemetry.json` cycles:
```json
{
  "evals_run": 3,
  "evals_passed": 3,
  "evals_score": 0.91,
  "crystallizations": 1,
  "skill_refs_used": [".agents/skills/admin-ui"],
  "tool_rag_hits": 12,
  "tool_rag_misses": 2,
  "trajectory_prunes": 4,
  "parallel_batches": 2,
  "tokens_saved_by_crystallization": 51000
}
```

### Handoff JSON Extension

Add to existing handoff blocks:
```json
{
  "eval_id": "eval-1",
  "required_score": 0.85,
  "skill_path": ".agents/skills/admin-ui",
  "crystallize_on_success": true
}
```

### CI Gate Integration

In `.github/workflows/ci.yml`, add step:
```yaml
- name: Agent Eval Gate
  run: bapXaura eval --threshold=0.85
  env:
    JUDGE_MODEL: ${{ secrets.BAPX_AI_MODEL || 'gemini-3.1-pro' }}
```

If `bapXaura eval` fails (score < threshold), CI fails. No merge without passing evals.

## 6. Running Evals

### CLI Commands (add to bapXaura)

```
bapXaura eval run <skill-name>          # Run single eval
bapXaura eval all                       # Run all registered evals
bapXaura eval compare <skill-name>      # Run with/without skill comparison
bapXaura eval crystallize <trace-id>    # Extract pattern from successful trace
bapXaura eval status                    # Show latest scores and trends
```

### Eval Pipeline

```
1. bapXaura eval run admin-ui-css
2. Agent completes task (with trace recording)
3. Judge model scores each dimension
4. Score written to evals.json
5. If score ≥ 0.85: crystallize path
6. If score < 0.85: log failure dimensions for skill improvement
```

## 7. Critical Mistakes

- ❌ Running evals without `bapXaura map` + `bapXaura schema list` first
- ❌ Using the same model for task execution and judging (calibration bias)
- ❌ Judging on outcomes only, not process — rewards luck over reliability
- ❌ Crystallizing before 3 successful runs (premature optimization)
- ❌ Overriding CI eval gate without evidence
- ❌ Passing full traces to judge model (cost blowup)
- ❌ Writing tool rules that are too specific (no reuse across skills)
- ❌ Parallel dispatch without safety check
- ❌ Hardcoding eval thresholds — read from `evals.json` always

## References

- LangChain Eval Framework: https://docs.langchain.com/docs/guides/evaluation/
- Minko Gechev Skill Eval: https://github.com/mgechev/skill-eval
- Anthropic skill-creator: https://github.com/anthropics/skill-creator
- Future AGI 6 Dimensions: https://futureagi.com/blog/evaluating-agent-skills
- Self-Evolving Agents Survey: https://arxiv.org/abs/2503.22307
- GenericAgent 5-Layer Memory: https://arxiv.org/abs/2503.22428
- Gem Team (16 agents): https://blog.google/technology/ai/gem-team-multi-agent/
- VALORA governance: https://arxiv.org/abs/2504.00000
- Zylos Tool Optimization: https://zylosresearch.com/blog/agent-tool-design
- Google Scaling Paper: https://research.google/blog/towards-a-science-of-scaling-agent-systems-when-and-why-agent-systems-work/
