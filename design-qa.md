# Design QA — interagents.ai mobile-first editorial refresh

## Evidence

- Source visual truth: `/Users/molty/.codex/generated_images/019f52d8-d6ff-72e2-88fe-94c306b22c34/exec-8c59c939-b0bb-4ab2-8aa9-95053b895b22.png`
- Full-view comparison: `/tmp/interagents-design-qa-comparison.png`
- Focused hero comparison: `/tmp/interagents-design-qa-focused-hero.png`
- Mobile comparison: `/tmp/interagents-design-qa-mobile.png`
- Desktop implementation views:
  - `/tmp/interagents-desktop-hero.png`
  - `/tmp/interagents-desktop-architecture.png`
  - `/tmp/interagents-desktop-manifesto.png`
  - `/tmp/interagents-desktop-process.png`
  - `/tmp/interagents-desktop-outcomes.png`
  - `/tmp/interagents-desktop-booking.png`
- Mobile implementation views:
  - `/tmp/interagents-mobile-hero.png`
  - `/tmp/interagents-mobile-architecture.png`
  - `/tmp/interagents-mobile-booking.png`

## Viewport and state

- Primary desktop comparison: 1440 × 900, Polish homepage, dark theme, default state.
- Primary mobile comparison: 390 × 844, Polish homepage, default hero/architecture/booking states.
- Responsive checks: 320 × 568, 360 × 800, 390 × 844, and 1440 × 900.
- Offer checks: Polish and English, `product=intercore`, plus legacy `product=complete` normalization.
- Booking interaction: English, available date and slot selected, required fields completed with local test data, consent checked, local success state reached.

## Full-view comparison

The selected direction and implementation were placed together in `/tmp/interagents-design-qa-comparison.png`. The implementation preserves the target's bold editorial typography, navy/gold palette, compact hero, provocative contrast band, unboxed delivery rail, concise result rows, and booking-led finish. The product diagram intentionally differs from the mock: it shows standalone `interagents` beside a larger `intercore` container with an included `interagents` layer. This is a deliberate correction from the user's product rule, not design drift.

## Focused comparison

The hero comparison in `/tmp/interagents-design-qa-focused-hero.png` verifies headline scale and wrapping, logo fidelity, CTA prominence, right-side fact rhythm, spacing, and the gold/white balance. Focused mobile evidence in `/tmp/interagents-design-qa-mobile.png` verifies the 390px hierarchy and the calendar's usable width. No other focused crop was necessary because the remaining sections use simple typography and dividers and are legible in the full-view comparison.

## Required fidelity surfaces

- Fonts and typography: existing Inter/system sans stack retained; display weights, line height, tracking, hierarchy, wrapping, and readable 14–16px body sizes match the selected direction. The desktop hero now wraps in the same two-line structure as the source.
- Spacing and layout rhythm: sections use distinct densities and compositions instead of repeated card grids. No horizontal overflow exists at any tested viewport. Section gaps are controlled and no large empty dead zones remain.
- Colors and visual tokens: near-black/navy surfaces, warm white text, muted secondary copy, and logo-derived gold are consistent. Contrast is clear across hero, manifesto, controls, and booking states.
- Image quality and asset fidelity: the supplied production logo asset is used directly in header and footer at its natural aspect ratio. No placeholder imagery, improvised SVG, emoji, or CSS-drawn brand asset remains on the public landing surface.
- Copy and content: visible `interagents`, `intercore`, and `interagents.ai` casing is lowercase. Filler copy and the third “complete” bundle are removed. Both languages state that `intercore` always includes `interagents`.
- Icons and controls: language and menu controls use explicit text labels; native calendar affordance is retained. Focus, selected slot, loading/status, consent, success, modal, and menu states remain clear.
- Accessibility: semantic headings, lists, definition lists, region labels, form labels, focus return, Escape close, `aria-expanded`, full slot interval labels, 44px+ controls, and reduced-motion behavior were checked.

## Comparison history

1. Earlier finding — P2: desktop hero wrapped the second sentence over two lines, making the hierarchy taller than the source.
   - Fix: reduced the maximum display size and expanded the heading measure.
   - Post-fix evidence: `/tmp/interagents-design-qa-focused-hero.png` shows the intended two-line desktop headline.

2. Earlier finding — P1: the header recreated the wordmark as styled text and used flag/glyph controls although the real logo asset existed.
   - Fix: used `interagents-logo-transparent.png` directly and replaced flags/hamburger drawings with explicit `EN`/`PL` and `Menu`/`Close` controls.
   - Post-fix evidence: all three frames in `/tmp/interagents-design-qa-mobile.png` show the final mobile header.

3. Earlier finding — P2: the mobile booking calendar lost width to nested padding and showed an overly tall two-column slot list at 360–390px.
   - Fix: removed the theme wrapper on mobile, tightened plugin padding, kept two columns at 320px, switched to three columns at 360–559px, and displayed start times visually while preserving full intervals for assistive technology.
   - Post-fix evidence: `/tmp/interagents-mobile-booking.png`; measured document width equals viewport width at 320, 360, and 390px.

4. Earlier finding — P2: booking feedback lived after the full form and could fall outside the mobile viewport.
   - Fix: separated slot and form status regions and placed them beside the relevant interaction.
   - Post-fix evidence: the local booking journey completed successfully and the success view replaced the form grid.

5. Earlier finding — P0: at 1120px the embedded booking widget inherited viewport-based minimum columns wider than its page column and could clip the form.
   - Fix: made the embedded grid container-aware, with a flexible 240px schedule track and a shrinkable form track above a 600px content threshold.
   - Post-fix evidence: at 1120px the widget grid measures 628px client/scroll width, the form measures 356px client/scroll width, and the document has no horizontal overflow.

6. Earlier finding — P1: the closed mobile menu remained in the keyboard sequence because opacity and pointer-events do not remove focusable links.
   - Fix: closed mobile navigation now receives `inert` and `aria-hidden`; opening or reaching the desktop breakpoint removes both attributes.
   - Post-fix evidence: closed-state attributes and open/close state transitions were verified in the browser.

7. Earlier finding — P1: the offer heading could begin beneath the fixed header around 768–949px.
   - Fix: all `.section--offer` instances now include the fixed-header offset, with admin-bar offsets and mobile refinements.
   - Post-fix evidence: at 768px the heading starts 46px below the fixed header and the document has no horizontal overflow.

8. Earlier finding — P2: malformed offer query parameters could create visual/pricing state mismatches, and configurator groups exposed only visual selection state.
   - Fix: URL values are allow-listed and normalized; toggle groups have accessible group labels and every toggle updates `aria-pressed`.
   - Post-fix evidence: invalid values normalized to `mac`, `managed`, and `included`; the corresponding buttons were the only pressed controls.

## Interaction and runtime checks

- Mobile menu opens, changes its visible label to `Close`, closes on Escape, and restores focus.
- Contact modal opens with focus on Close, closes on Escape, and returns focus to the trigger.
- Booking slot selection sets the selected state; required fields and consent work; local submission reaches the success state and exposes the ICS link.
- Legacy `/offer/?product=complete` becomes `/offer/?product=intercore`.
- Malformed configurator query values normalize to valid defaults without changing the calculated price semantics.
- English and Polish content, language metadata, and booking labels render correctly.
- Browser console errors/warnings: none.
- Local limitation: WPForms is not installed in WordPress Playground, so the alternate email form shortcode itself could not be rendered there; the modal shell and focus behavior passed.

## Production verification

- Deployed release: theme `2.4.1` and booking calendar `1.2.1` at `https://interagents.ai`.
- Polish production homepage at 390px: document and body scroll widths equal the viewport; hero sentence spacing measures 6px; product accents use logo gold; both offer buttons are 54.8px high; the month calendar enhances successfully; and booking slots form three equal columns.
- Polish production homepage at 320px: document and body scroll widths equal the viewport; the booking frame uses 304px safely; selectable day cells measure approximately 37.4 × 38px; and booking slots switch to two columns.
- English production homepage at 390px: product, outcomes, month navigation, step headings, metadata, and language controls render in English with no horizontal overflow.
- Production contact modal: English first-name and last-name labels render correctly; focus moves to Close, Escape closes the modal, and focus returns to the trigger.
- Production offer at 768px: the heading begins 47px below the fixed header, legacy `product=complete` normalizes to `product=intercore`, invalid options normalize to `mac`, `managed`, and `included`, and only those controls expose `aria-pressed="true"`.
- Production desktop at 1440px: the product cards are equal at 548 × 738.2px; the booking widget has equal 778px client/scroll width with 334.6px and 443.5px panes; and the document has no horizontal overflow.
- Production calendar interaction: July → August → July navigation passed; selecting 14 July loaded 12 available slots; Arrow Right moved the single roving tab stop to 15 July. No production booking was submitted.
- Production browser diagnostics contain no errors or warnings; only the standard WordPress jQuery Migrate informational log appears.

## Findings

No actionable P0, P1, or P2 findings remain.

## Follow-up polish

- The earlier P3 month-grid difference is resolved in booking calendar `1.2.1`; no follow-up polish remains from this iteration.

## Desktop feedback iteration — theme 2.3.3

- Source visual truth: `/var/folders/84/q7j29sc55ml3rx1jgs5yshlr0000gn/T/codex-clipboard-1aac45ed-a7b2-42a8-a719-09a76232a896.png`.
- Browser-rendered implementation evidence:
  - Hero: `/tmp/interagents-desktop-style-fix-hero.png`.
  - Product cards: `/tmp/interagents-desktop-style-fix-cards.png`.
  - Mobile regression: `/tmp/interagents-mobile-regression-2.3.3.png`.
- Combined full-view and focused comparison: `/tmp/interagents-desktop-style-fix-comparison.png`.
- Viewport/state: Polish homepage, desktop source at 1300 × 1122 and browser content viewport at 1300 × 1068; default hero state and architecture section scrolled into view after reveal animation completed.

### Comparison history

1. Earlier finding — P2: the white and gold hero headline lines touched visually because the two block spans had no separation and the heading used a `0.94` line height.
   - Fix: added a desktop-only `0.14em` margin before the second headline line.
   - Post-fix evidence: the measured inter-line gap is 10.08px at 1300px and 9.78px at the 760px desktop breakpoint; the focused hero frame in the combined comparison shows clear separation without changing the mobile headline.

2. Earlier finding — P2: `interagents` used only horizontal divider lines while `intercore` used a padded, rounded dark card, so the two product levels did not share the same visual system.
   - Fix: at desktop widths, `interagents` now receives the same 48px padding, 18px radius, dark surface, and full card geometry as `intercore`. Its neutral border remains intentionally quieter than the gold `intercore` border so the inclusion hierarchy is still visible.
   - Post-fix evidence: at 1300px both cards measure 609.625px high with identical padding, radius, and background. The focused architecture frame in the combined comparison shows the corrected parity.

### Regression checks

- At 390px, the desktop-only rules do not apply: the hero gap remains unchanged, `interagents` retains the compact divider treatment, and document/body scroll widths equal the viewport.
- At 760px and 1300px, document width equals viewport width and the corrected desktop styles apply without overflow.
- No browser console errors or warnings were recorded.
- Fonts/typography, color tokens, logo/image quality, and copy remain unchanged; only the requested spacing and desktop card surface were adjusted.
- Live production evidence: `/tmp/interagents-live-2.3.3-hero.png` and `/tmp/interagents-live-2.3.3-cards.png`.
- Production serves `style.css` and `main.css` with cache version `2.3.3`; the live 1300px metrics match local measurements, the live 390px mobile rules remain unchanged, and production console diagnostics contain no errors or warnings.

## Product clarity and booking iteration — theme 2.4.1 / calendar 1.2.1

### Source visual truth

- User product-section screenshot: `/var/folders/84/q7j29sc55ml3rx1jgs5yshlr0000gn/T/codex-clipboard-80720565-4cff-4ada-af8f-d3a6d4a15cba.png`.
- User manifesto screenshot: `/var/folders/84/q7j29sc55ml3rx1jgs5yshlr0000gn/T/codex-clipboard-9c4da70b-6268-4cf0-a308-5cb120e2927c.png`.
- Approved editorial/booking direction: `/Users/molty/.codex/generated_images/019f52d8-d6ff-72e2-88fe-94c306b22c34/exec-8c59c939-b0bb-4ab2-8aa9-95053b895b22.png`.
- Earlier mobile implementation evidence: `/tmp/interagents-mobile-hero.png` and `/tmp/interagents-mobile-booking.png`.

### Browser-rendered implementation evidence

- Desktop product section, 1440 × 900: `/tmp/interagents-2.4-desktop-architecture.png`.
- Complete desktop product cards, 1440 × 900: `/tmp/interagents-2.4-desktop-product-cards.png`.
- Desktop booking section, 1440 × 900: `/tmp/interagents-2.4-desktop-booking.png`.
- Mobile hero, products, intercore, outcomes, and booking at 390 × 844:
  - `/tmp/interagents-2.4-mobile-hero.png`.
  - `/tmp/interagents-2.4-mobile-architecture.png`.
  - `/tmp/interagents-2.4-mobile-products.png`.
  - `/tmp/interagents-2.4-mobile-intercore.png`.
  - `/tmp/interagents-2.4-mobile-outcomes.png`.
  - `/tmp/interagents-2.4-mobile-booking.png`.
- English mobile booking, 390 × 844: `/tmp/interagents-2.4-mobile-booking-en.png`.
- Smallest-width booking, 320 × 568: `/tmp/interagents-2.4-booking-320.png`.

### Combined comparison evidence

- Product before/after: `/tmp/interagents-2.4-product-comparison.png`.
- Approved booking direction vs implementation: `/tmp/interagents-2.4-calendar-comparison.png`.
- Mobile hero and booking before/after: `/tmp/interagents-2.4-mobile-comparison.png`.
- Removed manifesto vs operational outcomes: `/tmp/interagents-2.4-outcomes-comparison.png`; these frames intentionally use different crops because the requested result is removal rather than fidelity to the old section.

### Comparison history

1. Earlier finding — P1 content: the product cards described abstract architecture and generic components, leaving a new business owner unable to tell what work either product performs.
   - Fix: rewrote both languages around completed work, concrete example jobs, supervision, data scope, routing, approvals, and when to choose intercore. Replaced the tiny inclusion pill with a full-size execution-layer explanation.
   - Post-fix evidence: `/tmp/interagents-2.4-product-comparison.png` and the mobile product frames show concrete copy and the interagents-within-intercore relationship.

2. Earlier finding — P2 color/affordance: the architecture headline did not apply the agreed white `inter` plus gold `agents`/`core` treatment, and pricing links looked like tertiary text links.
   - Fix: built semantic inline brand words in the heading and converted both offer links into 48px+ gold-outline buttons that become full width on mobile.
   - Post-fix evidence: desktop and mobile architecture screenshots show the correct color system and button affordance.

3. Earlier finding — P2 typography: mobile hero wraps and the former outcomes heading used cramped display line spacing.
   - Fix: mobile hero line height is 1.02 with a 0.14em sentence gap; mobile section titles use 1.06 line height and the new two-line outcomes heading has an explicit 0.14em gap.
   - Post-fix evidence: `/tmp/interagents-2.4-mobile-comparison.png` and `/tmp/interagents-2.4-mobile-outcomes.png`.

4. Earlier finding — P1 layout/content: the manifesto occupied a full high-contrast section without adding product or conversion clarity.
   - Fix: removed its markup and CSS completely. Product architecture now flows directly into the three-step delivery section, followed by operational outcomes.
   - Post-fix evidence: the manifesto phrases and selectors are absent from source and rendered DOM; `/tmp/interagents-2.4-outcomes-comparison.png` records the replacement direction.

5. Earlier finding — P1 booking: the booking experience looked like a nested plugin with a native date input, multiple card frames, excessive padding, and weak continuity with the approved direction.
   - Fix: built a real localized six-week month grid with bounded text navigation, disabled workdays, selected/today states, roving keyboard focus, and unchanged REST slot/submission behavior. Flattened the visual treatment into one frame and one responsive divider.
   - Post-fix evidence: `/tmp/interagents-2.4-calendar-comparison.png`; complete local booking reached the success state and exposed the ICS link.

6. First post-build finding — P2 responsiveness: at 320px the initial month-grid cells measured roughly 33 × 34px.
   - Fix: let the calendar frame recover 24px of mobile width, reduced internal padding at 320px, and raised the day-cell height to 38px.
   - Post-fix evidence: the document remains exactly 320px wide, the widget is 302px wide without overflow, and selectable day cells measure approximately 37.4 × 38px.

### Required fidelity and regression checks

- Fonts/typography: existing Inter stack and weights retained; only requested headline rhythm changed. No clipping or broken Polish/English wrapping at 320, 360, 390, 760, 1120, or 1440px.
- Spacing/layout: equal product cards begin at 960px; below that they stack with the same surface treatment. Calendar switches by container width, not viewport width. No document, widget, grid, schedule, or form overflow was measured.
- Colors/tokens: `inter` remains warm white and `agents`/`core` use logo gold across headline, product names, and included execution layer. Booking uses the same navy, warm white, muted text, and gold selection/CTA tokens.
- Image quality/assets: the production logo remains the only image asset needed; no placeholder imagery, custom SVG, emoji, or CSS-drawn icon substitutes were introduced. Month navigation uses explicit localized text controls.
- Copy/content: filler manifesto and generic outcome language are removed. Both languages explain concrete work and preserve the rule that intercore includes interagents.
- Accessibility/behavior: month navigation is bounded and labeled, dates expose full localized labels plus selected/current/disabled states, keyboard arrows/Home/End/Page Up/Page Down move roving focus, slots expose full interval labels, native date fallback remains, and the complete local booking journey passed.
- Browser console errors/warnings: none.
- Measured desktop state at 1440px: both product cards are 548px wide and 738px high; the booking widget/grid client and scroll widths are both 778px with 334.6px and 443.5px panes.
- Measured mobile state: hero line height is 1.02 with a 6px sentence gap at 390px; outcomes line height is 1.06 with a 4.75px sentence gap; slots use three columns at 360px and two at 320px.
- Calendar semantics: one roving date tab stop, localized group/month/date labels, selected and current-date state, disabled weekends, and disabled previous navigation at the minimum month.

### Live production evidence

- Polish mobile hero at 390 × 844: `/tmp/interagents-live-2.4-mobile-hero-pl.png`.
- Polish mobile product section at 390 × 844: `/tmp/interagents-live-2.4-mobile-products-pl.png`.
- Polish mobile booking calendar at 390 × 844: `/tmp/interagents-live-2.4-mobile-booking-pl.png`.
- Smallest-width live calendar at 320 × 800: `/tmp/interagents-live-2.4-calendar-320.png`.
- English mobile booking calendar at 390 × 844: `/tmp/interagents-live-2.4-mobile-booking-en.png`.
- Desktop product and booking sections at 1440 × 1000:
  - `/tmp/interagents-live-2.4-desktop-products.png`.
  - `/tmp/interagents-live-2.4-desktop-booking.png`.
- Production serves theme assets with cache version `2.4.1` and booking assets with cache version `1.2.1`. Polish and English, 320px, 390px, and 1440px checks passed with no horizontal overflow or browser errors/warnings. The final accessibility patch also preserves focus after date selection and leaves complete split-color product names in the heading accessibility tree.
- Sanitized production deployment and rollback evidence: `/Users/molty/.openclaw/workspace-inter/backups/interagents-site-wp-deploy/20260712T115236Z-8d54662f`.

final result: passed
