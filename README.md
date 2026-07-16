# AuraEdu — Aura Medical Institute of Electropathy and Hospital

A PHP full-stack monorepo for the Aura Medical Institute website — admissions, hospital facilities, therapy consultations, and product shop.

- **Stack:** PHP 8.3 templates, MySQL, Hostinger shared hosting
- **Frontend:** Server-rendered PHP views following `Design.md` (bapXaura design system)
- **Backend:** Route → controller → service → `DatabaseService` (MySQL)
- **CI:** GitHub Actions — lint, test, map generation/validation, smoke check
- **MySQL is the primary runtime store.** `storage/data/` JSON is import-only.
- **Config:** Set `APP_NAME`, `APP_URL` in `.env`. See `docs/deployment-hostinger.md`.
- **Docs:** See `AGENTS.md` for the agent operating guide, `docs/README.md` for doc index, `docs/systematic-map.mmd` for route wiring, and `docs/deployment-hostinger.md` for deployment steps.
- **Settings:** Site config via Admin → Settings, Integrations admin panel.
- **Secrets:** Admin-editable via Admin → Integrations (MySQL `secrets` table). Never in `.env`.
