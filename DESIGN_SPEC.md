# InterAgents.ai — Custom WordPress Theme Design Spec

## Brand
- **Company:** InterAgents.ai
- **Business:** Creating AI agents and building platforms that connect multiple systems together
- **Logo colors:** Dark text with warm gold/amber accent on "agents" + connected nodes icon
- **Language:** Polish (primary), English (future)

## Design Direction
- Modern, clean, tech-forward
- Dark sections with gradient accents (deep navy/charcoal + warm gold/amber highlights)
- Mobile-first responsive
- Smooth scroll animations (CSS + minimal JS)
- Clean typography (Inter or similar sans-serif from Google Fonts)

## Color Palette
- Primary dark: #0a0a1a (near-black navy)
- Secondary dark: #1a1a2e  
- Accent gold: #c8963e (from logo)
- Accent light: #e8b85e
- Text light: #f0f0f0
- Text muted: #8892a0
- White: #ffffff
- Card bg: #16162a

## Typography
- Headings: Inter, 700/800 weight
- Body: Inter, 400 weight
- Large hero text, generous spacing

## Page Structure (Single Page)

### 1. Navigation (sticky)
- Logo left, nav links right
- Hamburger on mobile
- Transparent on hero, solid on scroll

### 2. Hero Section
- Full viewport height
- Headline: "Tworzymy inteligentne rozwiązania AI"
- Subtitle: "Projektujemy agentów AI i budujemy platformy łączące Twoje systemy w jedną, spójną całość."
- CTA button: "Porozmawiajmy" (gold accent)
- Subtle animated gradient background or particle effect (CSS only)

### 3. Usługi (Services) Section
Three cards with icons (SVG placeholders):
- **Agenci AI** — "Projektujemy i wdrażamy inteligentnych agentów AI, którzy automatyzują procesy i wspierają Twój zespół."
- **Integracja Systemów** — "Łączymy Twoje narzędzia i systemy w jedną, wydajną platformę. API, automatyzacje, przepływ danych."  
- **Rozwiązania na Miarę** — "Każdy biznes jest inny. Tworzymy dedykowane rozwiązania dopasowane do Twoich potrzeb."

### 4. Jak Działamy (How We Work) Section
Three steps with numbered circles:
1. **Analiza** — "Poznajemy Twój biznes, procesy i potrzeby"
2. **Projektowanie** — "Tworzymy architekturę i prototyp rozwiązania"
3. **Wdrożenie** — "Implementujemy, testujemy i uruchamiamy"

### 5. Dlaczego My (Why Us) Section
- Grid of 4 features with icons
- "Doświadczenie w AI", "Nowoczesne technologie", "Indywidualne podejście", "Wsparcie po wdrożeniu"

### 6. CTA Section
- Bold statement + contact button
- "Gotowy na transformację?" / "Skontaktuj się z nami"

### 7. Footer
- Logo, copyright, social links placeholders
- "© 2025 InterAgents.ai"

## Technical Requirements
- Custom WordPress theme (NOT a child theme of Divi — standalone)
- Clean PHP, semantic HTML5
- CSS custom properties for theming
- CSS Grid + Flexbox
- Intersection Observer for scroll animations
- No jQuery dependency
- Responsive breakpoints: 480px, 768px, 1024px, 1280px
- Fast: inline critical CSS, defer non-critical
- WordPress customizer support for logo/text changes
- wp_enqueue for all assets

## File Structure
```
interagents-theme/
├── style.css (theme header + critical styles)
├── functions.php
├── index.php
├── front-page.php (main landing page template)
├── header.php
├── footer.php
├── assets/
│   ├── css/
│   │   └── main.css
│   ├── js/
│   │   └── main.js
│   └── img/
│       └── (placeholder SVGs)
├── inc/
│   └── customizer.php
└── screenshot.png (can be placeholder)
```
