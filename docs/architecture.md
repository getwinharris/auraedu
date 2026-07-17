---
type: doc
title: Architecture
description: PHP-rendered public, account, and admin templates in views/ with shared CSS and small inline enhancement scripts.
category: docs
---
# Architecture

## Frontend

- PHP-rendered public, account, and admin templates in `views/`.
- Shared CSS in `assets/css/` plus small inline enhancement scripts in PHP layouts.
- Known public routes are dispatched by `index.php` into PHP controllers.
- Unknown routes render the PHP 404 page with `HTTP 404`. There is no SPA fallback.

## Backend

- PHP controllers in `app/Controllers/` render pages and handle form posts.
- `/api/*` routes return JSON for the shop, product, astrologer, and temple endpoints. CSRF is NOT enforced on `/api/*`.
- Business logic lives in services under `app/Services/`.
- Route and dependency documentation is generated from `app/Services/ProjectMapService.php`.

### Routing split

`index.php` splits incoming requests at the top:

1. **`/api/*` paths** → `api/index.php`. Always JSON. No CSRF. Session is optional. Error responses are always JSON.
2. **`/remoteDB`** → `RemoteDbController` for proxied MySQL queries from local/dev.
3. **Web routes** (`/admin/*`, public pages, account) → `app/bootstrap.php` (starts session, loads autoloader) → `Router` → controller. CSRF is enforced on POST.

Admin routes are defined in `ProjectMapService` (`app/Services/ProjectMapService.php`) and served through `app/routes.php` which returns `ProjectMapService::registry()['routes']`.

### CSRF Convention

- `AdminController::__construct()` validates CSRF on every POST before the action runs.
- `BaseController::validateCsrf()` checks `$_POST['_csrf']` against `$_SESSION['csrf_token']`.
- PHP does NOT parse `Content-Type: application/json` bodies into `$_POST`. Admin AJAX endpoints that send JSON must validate CSRF from the parsed JSON body, not the constructor.
- The CSRF token is set lazily in layouts: `$_SESSION['csrf_token'] ??= bin2hex(random_bytes(16))`.
- Admin layout (`views/layouts/admin.php`) exposes `$csrf` for template forms.

## Data Persistence

- MySQL is the primary runtime data store, accessed through `DatabaseService`.
- Collection schema lives in `storage/schema/collections.php`. Data is stored in MySQL tables (not JSON files).
- Runtime data is remote-only: `.env` supplies direct hosted MySQL credentials to `DatabaseService`; when direct access is unavailable, the CLI/application use `APP_URL/remotedb` (remoteDB fallback). The local checkout is not a customer-data fallback.
- Blog posts are file-based: `content/blog/posts/*.md` with YAML frontmatter.
- Media metadata lives in `storage/media.yaml` (not MySQL).
- `AgentContextService` builds safe user-specific context for the support/model assistant.

### remoteDB fallback chain

`DatabaseService` tries in order:
1. Connect to local MySQL via `config/database.php` credentials.
2. If MySQL unreachable AND `remote_url` is set → switch to remote mode.
3. In remote mode, every `read()`/`write()`/`upsert()`/`delete()` calls `{APP_URL}/remoteDB` with `REMOTE_DB_PASSWORD`.
4. `remote_url` defaults to `https://auraedu.co.in/remoteDB`.
5. If remote call fails (non-200), `read()` returns `[]`, mutations throw `RuntimeException`.

This enables shared-hosting dev without local MySQL — the dev server proxies through production's remoteDB endpoint.

## Current Data Flow

1. `index.php` routes public, account, review, and admin paths into the PHP router.
2. Controllers load data through services and render templates.
3. Customer forms post back to PHP controllers for cart, checkout, contact, reviews, and account flows.
4. Admin forms update MySQL-backed resources through services. CSRF is enforced on the controller constructor.
5. API clients use `/api/*` for JSON catalog data. No CSRF. Session optional.

## File Structure

```text
api/                    PHP API entry point
app/
  Controllers/          Page and form controllers
  Services/             Business logic and MySQL persistence
  Router.php            Route dispatcher
assets/
  css/                  Shared stylesheets
cli/                    bapXaura CLI entry point and helper scripts
content/blog/posts/     Blog post markdown files
docs/                   Project, module, and deployment docs
storage/schema/         MySQL schema contract (collections.php)
storage/data/           Optional JSON seed files (one-time sync only)
views/
  public/               Customer-facing PHP templates
  account/              Signed-in customer pages
  admin/                Owner/admin pages
  layouts/              Shared layouts
```
