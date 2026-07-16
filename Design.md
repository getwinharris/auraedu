---
version: alpha
name: AuraEdu (bapXaura design language)
description: Design token system and visual contract for Aura Medical Institute of Electropathy and Hospital.
colors:
  primary: "#000000"
  primary-hover: "#333333"
  accent: "#00D4A4"
  accent-hover: "#00B88E"
  accent-soft: "#E6F9F4"
  canvas: "#FFFFFF"
  canvas-dark: "#0D0D0D"
  surface: "#F6F6F6"
  surface-soft: "#FBFBFB"
  border: "#E5E5E5"
  border-light: "#EEEEEE"
  border-dark: "#333333"
  ink: "#000000"
  ink-muted: "#333333"
  text-muted: "#666666"
  text-light: "#888888"
  text-soft: "#999999"
  text-disabled: "#B0B0B0"
  on-dark: "#FFFFFF"
  on-dark-muted: "rgba(255,255,255,0.6)"
  success: "#00D4A4"
  success-soft: "#E6F9F4"
  warning: "#E8A317"
  warning-soft: "#FFF8E7"
  error: "#DC3545"
  error-soft: "#FDE8E9"
  info: "#3772CF"
  info-soft: "#EBF0FA"
  orange: "#FF6B4A"
  hero-dark-from: "#0A2E36"
  hero-dark-to: "#1A6B5A"
  hero-sky-from: "#B8D4F0"
  hero-sky-to: "#FFF8E7"
typography:
  display:
    fontFamily: Inter
    fontSize: 40px
    fontWeight: 600
    lineHeight: 1.1
    letterSpacing: -1px
  heading-1:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: 600
    lineHeight: 1.15
    letterSpacing: -0.5px
  heading-2:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: 600
    lineHeight: 1.25
    letterSpacing: 0
  heading-3:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: 600
    lineHeight: 1.3
    letterSpacing: 0
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  label:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: 500
    lineHeight: 1.4
    letterSpacing: 0
  label-bold:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 0
  micro:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: 500
    lineHeight: 1.4
    letterSpacing: 0
  micro-uppercase:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 0.5px
  button:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: 500
    lineHeight: 1.3
    letterSpacing: 0
rounded:
  xs: 4px
  sm: 6px
  md: 8px
  lg: 12px
  xl: 16px
  xxl: 24px
  full: 9999px
spacing:
  xxs: 4px
  xs: 8px
  sm: 12px
  md: 16px
  lg: 20px
  xl: 24px
  xxl: 32px
  xxxl: 40px
  section-sm: 48px
  section: 64px
  section-lg: 96px
  hero: 120px
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-dark}"
    rounded: "{rounded.full}"
    padding: 8px 20px
    height: 36px
    typography: "{typography.button}"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
  button-accent:
    backgroundColor: "{colors.accent}"
    textColor: "{colors.primary}"
    rounded: "{rounded.full}"
    padding: 8px 20px
    height: 36px
    typography: "{typography.button}"
  button-accent-hover:
    backgroundColor: "{colors.accent-hover}"
  button-outline:
    backgroundColor: transparent
    textColor: "{colors.ink}"
    border: 1px solid {colors.border}
    rounded: "{rounded.full}"
    padding: 8px 20px
    height: 36px
    typography: "{typography.button}"
  button-ghost:
    backgroundColor: transparent
    textColor: "{colors.text-muted}"
    rounded: "{rounded.md}"
    padding: 6px 12px
    typography: "{typography.button}"
  card-base:
    backgroundColor: "{colors.canvas}"
    rounded: "{rounded.lg}"
    padding: "{spacing.xl}"
    border: 1px solid {colors.border}
  card-surface:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.lg}"
    padding: "{spacing.xxl}"
  card-course:
    backgroundColor: "{colors.canvas}"
    rounded: "{rounded.lg}"
    padding: "{spacing.xl}"
    border: 1px solid {colors.border-light}
  text-input:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    border: 1px solid {colors.border}
    rounded: "{rounded.md}"
    padding: "{spacing.sm} {spacing.md}"
    height: 40px
  text-input-focus:
    border: 2px solid {colors.accent}
    shadow: "{colors.accent} 0 0 0 3px"
  search-pill:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-light}"
    rounded: "{rounded.md}"
    height: 36px
    typography: "{typography.body-sm}"
    border: 1px solid {colors.border}
---

## Overview

AuraEdu uses a clean, high-polish SaaS-calibre design language inspired by the interaction quality of modern developer platforms. The system prioritises typographic hierarchy, generous spacing, restrained accent colour, and flat-but-intentional elevation. Black and white form the structural canvas; mint green (`#00D4A4`) is the single accent reserved for CTAs and active-state indicators. Inter is the sole typeface — crisp, legible, one-family simplicity.

This document defines *how* the interface looks and behaves. It is not responsible for product content, business rules, course data, or application architecture.

## Visual Principles

- **One accent, one job.** Mint green appears only on primary or urgent CTAs, active nav underlines, focus rings, and checkmarks. If it appears on a background surface, it has not earned that role.
- **Flat by default, elevated by exception.** Cards sit on a white canvas separated by a hairline border and a barely-perceptible shadow. The hero band's atmospheric gradient is the single allowed decorative-depth treatment.
- **Typography is the hierarchy.** No decorative flourishes, no italic serif accents, no gradient underlines. Weight (400/500/600) and font-size step the hierarchy.
- **Buttons are always pills.** Every interactive button uses `rounded.full` (9999px). The pill is the system's unbroken rule.
- **Generous but disciplined spacing.** The 4px-base scale provides rhythm without surprise. Sections breathe at 64px padding; cards at 24px internal padding.

## Colors

### Brand & Accent

| Token | Value | Usage |
|---|---|---|
| `accent` | `#00D4A4` | Primary CTAs, active nav underline, focus rings, checkmarks, featured borders |
| `accent-hover` | `#00B88E` | Pressed/active accent state |
| `accent-soft` | `#E6F9F4` | Success confirmation surfaces, subtle highlight |

### Surface

| Token | Value | Usage |
|---|---|---|
| `canvas` | `#FFFFFF` | Page background, card backgrounds |
| `canvas-dark` | `#0D0D0D` | Dark inversion surfaces (promo banners, dark hero footer band) |
| `surface` | `#F6F6F6` | Subtle section backgrounds, search-pill rest, code-inline bg |
| `surface-soft` | `#FBFBFB` | Quieter section variants |
| `border` | `#E5E5E5` | 1px borders, primary dividers |
| `border-light` | `#EEEEEE` | Quieter dividers, secondary separators |

### Text

| Token | Value | Usage |
|---|---|---|
| `ink` | `#000000` | Primary headlines, strong body |
| `ink-muted` | `#333333` | Secondary headings |
| `text-muted` | `#666666` | Body text, metadata |
| `text-light` | `#888888` | Secondary text, footer links |
| `text-soft` | `#999999` | Captions, placeholders |
| `text-disabled` | `#B0B0B0` | Disabled labels |
| `on-dark` | `#FFFFFF` | Text on dark backgrounds |
| `on-dark-muted` | `rgba(255,255,255,0.6)` | Reduced-opacity white |

### Semantic

- Success uses `accent` values. Warning uses `#E8A317`. Error uses `#DC3545`. Info uses `#3772CF`.

## Typography

### Font Family

**Inter** is the single typeface for every surface — headings, body, navigation, buttons, labels, captions. No second typeface enters the system. Emphasis is carried by weight (500/600), size, and colour — never by italic.

### Hierarchy

| Token | Size | Weight | Line Height | Letter Spacing | Use |
|---|---|---|---|---|---|
| `display` | 40px | 600 | 1.1 | -1px | Hero H1 display, section openers |
| `heading-1` | 32px | 600 | 1.15 | -0.5px | Page-level headlines |
| `heading-2` | 24px | 600 | 1.25 | 0 | Section headlines |
| `heading-3` | 20px | 600 | 1.3 | 0 | Subsection / card titles |
| `body-md` | 16px | 400 | 1.5 | 0 | Primary body text |
| `body-sm` | 14px | 400 | 1.5 | 0 | Secondary body, nav, metadata |
| `label` | 13px | 500 | 1.4 | 0 | Labels, captions |
| `label-bold` | 13px | 600 | 1.4 | 0 | Badge labels, emphasis |
| `micro` | 12px | 500 | 1.4 | 0 | Footer, helper text |
| `micro-uppercase` | 11px | 600 | 1.4 | 0.5px | Section headers, category labels |
| `button` | 14px | 500 | 1.3 | 0 | Pill button labels |

## Layout

### Spacing System

Base unit: 4px, primary increment 8px. Section rhythm uses `section` (64px) between major bands and `section-sm` (48px) between compact sections. Cards use `xl` (24px) internal padding.

### Container

Content max-width: 1280px with 24px gutters (`.container`). Wide variant: 1440px. Narrow variant: 800px for prose.

### Whitespace

The interface breathes. Hero bands get `hero` (120px) vertical padding. Content sections get `section` (64px). Cards and inputs are compact — elevation and colour do the separation, not excessive padding.

## Elevation & Depth

Flat by default. The system runs predominantly flat with strategic atmospheric depth.

| Level | Treatment | Use |
|---|---|---|
| 0 (flat) | No shadow; hairline border | Default cards, table rows, form inputs |
| 1 (subtle) | `0 1px 2px rgba(0,0,0,0.04)` | Hover-elevated tiles |
| 2 (card) | `0 4px 12px rgba(0,0,0,0.08)` | Standard feature cards, raised buttons |
| 3 (hero) | `0 24px 48px -8px rgba(0,0,0,0.12)` | Hero image frame, featured showcase |
| 4 (brand) | `0 8px 24px rgba(0,212,164,0.08)` | Featured card highlight |

### Decorative Depth

The hero band uses atmospheric gradient + layered radial glows + 26px dot texture. This is the single allowed decorative-depth treatment. No other page receives glow, blur-heavy shadows, or coloured elevation.

## Shapes

### Border Radius Scale

| Token | Value | Use |
|---|---|---|
| `xs` | 4px | Chips, small tags |
| `sm` | 6px | Sidebar nav items, type badges |
| `md` | 8px | Inputs, search pill, code blocks, secondary cards |
| `lg` | 12px | Standard cards, pricing/course tiers, hero mockup, FAQ items |
| `xl` | 16px | Larger feature panels, media frames |
| `xxl` | 24px | Featured showcase tiles |
| `full` | 9999px | Every button, pill tabs, badges |

The radius scale is disciplined: buttons always use `full`. Cards use `lg` (12px). Inputs use `md` (8px). No softening between `md` and `lg` for the same component family.

## Components

### Buttons

Every button is a pill (`rounded.full`). Heights are 36px (default), 32px (small), 44px (large). No uppercase, no letter-spacing.

- **`.btn-primary`** — Black pill, white text. The dominant action.
  - Hover: dark grey `#333333`
  - Active: `scale(0.97)`
  - Focus: `3px rgba(0,212,164,0.3)` ring
- **`.btn-accent`** — Mint pill, black text. For brand-emphasis CTAs on light or dark bands.
  - Hover: `#00B88E`
- **`.btn-outline`** — Transparent, 1px hairline border. Secondary actions.
  - Hover: mint border + text
- **`.btn-ghost`** — No border, rectangular (`rounded.md`). Tertiary/sidebar actions.
  - Hover: `surface` bg

### Header

Sticky white bar, 64px height. White background (`rgba(255,255,255,0.98)`), 1px `border-light` bottom border. Primary nav links are `text-muted` on rest, `ink` on hover/active. Active link has a 2px `accent` bottom underline with `border-radius: 2px`. Logo/brand on the left, nav centered, actions (cart/account) right. Mobile: hamburger toggle below 768px.

### Navigation

- Desktop nav uses `body-sm` (14px), 500 weight, `text-muted` → `ink` on hover
- Active page uses accent underline (2px)
- Dropdowns: white, 8px radius, hairline border, `shadow-md`, hover items get `surface` background
- Bottom nav (mobile): 5-icon row, `text-muted` icons, active = `accent`

### Hero Band

Two-colour atmospheric gradient band for the home page and interior section headers.

- **Home hero** (`.home-hero`): Dark teal→mint (`#0A2E36` → `#1A6B5A`), 90vh min-height, 2-column split (copy left, image frame right). Gold 3px top accent line. Layered radial glows + 26px dot texture. White `display` headline, `body-md` lede at 85% white opacity, mint eyebrow (`accent`).
- **Interior hero band** (`.hero-band`): Same dark teal→mint gradient, `section` padding, `display` heading, mint eyebrow, white lede at 85% opacity.

### Image Frame

Hero features an `aspect-ratio: 4/5` framed image panel:
- `rounded.lg` (12px) corners
- 1px white hairline border at 25% opacity
- Deep diffuse shadow: `0 24px 48px -8px rgba(0,0,0,0.35)`
- `object-fit: cover` so it reads as a featured panel

### Footer

White canvas background, 1px `border` top separator. Steel-coloured (`text-light`) link text, `ink` headings, `text-soft` copyright bar. 4-column grid (brand + 3 link columns). Clean, quiet, no decorative treatments.

### Cards

- **`.card-base`**: White, `rounded.lg` (12px), 1px `border`, `xl` padding, `shadow-sm` on rest, `shadow-md` on hover.
- **`.card-course`**: White, `rounded.lg` (12px), 1px `border-light`, `xl` padding.
- **`.card-surface`**: `surface` bg, `rounded.lg` (12px), `xxl` padding.

### Inputs & Forms

- Default: white bg, 1px `border`, `rounded.md` (8px), 40px height, `sm`/`md` padding
- Focus: 2px `accent` border, 3px `accent` glow ring
- Labels: `label` (13px, 500 weight)
- Errors: `error` border + `error-soft` background

### Badges & Status

- **Pill badges**: `rounded.full`, `micro-uppercase` or `label-bold` typography
- **Status pills**: Small `xs`/`sm` radius, coloured background tint (e.g. `success-soft` for "Active")

## Do's and Don'ts

### Do
- Reserve `accent` (mint green) for CTAs and active-state indicators only
- Use `primary` (black) as the dominant CTA on light backgrounds
- Apply `rounded.full` to every button — never soften pill corners
- Use Inter consistently on every surface — no second typeface
- Apply `rounded.lg` (12px) consistently on cards; use `rounded.md` (8px) only on inputs and compact UI
- Keep body typography at 16px with 1.5 line-height — never compress below 1.5
- Let the hero gradient breathe — no competing accents inside the band
- Use atmospheric gradient hero bands only on the home hero and interior section headers

### Don't
- Don't use `accent` on body text or large surfaces — it loses signal
- Don't introduce additional accent colours beyond mint
- Don't apply heavy shadows on flat cards; reserve elevation for the hero image frame
- Don't reduce body line-height below 1.5
- Don't combine atmospheric gradients with multiple competing colour accents
- Don't use uppercase or letter-spacing on buttons
- Don't use italic on UI text
- Don't hide essential content behind entrance animations

## Responsive Behavior

### Breakpoints

| Name | Width | Key Changes |
|---|---|---|
| Mobile (small) | < 480px | Single column. Hero scales to 28px display. Nav collapses to hamburger. Footer 1-column. |
| Mobile (large) | 480–767px | Feature tiles 2-up. Hero display at 32px. |
| Tablet | 768–1023px | 2-column grids. Nav returns. Hero display at 36px. |
| Desktop | 1024–1279px | Full layout. Hero display at 40px. |
| Wide | ≥ 1280px | Wider gutters, larger hero image. |

### Touch Targets
- Pill buttons: 36–40px height → 44px on mobile
- Icon buttons: 32×32px → 44×44px on mobile
- Form inputs: 40px → 44px on mobile

### Collapsing Strategy
- **Header nav**: below 768px collapses to hamburger drawer
- **Hero band**: 2-column (text + image) → stacked at < 768px
- **Course cards**: 3-column → 2-column → 1-column
- **Footer**: 4-column → 2-column → 1-column
- **Hero typography**: `display` (40px) → 36px tablet → 32px mobile-large → 28px mobile-small

## Iteration Guide

1. Focus on one component at a time. The system has high internal consistency.
2. Reference tokens directly (`color-accent`, `rounded.full`, `font-main`).
3. Add new variants as separate component entries (e.g. `-pressed`, `-disabled`).
4. Default to `body-md` (16px) for body. Headlines step down `display → heading-1 → heading-2 → heading-3`.
5. Keep accent confined to accent moments. If it appears on a generic surface, ask whether it earned that role.
6. Pill-shaped buttons always; squared buttons signal "third-party widget."
7. Body prose belongs at 16px with 1.5 line-height — anything denser breaks reading.
8. Run visual regression on home, a course page, shop, and a content page at desktop and mobile widths before shipping.

## Implementation Notes

- Tokens are expressed as CSS custom properties in `assets/css/band.css` (`:root`).
- Semantic names (`color-primary`, `color-accent`, `color-canvas`) mirror the YAML tokens.
- Elevation tokens: `shadow-sm/md/lg/xl/focus/brand`.
- Motion: `transition-base` (0.2s ease) for colour/opacity; `transition-spring` (0.35s cubic-bezier(0.34, 1.4, 0.64, 1)) for hover lifts.
- Inline critical CSS in `views/layouts/app.php` mirrors the `:root` tokens for first-paint consistency.
- This file can be linted with `npx @google/design.md lint Design.md` to validate token references and section order.
