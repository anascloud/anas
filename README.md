# QuantiTop — Authentication Module

Laravel + Inertia.js + Vue 3 authentication module: registration, login, email-based
two-factor authentication, forgot/reset password, logout, and a `Hello World`
authenticated landing page. Fully localized in English and Hebrew (with RTL).

> **Note on how this repo was produced:** this codebase was generated in a sandboxed
> environment without access to `packagist.org` or an npm registry mirror for Laravel's
> own packages, so `composer install` / `npm install` could not be executed here to
> smoke-test the build. Every file was written by hand against Laravel 11 / Inertia 1.x /
> Vue 3 conventions. **Before you rely on this as "done," run it locally** (see below) and
> fix anything a real `composer install` surfaces — treat this as a strong first draft,
> not a verified build.

## 1. Stack

- **Backend:** Laravel 12, session-based auth (no Sanctum/API tokens needed for a
  first-party Inertia app), Eloquent, database-backed sessions/cache/queue.
- **Frontend:** Vue 3 (`<script setup>`), Inertia.js, Tailwind CSS, vue-i18n.
- **Mail:** Laravel Notifications over SMTP (Mailtrap by default — see `.env.example`).

> Originally pinned to Laravel 11, but `composer install` correctly refused every 11.x
> release: Composer 2.9+ blocks resolving any package version with a known security
> advisory, and Laravel 11.x's Symfony dependency range only ever included versions
> affected by a few 2026 Symfony CVEs (fixed only in the Symfony line Laravel 12 allows).
> No patched 11.x point release exists, so the constraint was bumped to `^12.0` rather
> than suppressing the advisory. If you hit a similar `"were not loaded, because they
> are affected by security advisories"` error on a future `composer update`, check
> whether a newer major actually contains the fix before reaching for
> `config.audit.ignore` / `block-insecure: false` — those should be a last resort with a
> written justification, not the default fix.

## 2. Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate

npm install
npm run build   # or `npm run dev` while developing

# Point DB_* in .env at a real MySQL/Postgres database, then:
php artisan migrate --seed

php artisan serve
```

The seeder creates a demo account: `demo@quantitop.test` / `Password123` — useful for a
reviewer, since the password is otherwise only known to whoever registered.

### Mail

By default `.env.example` is wired for a **Mailtrap sandbox inbox** (safe for testing —
nothing gets delivered to real inboxes). Sign up at mailtrap.io, create an inbox, and
drop your own `MAIL_USERNAME` / `MAIL_PASSWORD` in `.env`. Swap `MAIL_MAILER`/host for
any real SMTP provider (Resend, Postmark, SES, Gmail SMTP, etc.) when you want mail to
actually land in real inboxes for deployment.

## 3. What's implemented

- **Registration** — creates the user, then immediately routes into the 2FA challenge
  (a fresh account still has to prove control of the email before it's treated as
  signed in — consistent with login).
- **Login** — email + password checked first; on success the session stores a
  *pending* user id (not yet authenticated) and a 2FA email is sent. Wrong credentials,
  and 5 failed attempts within a minute per email+IP, are both handled with specific
  messages (`auth.failed`, `auth.throttle`).
- **Two-factor via email** — 6-digit code, hashed at rest (never stored or logged in
  plaintext), 10-minute expiry, 5 wrong-attempt cap before the code is locked (a new one
  must be requested), and a 30-second resend cooldown to stop mail-bombing. Distinct
  messages for: wrong code, expired code, too many attempts, resend-too-soon, and
  "your sign-in session expired, log in again" (covers refreshing/bookmarking the
  challenge page after the session pending-state is gone).
- **Forgot / reset password** — standard Laravel token broker, but the emailed link
  points at the Vue reset-password page (not a Laravel Blade view). The "check your
  email" response is identical whether or not the address exists, to avoid leaking
  which emails are registered.
- **Logout** — invalidates the session and regenerates the CSRF token.
- **Authenticated landing page** — literally `Hello World`, plus the signed-in user's
  name/email and a logout button, per the spec.
- **Localization** — full English/Hebrew UI (vue-i18n) with a pill toggle (matches the
  reference screenshot's style) that persists via a cookie and flips `dir="rtl"` on
  `<html>`; **all server-side validation/error strings are also localized** based on
  the same cookie (`SetLocale` middleware), not just the static UI labels.
- **UX states** — every form disables its submit button and swaps in a loading label
  while `form.processing` is true (Inertia's built-in double-submit guard), shows
  field-level errors inline, and shows success banners (e.g. "reset link sent",
  "code sent") via flashed session status.
- **Accessibility basics** — every input has a bound `<label for>`, `aria-invalid` /
  `aria-describedby` on errored fields, and errors are announced via `role="alert"`.

## 4. Known gaps / what I'd do with more time

- No automated browser/e2e test for the Vue side (only Laravel feature tests for the
  auth flows are included in `tests/Feature/Auth`).
- No "remember this device, skip 2FA for N days" option — every login always requires
  a fresh code, which is the safer default but adds friction; a device-trust cookie
  would be a reasonable v2 addition.
- Rate limiting is per-route (`throttle:`); a stricter global lockout policy
  (e.g. temporary account lock after repeated failures) is not implemented.
- The visual design approximates the reference Figma (split card layout, blue accent,
  pill language switcher) but isn't a pixel-for-pixel match — worth a pass against the
  actual Figma file before shipping.

## 5. Deployment

Since this needs a real database + real outbound SMTP, the simplest free/low-effort
options for a reviewable demo are:

- **Render.com** — free-tier Postgres + a web service running
  `php artisan serve` (or a small Dockerfile). Set the same `.env` vars in Render's
  dashboard as environment variables.
- **Railway.app** — similar: managed MySQL/Postgres plugin + a Laravel service,
  good free/starter tier for a short-lived demo.

Either way: run `npm run build` before deploy (Vite needs a production build committed
or built in a deploy step), then `php artisan migrate --seed --force` once against the
live database.

## 6. Repo layout highlights

```
app/Http/Controllers/Auth/   Registration, session (login/logout), 2FA challenge, password reset
app/Services/TwoFactorAuthenticator.php   All code generation/verification/resend logic
app/Models/TwoFactorCode.php  Hashed one-time codes with expiry/attempt tracking
lang/{en,he}/                Backend-facing translated strings (auth + mail + validation)
resources/js/i18n/{en,he}.json   Frontend-facing translated strings
resources/js/Pages/Auth/     Login, Register, TwoFactorChallenge, ForgotPassword, ResetPassword
resources/js/Pages/Dashboard.vue   The "Hello World" authenticated landing page
tests/Feature/Auth/          Registration + 2FA feature tests
```
