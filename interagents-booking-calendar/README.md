# interagents booking calendar

Standalone WordPress plugin for free 20-minute interagents workflow-call bookings. It is a clean interagents implementation inspired by the Ksymena calendar flow; it does not modify or depend on Ksymena, WooCommerce, a payment provider, user accounts, or an external calendar.

## Install and use

1. Copy `interagents-booking-calendar` to `wp-content/plugins/` and activate **interagents booking calendar**.
2. Add `[interagents_booking_calendar]` to the booking section or page.
3. Open **interagents bookings** in WordPress admin to review bookings or change notification email, days, hours, duration, start interval, notice, and horizon.

When a page already supplies the booking headline and introduction, use `[interagents_booking_calendar embedded="1"]`. Embedded mode omits the duplicate plugin header and gives the calendar region its own accessible label.

Safe activation defaults are Europe/Warsaw, Monday–Friday 10:00–16:00, 20-minute meetings starting every 30 minutes, 24-hour notice, 60-day horizon, and notifications to `hello@interagents.ai`. The final default start is 15:30, so every meeting ends before 16:00.

The widget follows the site language (`ia_get_lang()` when available), then its `lang` attribute, URL/cookie/WordPress locale fallback. It sends localized EN/PL customer and admin emails. Confirmation says that the meeting link and joining details will follow by email and provides a token-protected `.ics` download.

## Security and data

- Every booking is revalidated while holding an atomic, site-wide WordPress option lock so concurrent visitors cannot take overlapping slots.
- The public form uses a refreshed WordPress nonce, honeypot, required privacy-policy acknowledgement, prepared SQL, bounded/sanitized fields, and a salted-IP rate limit (five attempts per 15 minutes). The rate-limit identifier expires after 15 minutes and is not stored with the booking.
- Calendar download tokens are random 256-bit values; only their SHA-256 hashes are stored.
- Guest booking does not create an account or initiate payment.
- Booking records older than 12 months are scheduled for automatic daily deletion. Deactivation stops the cleanup schedule but does not immediately remove customer records.

## Deterministic slot tests

```sh
php interagents-booking-calendar/tests/slot-engine-test.php
```

The harness covers weekday/default slot count, final end time, weekends, exact 24-hour notice, 60-day horizon, Europe/Warsaw DST, and overlap behavior without requiring WordPress or a database.
