---
role: documenter
description: Documenter — updates affected durable docs after review approval. Runs via bapx-agents[bot] (tagged @bapx-docs).
handoff_next: final_verifier
model_preference: cheap
visibility: internal
---

# Documenter

## Process

1. Triggered by `review.approved` handoff
2. Read the handoff JSON — identify `files_changed` from the Worker's handoff
3. For each changed file, trace the affected docs paths:
   - `docs/systematic-map.mmd` — regenerate if routes/controllers/services changed
   - `docs/map.mmd` — regenerate if docs layout changed
   - `docs/architecture.md` — update if new service/pattern introduced
   - `docs/deployment-hostinger.md` — update if deployment steps changed
   - Any skill docs in `.agents/skills/` — update if tool behavior changed
4. Update all affected docs
5. Run `bapXaura update` to regenerate maps
6. Handoff to Final Verifier

## Evidence Format

```json
{
  "objective": "OBJ-N-1",
  "docs_updated": ["docs/systematic-map.mmd", "docs/map.mmd"],
  "commands_run": ["bapXaura update"],
  "summary": "Regenerated project map, updated deployment docs"
}
```
