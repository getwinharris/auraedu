---
type: skill
name: git
description: Use plain Git for local history, branches, pushes, and Hostinger deployment checkouts.
allowed-tools: Bash(git:*)
---

# Git

Git is the only repository executable required on Hostinger. Do not require
GitHub CLI (`gh`) on the hosted server.

## Boundaries

- Use plain `git` for status, diff, branches, commits, fetch, pull, and push.
- Use `bapXaura` only for project-owned operations such as tests, maps, schema,
  database access, browser automation, hooks, and AI configuration.
- GitHub issues, handoff comments, PR creation, review routing, and merge
  coordination belong to GitHub Actions or the GitHub web interface.
- Never wrap ordinary Git commands in `bapXaura`.
- Never force-push or discard divergent customer-fork commits.

## Local Workflow

```bash
git status --short --branch
git switch -c fix/issue-123-description
git diff --check
git add path/to/intended-file
git commit -m "fix: describe the change"
git push -u origin HEAD
```

Pushing an eligible feature/fix branch lets the repository workflow create or
update the PR. CI and reviewer handoffs run in GitHub Actions.

## Workflow Conditions

All `.github/workflows/*.yml` files contain an `if:` guard matching the
repository name. When the repo is renamed or forked, update every workflow:

- `branch-pr.yml`: `github.repository == '<owner>/<repo>'`
- `ci.yml`, `ai-pr-review.yml`, `issue-agent-trigger.yml`, `issue-comment-handoff.yml`: `endsWith(github.repository, '<repo>')`
- `sync-upstream.yml`: `github.repository == '<owner>/<repo>'`

All seven workflow files must match the current repository. CI runs are
otherwise silently skipped.

## Hostinger

```bash
git status --short --branch
git fetch origin
git pull --ff-only origin main
git rev-parse HEAD
```

Hostinger hPanel normally performs the pull automatically. Manual SSH Git
commands are recovery and diagnosis tools only.
