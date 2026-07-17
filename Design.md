---
version: alpha
name: AuraEdu — Dual Design System
description: "Two distinct design languages: elevenlabs (frontend: editorial off-white, serif display, Inter body, pastel gradient orbs) and linear (admin: near-black canvas, lavender-blue accent, charcoal surfaces, sans-serif)."
---

This document defines two design systems that share the same codebase:

- **elevenlabs** — Frontend/public-facing pages. Editorial magazine feel: off-white canvas, warm near-black ink, Waldenburg Light (300 weight) serif for display, Inter for body, pastel atmospheric gradient orbs.
- **linear** — Admin panel. Product-craft feel: near-black canvas (#010102), lavender-blue accent (#5e6ad2), four-step charcoal surface ladder, Inter/SF Pro sans-serif throughout.

---

# ElevenLabs — Frontend Design System

> Off-white canvas, warm near-black ink, editorial serif display, Inter body, pastel gradient orbs.

## Overview

The frontend reads like a quietly editorial print magazine. Base canvas is off-white (`#f5f5f5`) holding warm near-black ink (`#0c0a09`). Brand voltage is **photographic, not chromatic**: soft pastel atmospheric gradient orbs (mint, peach, lavender, sky, rose) drift through the page as the only "color" moments. No neon accent, no saturated CTA color, no dark-canvas dev-tools atmosphere.

Type pairs **Waldenburg Light** (custom serif at weight 300) for display with **Inter** for body, navigation, captions. Fallback: `'Times New Roman', serif` for Waldenburg, `Inter, sans-serif` for body.

CTAs are subtle: a warm near-black ink pill is the primary, a transparent outline is secondary. The brand trusts atmospheric photography and modest type weights to carry brand work.

## Colors

### Brand & Accent
| Token | Value | Usage |
|---|---|---|
| `primary` | `#292524` | Warm near-black — primary CTA pill |
| `primary-active` | `#0c0a09` | Press state |

### Surface
| Token | Value | Usage |
|---|---|---|
| `canvas` | `#f5f5f5` | Off-white page floor |
| `canvas-soft` | `#fafafa` | Lighter alternating sections |
| `canvas-deep` | `#0c0a09` | Dark hero (rare) |
| `surface-card` | `#ffffff` | Pure white cards |
| `surface-strong` | `#f0efed` | Badges, icon plates |
| `surface-dark` | `#0c0a09` | Dark hero/CTA canvas |
| `surface-dark-elevated` | `#1c1917` | Cards on dark canvas |

### Hairlines
| Token | Value | Usage |
|---|---|---|
| `hairline` | `#e7e5e4` | Default 1px divider |
| `hairline-soft` | `#f0efed` | Lighter divider |
| `hairline-strong` | `#d6d3d1` | Stronger panel outline |

### Text
| Token | Value | Usage |
|---|---|---|
| `ink` | `#0c0a09` | Display, primary text |
| `body` | `#4e4e4e` | Default running-text |
| `muted` | `#777169` | Sub-titles |
| `muted-soft` | `#a8a29e` | Disabled text |

### Atmospheric Gradient Stops (signature decoration)
| Token | Value |
|---|---|
| `gradient-mint` | `#a7e5d3` |
| `gradient-peach` | `#f4c5a8` |
| `gradient-lavender` | `#c8b8e0` |
| `gradient-sky` | `#a8c8e8` |
| `gradient-rose` | `#e8b8c4` |

These appear ONLY as soft radial-gradient atmospheric orbs. Never as button fills or text colors.

## Typography

| Token | Size | Weight | Line Ht | Letter Spc | Use |
|---|---|---|---|---|---|
| `display-mega` | 64px | 300 | 1.05 | -1.92px | Homepage hero h1 |
| `display-xl` | 48px | 300 | 1.08 | -0.96px | Subsidiary heroes |
| `display-lg` | 36px | 300 | 1.17 | -0.36px | Section heads |
| `display-md` | 32px | 300 | 1.13 | -0.32px | Sub-section heads |
| `display-sm` | 24px | 300 | 1.2 | 0 | Card group titles |
| `title-md` | 20px | 500 (Inter) | 1.35 | 0 | Component titles |
| `body-md` | 16px | 400 (Inter) | 1.5 | 0.16px | Default body |
| `body-sm` | 15px | 400 (Inter) | 1.47 | 0.15px | Footer body |
| `caption` | 14px | 400 (Inter) | 1.5 | 0 | Photo captions |
| `caption-uppercase` | 12px | 600 (Inter) | 1.4 | 0.96px | Section labels |
| `button` | 15px | 500 | 1.0 | 0 | CTA pill |

**Font Family**: Waldenburg Light (serif, weight 300) for display; Inter for body/nav/buttons/captions.

## Components

- **Button primary**: Near-black pill (`#292524`), white text, `rounded.pill`, 15px/500, padding 10px×20px, height 40px.
- **Button outline**: Transparent pill, 1px ink border, same size.
- **Hero band**: Off-white canvas, display-mega headline, subhead in body-md, atmospheric gradient orb behind centered copy.
- **Gradient orb card**: Large card (`rounded.xxl` 24px) with soft radial gradient using one of the 5 gradient colors.
- **Feature card**: White (`surface-card`), `rounded.xl` (16px), padding 24px, 1px hairline.
- **Text input**: White bg, `rounded.md` (8px), height 44px, 1px strong hairline.

## Shape Scale

| Token | Value | Use |
|---|---|---|
| `rounded.md` | 8px | Form inputs |
| `rounded.lg` | 12px | Compact cards |
| `rounded.xl` | 16px | Feature cards |
| `rounded.xxl` | 24px | Gradient orb cards |
| `rounded.pill` | 9999px | All CTAs, badges |

## Spacing

Base unit 4px. Section rhythm: 96px. Card padding: 24px.

---

# Linear — Admin Design System

> Near-black canvas (#010102), lavender-blue accent, four-step charcoal surface ladder, Inter/SF Pro sans-serif throughout.

## Overview

Linear's marketing canvas is the deepest dark surface — canvas is #010102 (near-pure black with faint blue tint). On top sits a four-step surface ladder for cards, panels, and lifted tiles, with hairline borders. Light gray text (`#f7f8f8`) carries the body and headlines.

The single chromatic accent is **lavender-blue** (`#5e6ad2`) — used on the brand mark, focus rings, and primary CTA button. No saturated greens, oranges, or reds.

Display type runs Inter/SF Pro Display at weight 500–600 with negative tracking. Body is Inter at 400.

Page rhythm is **dense product screenshots** — admin captures of the product UI framed in surface-1 panels.

## Colors

### Brand & Accent
| Token | Value | Usage |
|---|---|---|
| `primary` | `#5e6ad2` | Primary CTA, brand mark, focus rings |
| `primary-hover` | `#828fff` | Hovered CTA |
| `primary-focus` | `#5e69d1` | Focus ring tint |

### Surface (four-step ladder)
| Token | Value | Usage |
|---|---|---|
| `canvas` | `#010102` | Page bg (near-pure black) |
| `surface-1` | `#0f1011` | Cards, panels |
| `surface-2` | `#141516` | Featured cards, hovered |
| `surface-3` | `#18191a` | Sub-nav, dropdowns |
| `surface-4` | `#191a1b` | Deepest lifted |

### Hairlines
| Token | Value |
|---|---|
| `hairline` | `#23252a` |
| `hairline-strong` | `#34343a` |
| `hairline-tertiary` | `#3e3e44` |

### Text
| Token | Value | Usage |
|---|---|---|
| `ink` | `#f7f8f8` | Primary headlines, body |
| `ink-muted` | `#d0d6e0` | Secondary text |
| `ink-subtle` | `#8a8f98` | Tertiary text, placeholders |
| `ink-tertiary` | `#62666d` | Disabled, footnotes |

## Typography

| Token | Size | Weight | Line Ht | Letter Spc | Use |
|---|---|---|---|---|---|
| `display` | 80px | 600 | 1.05 | -3.0px | Largest hero |
| `display-lg` | 56px | 600 | 1.10 | -1.8px | Section openers |
| `display-md` | 40px | 600 | 1.15 | -1.0px | Sub-section heads |
| `headline` | 28px | 600 | 1.20 | -0.6px | Pricing titles |
| `card-title` | 22px | 500 | 1.25 | -0.4px | Card titles |
| `subhead` | 20px | 400 | 1.40 | -0.2px | Lead body |
| `body` | 16px | 400 | 1.50 | -0.05px | Default body |
| `body-sm` | 14px | 400 | 1.50 | 0 | Card body, footer |
| `caption` | 12px | 400 | 1.40 | 0 | Status, meta |
| `button` | 14px | 500 | 1.20 | 0 | Button labels |
| `mono` | 13px | 400 | 1.50 | 0 | Code |

## Components

- **Button primary**: Lavender (`#5e6ad2`), white text, `rounded.md` (8px), padding 8px×14px.
- **Button secondary**: Charcoal (`surface-1`), ink text, 1px hairline, same size.
- **Button tertiary**: Transparent, ink-muted text.
- **Sidebar nav**: Fixed 240px, surface-1 bg, hairline right border. Links at 13.5px/450 weight, ink-muted. Active: surface-2 bg, ink text.
- **Cards**: surface-1 bg, 1px hairline, `rounded.lg` (12px), padding 24px.
- **Table**: Full-width, surface-1 header, hairline rows, ink-muted cells. Hover: surface-2.
- **Form input**: surface-1 bg, 1px hairline, `rounded.md` (8px), ink text. Focus: primary border + glow.
- **Stat card**: surface-1 bg, 1px hairline, `rounded.lg`. Value at 28px/600 weight.
- **Badge**: Pill (`rounded.pill`), surface-2 bg, ink-muted text, 11.5px.

## Shape Scale

| Token | Value | Use |
|---|---|---|
| `rounded.sm` | 6px | Inline tags |
| `rounded.md` | 8px | All buttons, inputs |
| `rounded.lg` | 12px | All cards |
| `rounded.xl` | 16px | Screenshot panels |
| `rounded.pill` | 9999px | Tabs, badges |

## Spacing

Base unit 4px. Card interior: 24px. Section: 96px. Content padding: 32px.

---

## Implementation

- Admin CSS: `assets/css/admin.css` (Linear design system)
- Frontend CSS: `assets/css/band.css` (ElevenLabs design system)
- Admin layout: `views/layouts/admin.php`
- Frontend layout: `views/layouts/app.php`
- This file can be linted with `npx @google/design.md lint Design.md`
