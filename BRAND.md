# InterAgents.ai — Brand Guidelines

## Logo

The logo is **typographic** — no icon, no symbol. Just the name, styled.

```
inter agents .ai
```

- **"inter"** — light white
- **"agents"** — gold accent (gradient)
- **".ai"** — light white

The CSS text version is the **primary logo**. Use it everywhere on web.
The PNG (transparent background) is for **offline use** — decks, print, social, email signatures.

### Logo Files
- `interagents-logo-transparent.png` — 1991×349px, transparent background

### Logo Don'ts
- Don't change the colors
- Don't add an icon or symbol
- Don't use a different font
- Don't separate "inter" from "agents"

---

## Typography

### Primary Font
**Inter** — [rsms.me/inter](https://rsms.me/inter)  
Open source, Google Fonts available.

| Usage | Weight | Size |
|-------|--------|------|
| Logo | Bold (700) | — |
| Headings | ExtraBold (800) | 28–62px (clamp) |
| Subheadings | SemiBold (600) | 16–20px |
| Body text | Regular (400) | 15–16px |
| Labels / Small | Medium (500) | 12–14px |
| Buttons | Bold (700) | 15px |

### Loaded Weights
400, 500, 600, 700, 800

---

## Colors

### Core Palette

| Name | Hex | Usage |
|------|-----|-------|
| **Background** | `#0a0a1a` | Page background |
| **Background 2** | `#1a1a2e` | Section alternates, gradients |
| **Card** | `#16162a` | Cards, elevated surfaces |
| **Gold (primary)** | `#c8963e` | Primary accent, buttons, links |
| **Gold (light)** | `#e8b85e` | Gradient end, hover states |
| **Text** | `#f0f0f0` | Primary text |
| **Muted** | `#a0a0b8` | Secondary text, subtitles |

### CSS Custom Properties
```css
:root {
  --color-bg:       #0a0a1a;
  --color-bg-2:     #1a1a2e;
  --color-card:     #16162a;
  --color-accent:   #c8963e;
  --color-accent-2: #e8b85e;
  --color-text:     #f0f0f0;
  --color-muted:    #a0a0b8;
}
```

### Gold Gradient (buttons, tagline)
```css
background: linear-gradient(180deg, #e8b85e, #c8963e);
```

### Text on Dark
- Primary text: `#f0f0f0` (near-white, not pure white)
- Secondary: `#a0a0b8` (muted lavender-gray)
- Never pure `#ffffff` — too harsh on dark backgrounds

---

## Spacing & Layout

| Token | Value |
|-------|-------|
| Container max-width | `1100px` |
| Gutter (page padding) | `clamp(18px, 5vw, 40px)` |
| Border radius (small) | `10px` |
| Border radius (medium) | `16px` |
| Section padding | `clamp(80px, 12vw, 140px)` vertical |

---

## Buttons

### Primary (Gold)
```css
border-radius: 999px;
border: 1px solid rgba(232, 184, 94, 0.65);
background: linear-gradient(180deg, #e8b85e, #c8963e);
color: #0b0b17;
font-weight: 700;
```

### Secondary (Ghost)
```css
border-radius: 999px;
border: 1px solid rgba(255, 255, 255, 0.15);
background: transparent;
color: #f0f0f0;
```

---

## Tone of Voice

### Professional → Brutal

Start professional, end aggressive. The copy should feel like a wake-up call.

**Do:**
- "AI that replaces your entire team"
- "Your competitors are already deploying this"
- "A cheap team is still a team. AI is a different league."

**Don't:**
- Generic corporate fluff
- "Leveraging synergies"
- Soft, apologetic language

### Key Phrases
- 🇵🇱 "AI, które zastępuje cały zespół"
- 🇬🇧 "AI that replaces your entire team"
- 🇵🇱 "Bez zwolnień lekarskich, bez błędów, bez limitu godzin"
- 🇬🇧 "No sick leave, no errors, no hour limits"

---

## Languages

- **Primary:** Polish (pl)
- **Secondary:** English (en)
- Auto-detected via browser `Accept-Language` header
- Manual toggle via 🇬🇧/🇵🇱 flag button
- Cookie `ia_lang` persists choice for 1 year
