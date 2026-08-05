---
type: readme
title: AuraEdu
description: PHP 8.3 and MySQL application for Aura Medical Institute of Electropathy and Hospital.
category: root
---
# AuraEdu

PHP 8.3 and MySQL application for Aura Medical Institute of Electropathy and Hospital.

- Customer UI: server-rendered PHP templates guided by `Design.md`.
- Backend: route → controller → service → `DatabaseService`.
- Content: Markdown blog and help posts in `content/blog/posts/`.
- Configuration: `APP_NAME` and `APP_URL` in `.env`; secrets in Admin → Integrations.
- CI: PHP lint and tests on pull requests and `main`.

See `AGENTS.md` for the contribution workflow and `docs/deployment-hostinger.md` for Hostinger deployment.
