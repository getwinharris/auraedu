---
title: Project Objectives & Engineering Report
description: Current-state engineering report and product objectives for AuraEdu — Aura Medical Institute of Electropathy and Hospital.
category: root
---

# Project Objectives & Engineering Report

Generated: July 2026
Owner: Aura Medical Institute of Electropathy and Hospital (AuraEdu)
Domain: https://auraedu.co.in

## 1. Product Objective (Current)

AuraEdu is the web presence for **Aura Medical Institute of Electropathy and Hospital**, a Coimbatore-based
institution offering:

- **B.E.M.S.** — Bachelor of Electro-Medical Sciences, the core degree programme (admissions: no NEET, no age bar).
- **Hospital & clinical training** — students train where care is delivered.
- **Acupuncture & allied-health therapies** — electropathy practice plus complementary therapies.
- **Therapy/wellness products** — an online shop for clinically oriented acupuncture and electropathy products.
- **Campus services** — hostel, placements, faculty, facilities, gallery, news, approvals/disclosures, contact.

The product objective is the **institute and hospital**, not inherited commerce or spiritual consultation.
Every customer-facing surface must lead with education and care; commerce is a supporting function
(therapy products), not the primary narrative.

### Non-objectives (explicitly retired branding)
- Vedic astrology / horoscope consultation as a primary journey.
- Temple/devotional guidance, rudraksha, pooja kits, sacred jewellery as the lead commerce story.
- "spiritual" consultation framing.

## 2. What Has Been Built (Implemented)

### 2.1 Fork Sync & CI Pipeline
| Feature | Status | Files |
|---------|--------|-------|
| Fork sync switch: event→schedule | ✅ Done | `.github/workflows/sync-upstream.yml` |
| CI pipeline (lint → test → map val → docs val → codemap:val → smoke) | ✅ Done | `cli/bapXaura` `cmd_ci()` |

### 2.2 AGENTS.md & Documentation Consolidation
| Feature | Status | Files |
|---------|--------|-------|
| AGENTS.md consolidated | ✅ Done | `AGENTS.md` |
| YAML frontmatter with `type:` on source .md files | ✅ Done | `docs/*`, `content/blog/posts/*`, `.agents/skills/*` |

### 2.3 Branding & Re-theme (July 2026)
| Feature | Status | Files |
|---------|--------|-------|
| Renamed repo `bapXphp` → `auraedu`, CLI `bapXaura` | ✅ Done | repo, `cli/bapXaura` |
| Aura Medical palette in `band.css` (`--color-maroon`→green `#08A900`, `--color-gold`→orange `#EF6900`, `--color-accent`→teal `#087E82`) | ✅ Done | `assets/css/band.css` |
| Fonts: Bebas Neue / Oswald / Montserrat / Noto Sans Tamil | ✅ Done | `assets/css/band.css`, `Design.md` |
| Favicon + logo → `/assets/images/auraedu-favicon.svg`, `/assets/images/brand/logo.png` | ✅ Done | `views/layouts/app.php` |
| Removed devotional `hero-temple-bg.webp` backgrounds (auth/checkout/hero) | ✅ Done | `assets/css/band.css`, `views/public/blog*.php` |
| Homepage hero rewritten to medical/institute content | ✅ Done | `views/public/home.php` |
| Primary nav repurposed: B.E.M.S. · Shop · Therapies · Hospital · About · Contact | ✅ Done | `views/layouts/app.php` |

### 2.4 Remote DB — Endpoint Fixed
| Feature | Status | Files |
|---------|--------|-------|
| `index.php` `/remoteDB` case-mismatch bug fixed (CLI sends `/remoteDB`, was checking `/remotedb`) | ✅ Done | `index.php` |
| Password-gated `init` action on `/remoteDB` | ✅ Done | `app/Controllers/RemoteDbController.php` |
| CLI `db init` falls back to remote endpoint when direct MySQL unavailable | ✅ Done | `cli/bapXaura` |
| 22 tables created on hosted MySQL; site returns HTTP 200 | ✅ Done | live `auraedu.co.in` |

### 2.5 Admin Agent Workflow Page
| Feature | Status | Files |
|---------|--------|-------|
| `GET /admin/developer/workflow` + view | ✅ Done | `app/Services/ProjectMapService.php`, `views/admin/workflow.php` |

### 2.6 Handoff System (CLI)
| Feature | Status | Files |
|---------|--------|-------|
| `bapXaura handoff` validate/comment/next/template/execute/score | ✅ Done | `cli/bapXaura`, `cli/handoff.php` |

### 2.7 Map Architecture Overhaul
| Feature | Status | Files |
|---------|--------|-------|
| `bapXaura map` / `codemap` / `map:val` | ✅ Done | `cli/bapXaura` |
| `docs/systematic-map.mmd`, `docs/map.mmd`, root `map.mmd` | ✅ Done | generated |

### 2.8 Admin Audit Log Wiring
| Feature | Status | Files |
|---------|--------|-------|
| Admin mutations audit-logged | ✅ Done | `AdminController.php`, `ProjectMapService.php` |

## 3. Route Audit — Institute Repositioning

Full audit in `docs/route-audit.md`. Summary classes:
- **Retain**: `/`, `/education`, `/shop`, `/product/*`, `/cart`, `/checkout`, `/contact`, `/about`, `/blog`, `/account/*`, `/admin/*`, `/remoteDB`.
- **Repurpose**: `/consult` → Therapies/appointments; `/temples` → Hospital; `/consult/{slug}` → therapy profile; `/sri-panchami-education` → redirect to `/education`; `/support` → student/customer support.
- **Hide**: `/spiritual` (devotional page) — unlink from nav; 301 to `/education` or remove.
- **Remove later**: astrologer-specific admin (`/admin/astrologers`), temple admin (`/admin/temples`), `reviews/astrologer`, when content model migration completes.

## 4. Institute Content Model (to build)

Defined in `storage/schema/collections.php` extensions + `content/` pages:
- `bems` (programme, eligibility, duration, syllabus), `admissions`, `faculty`, `hospital`, `facilities`,
  `hostel`, `placements`, `approvals`, `gallery`, `news`, `contact`.
- See `docs/institute-content-model.md` for the verified field model.

## 5. Known Issues & Context

- `/remoteDB` endpoint currently **unprotected** (no `remote_db_password` secret set). Set it in Admin → Integrations immediately.
- MySQL tables exist but are **empty** — no seed content (programmes, faculty, products). Populate via admin or seed JSON.
- Legacy spiritual/astrology/temple copy remains in `views/public/spiritual.php`, `temples.php`, `about.php`, `contact.php`, `support.php` — scheduled for rewrite/hide per route audit.
- `og-image.jpg` is a generic placeholder; replace with an Aura Medical campus/image asset.

## 6. Next Objectives

### Phase A: Content Model (current)
1. Build institute collections + pages: B.E.M.S., admissions, faculty, hospital, facilities, hostel, placements, approvals, gallery, news.
2. Add acupuncture/therapy product category + seed products.
3. Rewrite `about.php`, `contact.php`, `support.php` to institute voice; hide `/spiritual`.

### Phase B: Documentation
4. Rewrite `Design.md` stale sections (astrologers/devotional/temple/shop-first).
5. Replace `docs/competitor-review.md` with electropathy/alt-med competitor research.
6. Docs for services/controllers (Phase B from prior report).

### Phase C: Verification
7. Cross-check every factual claim (B.E.M.S. recognition, no-NEET, address, phone) against Aura's Instagram + supplied documents before publishing.
8. Preserve architecture; update admin/editor docs alongside product work.
