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

- Deployed release: theme `2.3.2` and booking calendar `1.1.2` at `https://interagents.ai`.
- Polish production homepage at 390px: document and body scroll widths equal the 390px viewport, the embedded booking widget appears once without a duplicate header, and booking slots form three equal columns.
- Polish production homepage at 320px: document and body scroll widths equal the 320px viewport, booking slots switch to two columns, and the closed navigation has both `inert` and `aria-hidden`.
- English production homepage at 390px: copy, metadata, language switcher, booking heading, and three-column slot grid render correctly with no horizontal overflow.
- Production contact modal: English first-name and last-name labels render correctly; focus moves to Close, Escape closes the modal, and focus returns to the trigger.
- Production offer at 768px: the heading begins 47px below the fixed header, legacy `product=complete` normalizes to `product=intercore`, invalid options normalize to `mac`, `managed`, and `included`, and only those controls expose `aria-pressed="true"`.
- Production desktop at 1440px: the hero retains the intended two-line headline, the booking grid and form have equal client/scroll widths, and the document has no horizontal overflow.
- Production browser diagnostics contain no errors or warnings; only the standard WordPress jQuery Migrate informational log appears.

## Findings

No actionable P0, P1, or P2 findings remain.

## Follow-up polish

- P3: the selected concept uses a month-grid calendar while the production booking plugin uses a date field plus time slots. The production interaction is more compact on mobile and was retained intentionally.

final result: passed
