---
title: Route & View Audit — Institute Repositioning
description: Classification of every route/view as retain, repurpose, hide, or remove-later for the Aura Medical institute repositioning.
category: docs
---

# Route & View Audit — Institute Repositioning

Method: every route in `app/Services/ProjectMapService.php` and matching view in `views/` is classified.
Goal: the site leads with the institute/hospital (B.E.M.S., admissions, hospital, therapies), not inherited
commerce or spiritual consultation. Architecture is preserved — we repurpose, not rebuild.

## Legend
- **Retain** — keep as-is (or minor copy tweak).
- **Repurpose** — keep route, change copy/intent to institute voice.
- **Hide** — keep code but unlink from nav/SEO (301 or noindex) until rewritten.
- **Remove later** — delete after content-model migration completes.

## Public routes
| Route | Class | Action |
|-------|-------|--------|
| `/` | Retain | Homepage (hero already rewritten to medical). |
| `/education` | Retain | Lead B.E.M.S. programme page. |
| `/sri-panchami-education` | Repurpose→Hide | Legacy URL; 301 redirect to `/education`. |
| `/shop` | Retain | Therapy/acupuncture product shop (supporting). |
| `/product/{slug}` | Retain | Product detail. |
| `/categories` | Retain | Shop categories. |
| `/cart`, `/checkout`, `/payment/verify`, `/create-order`, `/verify-payment` | Retain | Commerce flow. |
| `/consult` | Repurpose | → "Therapies" — acupuncture/allied-health appointments. |
| `/consult/{slug}` | Repurpose | → therapy/faculty profile. |
| `/temples`, `/temples/{slug}` | Repurpose | → "Hospital" — facilities, departments, care. |
| `/about` | Repurpose | Institute history/mission (rewrite from devotional copy). |
| `/contact` | Repurpose | Admissions + hospital enquiries (rewrite form subjects). |
| `/support` | Repurpose | Student/customer support (not astrology). |
| `/spiritual` | Hide | Devotional page; unlink, 301 to `/education`. |
| `/blog`, `/blog/{slug}`, `/blog/category/{slug}`, `/help/{slug}`, `/docs` | Retain | Editorial/help (copy must drop astrology framing). |
| `/login`, `/register`, `/logout`, `/forgot-password`, `/reset-password`, `/auth/google*` | Retain | Auth. |
| `/account/*` | Retain | Student/customer dashboard. |
| `/reviews/astrologer` | Remove later | Astrologer reviews; replace with therapist/faculty reviews. |
| `/reviews/product` | Retain | Product reviews. |
| `/sitemap.xml` | Retain | Regenerate with new URLs. |
| `/terms`, `/privacy` | Repurpose | Institute-specific legal copy. |

## Admin routes
| Route | Class | Action |
|-------|-------|--------|
| `/admin` + dashboard/products/categories/coupons/orders/settings/* | Retain | Core commerce admin. |
| `/admin/astrologers`, `/admin/astrologers/save`, `/admin/astrologers/delete` | Remove later | Astrologer CRUD; replace with faculty CRUD. |
| `/admin/temples`, `/admin/temples/save`, `/admin/temples/delete` | Remove later | Temple CRUD; replace with hospital/facility CRUD. |
| `/admin/appointments` | Repurpose | Therapy appointments. |
| `/admin/integrations`, `/admin/agent`, `/admin/agent/ask` | Retain | AI agent + secrets (set `remote_db_password`). |
| `/admin/media`, `/admin/backups`, `/admin/audit-log`, `/admin/support-tickets` | Retain | Ops. |
| `/admin/blog*`, `/admin/appearance*`, `/admin/shipping`, `/admin/tax-report`, `/admin/consultation-analytics` | Retain | Content/ops. |
| `/admin/contact-submissions*`, `/admin/email-inbox`, `/admin/email-outbox` | Retain | Comms. |
| `/admin/developer/project-map`, `/admin/developer/workflow` | Retain | Dev tooling. |
| `/admin/environment/fix-permissions` | Retain | Working — route registered and controller active. |

## API routes
| Route | Class | Action |
|-------|-------|--------|
| `/api/shop`, `/api/categories`, `/api/product/{slug}`, `/api/consult` | Retain | Commerce/therapy APIs. |
| `/api/agent` | Retain | AI agent. |
| `/api/agent` family, `/api/browser/*`, `/api/tts/*`, `/api/support/latest-message`, `/api/consultations/{id}/status` | Retain | Agent/browser/support tooling. |
| `/remoteDB` | Retain | DB proxy (set password). |
| `/api/temples` | Repurpose | → hospital/facility API. |

## Views to rewrite (copy audit)
- `views/public/spiritual.php` — Hide.
- `views/public/temples.php`, `views/public/temple.php` — Repurpose to Hospital.
- `views/public/about.php` — Repurpose to institute voice.
- `views/public/contact.php` — Repurpose subjects (remove "astrology").
- `views/public/support.php` — Repurpose (remove rudraksha/pooja).
- `views/public/consult.php`, `views/public/astrologer*.php` — Repurpose to Therapies/faculty.

## Next
Implement in this order: (1) add 301 for `/sri-panchami-education`; (2) rewrite `/about`, `/contact`, `/support`;
(3) repurpose `/consult`, `/temples`; (4) hide `/spiritual`; (5) later replace astrologer/temple admin with
faculty/hospital admin.
