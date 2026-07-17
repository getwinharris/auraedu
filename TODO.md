---
title: Project TODO
description: Active todo list for pending engineering work across documentation, MCP, CLI, and browser testing.
category: root
---

# TODO

## Phase A: Documentation (High Priority)

- [ ] `docs/services/` — Create directory and document all 41 services
- [ ] `docs/pages/` — Add docs for 39 undocumented views (public, admin, account)
- [ ] `docs/cli/` — Create directory for CLI tool documentation
- [ ] `docs/agents/` — Document handoff system, workflows, skills, telemetry
- [ ] `docs/integrations/` — Document Stripe, Meta Pixel, Google Site Kit
- [ ] `docs/testing.md` — Document test suite (93 tests, how to run, how to add)
- [ ] Document AI/Agent system (agent, support bot, admin agent, BlogDraftService)
- [ ] Document mail system, media system, backup system, coupon/shipping/address

## Phase B: MCP Endpoint (High Priority)

- [ ] `app/Controllers/McpController.php` — JSON-RPC 2.0 controller
  - [ ] `tools/list` — List available tools
  - [ ] `tools/call` — Execute a tool
  - [ ] `resources/list` — List available resources
  - [ ] `resources/read` — Read a resource
  - [ ] `prompts/list` — List prompt templates
  - [ ] `prompts/get` — Get a prompt template
  - [ ] `initialize` — Protocol negotiation
  - [ ] `ping` — Health check
- [ ] Register MCP routes in `ProjectMapService.php`
- [ ] Add MCP route to `index.php`
- [ ] MCP tools: `query_database`, `read_collection`, `read_schema`, `read_project_map`, `run_bapXaura`, `list_skills`, `list_handoffs`
- [ ] MCP resources: `schema://collections`, `map://systematic`, `docs://{path}`, `handoffs://{issue}`
- [ ] MCP prompts: `analyze_issue`, `create_objective`, `review_changes`

## Phase C: A2A Protocol (Medium Priority)

- [ ] A2A task endpoints: `SendMessage`, `GetTask`, `ListTasks`, `CancelTask`, `Subscribe`
- [ ] Agent Card discovery (JSON metadata for agent capabilities)
- [ ] Wire A2A into existing handoff chain

## Phase D: Admin/Support API `/v1` (Medium Priority)

- [ ] `app/Controllers/ApiV1Controller.php`
- [ ] Admin endpoints: products, orders, users, analytics, audit log
- [ ] Support endpoints: tickets, chat, agent context
- [ ] Authentication for `/v1` endpoints

## Phase E: CLI Gaps (High Priority)

- [ ] `bapXaura websearch <query>` — Search the web
- [ ] `bapXaura webfetch <url>` — Fetch URL content
- [ ] `bapXaura browser navigate <url>` — Playwright-style browser control
- [ ] `bapXaura browser click <selector>`
- [ ] `bapXaura browser snapshot [file]`
- [ ] `bapXaura browser screenshot [file]`
- [ ] `bapXaura browser fill <selector> <value>`
- [ ] `bapXaura browser evaluate <js>`
- [ ] `bapXaura browser wait <text>`
- [ ] `bapXaura task <objective>` — Sub-agent dispatch
- [ ] `bapXaura skill load <name>` — Load a skill
- [ ] `bapXaura todo add/list/update` — Task tracking
- [ ] `bapXaura question <prompt>` — Ask user for input

## Phase F: Playwright MCP Integration

- [ ] Install `@playwright/mcp` as project dependency
- [ ] Create `cli/browser-tester.php` — PHP wrapper for Playwright MCP
- [ ] Wire into `bapXaura browser` subcommand
- [ ] Support all Playwright MCP tools: navigate, click, snapshot, screenshot, fill, evaluate, network, wait, console, hover, select, upload, resize, drag, tabs

## Phase G: Bug Fixes & Known Issues

- [ ] `.mobile-cart-tray` class misnomer — shows at all viewports, rename
- [ ] `wallet_transactions` schema missing `admin_managed` key
- [x] `/admin/environment` GET route + controller restored and working
- [ ] Test for `docs/README.md` validates file exists but should validate content

## Phase H: Architecture Decisions

- [ ] CEO decision: Who handles MCP endpoint work? (Worker vs CTO vs new role)
- [ ] CEO decision: MCP at same PHP app vs standalone server?
- [ ] CEO decision: Browser tester — Playwright MCP dependency vs custom PHP build?
- [ ] CEO decision: A2A same endpoint as MCP or separate?
