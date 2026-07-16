---
version: alpha
name: AuraEdu
description: Calm, credible interface system for Aura Medical Institute of Electropathy and Hospital — an institute/hospital with B.E.M.S. education, hospital care, and supporting therapy-product commerce.
colors:
  primary: "#08A900"
  on-primary: "#ffffff"
  primary-container: "#00A816"
  on-primary-container: "#E6F7E6"
  secondary: "#087E82"
  on-secondary: "#ffffff"
  secondary-container: "#0A9A9F"
  on-secondary-container: "#E4F4F4"
  tertiary: "#EF6900"
  on-tertiary: "#ffffff"
  tertiary-container: "#FDEEE2"
  on-tertiary-container: "#5c2a00"
  neutral: "#F1F1F1"
  neutral-variant: "#E6E6E6"
  surface: "#FFFFFF"
  surface-container: "#F1F1F1"
  outline: "#D7D7D7"
  outline-variant: "#E6E6E6"
  ink: "#454545"
  ink-muted: "#6A6A6A"
  ink-soft: "#8E8E8E"
  success: "#08A900"
  warning: "#EF6900"
  error: "#D64045"
  info: "#087E82"
typography:
  display:
    fontFamily: "Bebas Neue"
    fontSize: 2.5rem
    fontWeight: 400
    lineHeight: 1.1
  h2:
    fontFamily: "Oswald"
    fontSize: 1.5rem
    fontWeight: 600
    lineHeight: 1.2
  body-md:
    fontFamily: "Montserrat"
    fontSize: 1rem
    fontWeight: 400
    lineHeight: 1.7
  body-sm:
    fontFamily: "Montserrat"
    fontSize: 0.875rem
    fontWeight: 400
    lineHeight: 1.5
  label:
    fontFamily: "Montserrat"
    fontSize: 0.8rem
    fontWeight: 600
    lineHeight: 1.4
  tamil:
    fontFamily: "Noto Sans Tamil"
    fontWeight: 800
rounded:
  xs: 4px
  sm: 8px
  md: 14px
  lg: 20px
  xl: 32px
  pill: 999px
spacing:
  2xs: 2px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  2xl: 48px
  3xl: 64px
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    rounded: "{rounded.sm}"
    padding: 14px 32px
    height: 48px
  button-primary-hover:
    backgroundColor: "{colors.primary-container}"
  button-secondary:
    backgroundColor: "{colors.secondary}"
    textColor: "{colors.on-secondary}"
    rounded: "{rounded.sm}"
    height: 48px
  button-cta:
    backgroundColor: "{colors.tertiary}"
    textColor: "{colors.on-tertiary}"
    rounded: "{rounded.sm}"
    height: 48px
  card-product:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.sm}"
    padding: 16px
  card-therapist:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.sm}"
  input:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.sm}"
    height: 48px
---

## Overview

AuraEdu (Aura Medical Institute of Electropathy and Hospital) is a healthcare-and-education institution. The primary customer journey is discover a course / treatment -> review details -> request an appointment / admission -> manage the session. Editorial content and commerce support that journey; they do not replace it in the first viewport. This file is the canonical visual contract for everything customer-facing in `views/` and `assets/css/band.css`.

Keep the existing PHP templates, routes, forms, and JSON-backed behavior. Design changes must not scaffold a second frontend.

## Colors

- **Primary -- Aura Green (`#08A900`, range `#00A816`–`#149F13`):** dominant brand colour. Logo, major backgrounds, course panels, headings, icons, footer, and primary call-to-action elements. `on-primary` is white; `primary-container` (`#00A816`) is the pressed/active state. White text on green is the accessible default.
- **Secondary -- Energy Orange (`#EF6900`, range `#E96308`–`#F27612`):** reserved strictly for calls to action, admission messaging, important announcements, and phone-number bars. Orange creates urgency against green. Use white text only when the orange is sufficiently dark; for small text use charcoal (`#454545`) over orange.
- **Supporting -- Institutional Teal (`#087E82`):** affiliation strips, secondary information bars, institutional details.
- **Charcoal (`#454545`):** course duration, hostel and placement information, body text hierarchy.
- **Soft Grey (`#F1F1F1`):** background shapes, section separation, subtle visual depth.
- **White (`#FFFFFF`):** text over green/orange/teal backgrounds, clean medical backgrounds, visual breathing space. Predominantly white backgrounds preserve a clean healthcare appearance.
- **Outline / outline-variant:** hairline borders only -- no heavy strokes.
- **Ink / ink-muted / ink-soft:** body text hierarchy from primary copy down to placeholders.
- Success uses Aura Green; warning uses Energy Orange; error is red; info uses teal.

## Typography

- **Main English headings: Bebas Neue** (tall, condensed, bold display). Use for hero/H1-scale headings (B.E.M.S, NO NEET, NO AGE BAR, CALL +91…).
- **Secondary English headings: Oswald Bold** for smaller condensed promotional and section headings.
- **Body text: Montserrat** Regular/Medium, `14px`–`16px`, weight 400, line-height `1.7`.
- **Tamil headings: Noto Sans Tamil ExtraBold**; Tamil body: Noto Sans Tamil Regular/Medium.
- Two type families (English + Tamil equivalent) maximum. Reserve orange strictly for CTAs and critical admission info.
- Labels/metadata: `12px`–`14px`, weight 500-600.

### Scale

| Token | Font | Size | Weight | Line Height | Use |
|---|---|---|---|---|---|
| `display` | Bebas Neue | 2.5rem (40px) | 400 | 1.1 | Hero/H1 display |
| `h2` | Oswald | 1.5rem (24px) | 600 | 1.2 | Section headlines |
| `h3` | Oswald | 1.2rem (19px) | 600 | 1.25 | Subsection / card titles |
| `body-md` | Montserrat | 1rem (16px) | 400 | 1.7 | Primary body text |
| `body-sm` | Montserrat | 0.875rem (14px) | 400 | 1.5 | Secondary body, labels |
| `label` | Montserrat | 0.8rem (13px) | 600 | 1.4 | Eyebrows, metadata, badges |
| `tamil` | Noto Sans Tamil | — | 800 | — | Tamil headings |

Principles: display headings keep tight leading (1.05–1.1) for magazine-grade impact; body stays at 1.5–1.7 for long-form readability; weight (500/600) and color shift carry emphasis — no italics on UI text except the decorative `serif-accent` accent.

## Layout

- Spacing scale: `2, 4, 8, 16, 24, 32, 48, 64px`.
- Container: centered, max `1300px` (`1440px` for wide layouts).
- `.section`: `64px` vertical padding by default. `.section--alt` uses `surface-container` (`#F1F1F1`) for alternate sections.
- Responsive breakpoints: mobile below `744px` (one column, compact header, bottom nav), tablet `744-1128px` (reduced grid columns, same card geometry), desktop above `1128px` (centered container, `64px` section spacing).
- Text, buttons, images, and fixed controls must not overlap or reflow awkwardly as content length changes.
- Do not scale typography with viewport width. Use explicit breakpoint sizes so headings remain predictable and do not dominate short mobile screens.
- The first viewport must lead with the institute: B.E.M.S. programme, admissions, and hospital care. Therapy-product commerce is a supporting function, not the primary narrative. Account pages lead to the user's current task.
- Page sections are unframed full-width bands. Cards are reserved for repeated entities, forms, summaries, and genuinely bounded tools. Never place a card inside another decorative card.
- Desktop operational screens use compact density and stable columns. Mobile screens use one clear column with 16px page gutters and no horizontal scrolling.

## Elevation & Depth

Depth is used sparingly and tonally-first, the way Material 3 treats elevation: a surface reads as "raised" primarily because it's a different, lighter tone than the canvas (white card on light grey surface), with a soft shadow as the secondary cue -- not a spotlight effect.

Four shadow steps exist (`--shadow-sm/md/lg/xl` in `band.css`), each with exactly one job:

| Step | Use |
|---|---|
| `sm` | Resting state for cards and inputs |
| `md` | Raised buttons; hovered product cards |
| `lg` | Hovered therapist/feature cards and value cards |
| `xl` | Modals, drawers, popovers only |

Rules:
- Never stack two shadow steps on one element.
- A hover state may move exactly one step up the scale (`sm` → `md`, or `sm` → `lg`), never two.
- No glow, no colored shadows, no blur-heavy "spotlight" effects.

## Shapes

The radius scale expresses a calm, rounded-but-not-playful geometry. The scale is tightly disciplined: buttons and inputs use `rounded.md` (14px) or `rounded.pill` (999px); cards and panels use `rounded.lg` (20px); large media frames use `rounded.xl` (32px). Do not soften a card corner to a value between `md` and `lg` for the same component family.

| Token | Value | Use |
|---|---|---|
| `rounded.xs` | 4px | Chips, tags, small badges, inline code |
| `rounded.sm` | 8px | Sidebar nav items, type badges, compact UI |
| `rounded.md` | 14px | Buttons, inputs, search pill, product/media cards |
| `rounded.lg` | 20px | Feature cards, panels, hero image frame, value cards |
| `rounded.xl` | 32px | Large media frames and featured showcase tiles |
| `rounded.pill` | 999px | Buttons (primary/secondary/CTA), filter pills, status badges, dots |

Shape should stay consistent within a component family -- don't mix `md` and `lg` radii on sibling elements of the same card.

## Components

- **Header:** light (`rgba(255,255,255,0.98)`), ~`80px` tall, non-sticky, hairline bottom border (`rgba(8,169,0,0.45)`), compact logo, centered primary nav with a green active underline, right-aligned account/cart actions.
- **Navigation:** the linked brand mark and name are the sole home control. Do not repeat a separate Home item in desktop or mobile navigation.
- **Mobile commerce tray:** after the cart becomes non-empty, show one fixed green tray above the bottom navigation with item count and a direct View cart action. Use an 8px radius and stable 56px minimum height; update it without page reload.
- **Editorial media:** every blog post uses one intentional 16:9 image for both its listing thumbnail and article hero. UI guides use a legible screenshot of the exact page, cropped around the relevant interface rather than a decorative stock image, and link the represented page below the article.
- **Buttons (`button-primary` / `button-secondary` / `button-cta`):** `48px` minimum height, `14px` radius (`rounded.md`) — or `pill` for the marketing CTA row, no uppercase, no letter-spacing. Primary is solid Aura Green (`gradient-gold` via orange CTA variant for admission urgency); Secondary is teal outline; CTA is orange for admission/urgent actions. Hover moves one elevation step and lifts 2–3px, no more. Hover states never shift layout. On the dark hero, `btn-outline` becomes a white-bordered glass pill.
- **Forms:** white fields (`on-primary`), `8px` radius, `48px` height, clear labels, a single-value focus ring (`--shadow-focus`) -- no glow.
- **Search/filter:** one rounded (`pill`) search control, or a quiet grouped filter row. No nested cards for filters.
- **Product cards (`card-product`):** white, `14px` radius (`rounded.md`), 1px quiet border, stable 1:1 media, concise title and price, then the `- 0 +` quantity control. Do not add a second cart button.
- **Therapist / faculty cards (`card-therapist`):** white, `14px` radius (`rounded.md`), face-forward portrait, name, speciality, language/experience metadata, and one clear profile/booking action. Every card uses equal media and content tracks so rows align.
- **Hero (`home-hero`):** institute-led offer — B.E.M.S., admissions, and hospital care over actual campus/hospital imagery. The primary action is `Explore B.E.M.S. Admissions`; therapy shop is secondary. Desktop text is left aligned, two-column (copy left, framed student photo right). Mobile uses one column, a compact image, and must reveal the next content band without requiring a full-screen scroll.
- **Hero band (`hero-band`):** reusable atmospheric gradient band for interior pages (Courses, Eligibility, Scope, Gallery, Faculty). Green→teal gradient (`maroon-deep` → `maroon` → `accent`) with layered radial glows + a soft 26px dot texture for depth. White display headline (`font-display`), gold eyebrow, lede in 92% white. Keep the gradient as the mood and let it breathe — no competing accents inside the band.
- **Hero image frame:** the student photo sits in a `rounded.lg` (20px) framed panel with a 1px white hairline and a deep diffuse drop shadow (`0 24px 48px -8px rgba(0,0,0,0.35)`), `object-fit: cover` so it reads as a featured panel, not a floating logo.
- **Authentication:** login and registration are task pages, not marketing pages. Use a centered form surface, suppress the public footer, and keep the complete form visible on common mobile heights.
- **Consultation discovery:** search and language controls form one quiet toolbar. Results render immediately without reveal animations or low-opacity loading states. Empty and filtered states explain the next action.
- **Account:** use a persistent internal menu and one unframed content region. Orders, sessions, addresses, and installation are tasks, not promotional cards.
- **Admin:** optimize for scanning and repeated action: compact sidebar, clear tables, consistent forms, explicit save state, and no marketing-style hero composition.
- **Value-proposition cards:** 4-column desktop / 2 tablet / 1 mobile, white card, warm icon circle, `accent-italic` heading, muted body, `4px` hover lift into `shadow-lg`.
- **Footer:** white background, soft border, warm-brown headings, muted body, bottom bar with copyright + credit.
- **Documents and guides:** Markdown-backed pages use the same light canvas and a constrained reading column. The page header is centered and quiet; the content surface is white with a single soft border, 14px-20px radius, and `shadow-sm`. Use green `h2` headings, muted body text at 1.6-1.7 line-height, generous section spacing, and orange only for CTAs/eyebrows. Documentation indexes use a two-column desktop grid and one-column mobile layout with clear titles, summaries, and a visible `Read guide` action. Do not render legal or customer documentation as long unstructured text or nested cards.

## Do's and Don'ts

- **Do** keep the canvas warm (`surface` / `surface-container`) and reserve pure white for cards, inputs, and modals so they read as raised.
- **Do** let a hover state move exactly one elevation step and/or lift by a few pixels -- nothing more theatrical.
- **Do** use `accent-italic` (Playfair Display) sparingly, as a decorative highlight.
- **Do** keep shape consistent per component family (see Shapes).
- **Don't** use uppercase or letter-spacing on buttons or headings -- reserve it for short operational labels only.
- **Don't** add glow, colored blur, or stacked shadow tiers on flat cards. The hero band's atmospheric gradient and dot texture are the single allowed decorative-depth treatment; keep it to the home hero and `hero-band` interior pages only.
- **Don't** use decorative gradients, oversized pills, or large-radius containers to manufacture hierarchy. Use spacing, typography, borders, and real media.
- **Don't** introduce a second frontend, second routing scheme, or a component library that bypasses the existing PHP templates.
- **Don't** copy reference-product navigation labels or routes that don't exist in this app -- keep the real routes.
- **Don't** let hover/focus states shift layout or reflow siblings.
- **Don't** hide essential content behind entrance animations. Motion is optional enhancement and must never control legibility.
- **Don't** use decorative gradients, oversized pills, or large-radius containers to manufacture hierarchy. Use spacing, typography, borders, and real media.
- **Do** keep policy, legal, customer, and internal guide content in Markdown/YAML sources and render it through the shared document surface so copy changes do not require template rewrites.

## Verification

- Check `/`, `/consult`, `/shop`, one product, `/login`, and authenticated account pages at 1440x1000 and 390x844 in a real browser.
- Confirm image crops, active navigation, focus states, card alignment, no hidden reveal content, no horizontal overflow, and that the next section is hinted in the first mobile viewport.
- Use the fixed development customer created by `bapXaura dev:user`; its password must come from `BAPX_TEST_USER_PASSWORD` and must never be committed.
- Run the repo's PHP tests, project-map validation, and local smoke test before commit or push.

## Implementation Notes

- Tokens above are also expressed as CSS custom properties in `assets/css/band.css` (`:root`). The semantic names here (`primary`, `on-primary`, `primary-container`, etc.) exist there as aliases (`--color-primary`, `--color-on-primary`, `--color-primary-container`, ...) layered directly on top of the original hue-named variables (`--color-maroon`, `--color-gold`, ...) -- both are safe to use; new code should prefer the semantic names.
- Elevation tokens: `--shadow-sm/md/lg/xl` plus `--shadow-focus` for the single focus-ring definition.
- Motion: `--transition-spring` (a restrained expressive-motion curve, `cubic-bezier(0.34, 1.4, 0.64, 1)`) is used for hover lifts on buttons and cards; `--transition-base` still governs color/shadow fades.
- This file can be linted against the open spec with `npx @google/design.md lint Design.md` if you want machine validation of token references and section order.
