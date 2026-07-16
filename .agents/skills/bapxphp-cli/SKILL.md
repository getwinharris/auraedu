---
type: skill
name: bapxaura-cli
description: Use bapXaura CLI for ALL file/content/db operations. Never use raw shell/edit/write/find/grep tools directly.
---
# bapXaura CLI

All project operations go through `bapXaura`. This ensures every operation is auditable, logged, and consistent.

## Generic File Operations

| Operation | CLI Command |
|-----------|-------------|
| Read file    | `bapXaura read file <path>` |
| Write file   | `bapXaura write file <path>` (pipe stdin) |
| Edit file    | `bapXaura edit <path> <search> <replace>` |
| Search code  | `bapXaura grep <pattern> [path]` |
| Find files   | `bapXaura find <glob-pattern>` |
| Run command  | `bapXaura run <command...>` |
| List dir     | `bapXaura run ls <path>` |

## Content CRUD

```bash
bapXaura read blog                    # list all blog posts
bapXaura read blog <slug>             # read a blog post
bapXaura write blog [slug]            # create or edit blog post (interactive)
bapXaura read docs [slug]             # list or read customer help guides
bapXaura write docs [slug]            # create or edit a customer help guide
bapXaura read product                  # list all products
bapXaura read product <slug>           # read a product
bapXaura write product [slug]          # create or edit product (interactive)
```

## Database

```bash
bapXaura db query products --limit 5   # query products
bapXaura db find orders ord_123        # find an order
bapXaura db raw "SELECT * FROM ..."    # raw SQL
bapXaura db init                       # create MySQL tables from schema
bapXaura db sync                       # push JSON → MySQL
```

## Project Management

```bash
bapXaura test                          # run tests
bapXaura update                        # regenerate both map artifacts after source/docs changes
bapXaura ci                            # non-mutating full PR/CI validation
bapXaura check                         # alias for bapXaura ci
bapXaura serve                         # start dev server
bapXaura map:gen                       # regenerate project map
bapXaura docsmap                       # regenerate docs/map.mmd (content)
bapXaura codemap                        # regenerate map.mmd (code graph)
bapXaura schema list                   # list all collections
bapXaura hooks install                 # install repository-owned Git hooks
bapXaura hooks status                  # inspect hook installation
bapXaura tui                           # interactive project operations
bapXaura help                          # full reference

## Validation

Run the smallest useful validation for the change:

```bash
bapXaura lint path/to/changed.php
bapXaura update
bapXaura ci
```

## Telemetry & Ops

Handoffs and telemetry live under `.agents/`:

```bash
bapXaura handoff next <issue>         # load next objective
bapXaura handoff validate <file>      # validate handoff JSON
```

Cycle data is tracked in `.agents/ops/telemetry.json`.

Use plain `git` for branches, commits, fetch, pull, and push. GitHub Actions
and the GitHub web interface own issue/PR/handoff conversations. The hosted
server does not require `gh`.

## Attachments

When a coding agent attaches screenshots, images, or files, they go into `.agents/temp/`. This is the standard inbox for all user-provided attachments in this repo.

```bash
bapXaura run ls .agents/temp/         # list attachments
bapXaura read file .agents/temp/<file> # read an attachment
```

## Browser Agent (headless browser automation)

```bash
bapXaura browser-agent open <url>              # fetch page, YAML snapshot
bapXaura browser-agent click <selector>         # click element
bapXaura browser-agent fill <selector> <value>  # fill form input
bapXaura browser-agent submit [selector]        # submit form
bapXaura browser-agent snapshot                 # YAML page snapshot
bapXaura browser-agent smoke <url>              # health check
bapXaura browser-agent screenshot [file]        # YAML output
bapXaura browser-agent config set <k> <v>       # runtime config
bapXaura browser-agent log                      # audit trail
bapXaura browser-agent count <tag>              # count DOM tags
bapXaura browser-agent close                    # clean session
bapXaura browser-agent --pw <command>           # forward to Playwright
```

## Missing Commands

If a required operation is missing or unsafe, enhance the nearest existing `bapXaura` command before performing the operation. Never use raw bash/write/edit/find tools for operations that have a `bapXaura` equivalent.
