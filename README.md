# QuantiTop – Authentication Module

Laravel + Inertia + Vue 3 auth module. Register, login, email 2FA, forgot/reset password, logout, and a Hello World landing page. English + Hebrew (RTL).

## Stack

- Laravel 12, session auth, Eloquent
- Vue 3 + Inertia.js, Tailwind
- vue-i18n for EN/HE
- Mail: Brevo (SMTP locally, HTTP API in production — see below)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

npm install
npm run build

php artisan migrate --seed
php artisan serve
```

Seeded demo account: `anasbinsabiet@gmail.com` / `Anas@1995`

### Mail

Local dev uses Mailtrap (sandbox inbox, safe for testing) — set `MAIL_MAILER=smtp` and your Mailtrap creds in `.env`.

For real delivery, use Brevo:
- Sign up at brevo.com, verify a sender email under Senders
- Grab an API key (Settings → SMTP & API → API Keys)
- Set:
```
MAIL_MAILER=brevo
BREVO_API_KEY=your-key
MAIL_FROM_ADDRESS=your-verified-sender@example.com
```
This app ships a custom transport (`App\Mail\Transport\BrevoApiTransport`) that sends over Brevo's HTTP API instead of SMTP — needed because most free hosts (Render included) block outbound SMTP ports.

## What's built

- **Register** → creates user → sends 2FA code → challenge screen → dashboard
- **Login** → email/password check → 2FA code sent → challenge screen → dashboard
- **2FA** → 6-digit code, hashed, 10 min expiry, 5 wrong attempts locks it, 30s resend cooldown
- **Forgot password** → email with reset link → set new password → success screen → login
- **Logout** → clears session
- **Dashboard** → Hello World + user name/email + logout
- Full EN/HE UI, RTL support, server + client validation messages in both languages
- Loading/disabled states on every form, inline field errors, no double submits
- Labels, aria-invalid/aria-describedby, focus states on all inputs

## A product decision worth flagging

I went with what the written spec describes. A user who forgot their password by
definition doesn't have the current one to confirm, so a "current password" field
doesn't fit that flow. I built the email-link reset only, without a current-password
field. The Figma screen looks like it belongs to a separate "account settings → change
password" feature, which isn't in the listed scope, so I left it out rather than bolt on
a field that contradicts the flow it's attached to.

## Known gaps

- No "remember this device" option — every login always asks for a 2FA code
- No account settings / change-password-while-logged-in screen (see above)
- No e2e/browser tests, only backend feature tests for register + 2FA
- Rate limiting is per-route, not a global lockout policy

## Deployment

Live at: `https://laravel-vue-auth.onrender.com`

Deployed on Render (free tier). Postgres for the database. Docker build (`Dockerfile` in repo) since Render has no native PHP runtime. Free tier spins down after 15 min idle — first load after that takes a few seconds to wake up.

## Repo layout

```
app/Http/Controllers/Auth/       login, register, 2FA, password reset
app/Services/TwoFactorAuthenticator.php
app/Mail/Transport/BrevoApiTransport.php
lang/{en,he}/                    backend validation + mail strings
resources/js/i18n/{en,he}.json   frontend strings
resources/js/Pages/Auth/         Login, Register, TwoFactorChallenge, ForgotPassword, ResetPassword
resources/js/Pages/Dashboard.vue
tests/Feature/Auth/
```
