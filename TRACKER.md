# FinTrack — Project Tracker

Quick context for future work sessions, so the codebase doesn't need to be re-read from scratch. Update the **Status** and **Log** sections when something changes.

## Snapshot

- **Stack:** PHP 8.2 (mysqli, prepared statements) · MySQL `finance_db` · jQuery 3.6 · Bootstrap 5.3 · Bootstrap Icons · Chart.js 4.4 (client dashboard only)
- **Local URL:** `http://localhost/Complete%20Projects/php/FinTrack/` (XAMPP; base URL auto-detected in `includes/config.php`)
- **Repo:** https://github.com/Kassophiaa/FinTrack (public, branch `main`)
- **DB credentials:** `db/db_config.php` (git-ignored). Template: `db/db_config.example.php`. Never commit real credentials or passwords.
- **Roles:** `admin`, `advisor`, `user` (shown as "Client"). Folder per role: `admin/`, `advisor/`, `client/`.

## Architecture (where things live)

| Area | Files | Notes |
|---|---|---|
| Page bootstrap | `includes/config.php` → `includes/session.php` | `session.php` enforces login, active status, and role-per-folder |
| Layout | `includes/header.php`, `includes/navbar.php` (sidebar + top bar + menu config per role; Messages only in sidebar, Notifications only as top-bar bell), `includes/footer.php` (toast stack, confirm modal, scripts) | Set `$title` and `$active` before including header/navbar |
| Shared page templates | `includes/transaction_page.php` (income/expenses), `messages_page.php`, `notifications_page.php`, `profile_page.php`, `auth_aside.php` | Thin role pages just set config vars and include these |
| JSON endpoints | `db/*_request.php`, `db/request.php` (income/expense/investment/account/advisors modules) | All start with `require_once __DIR__ . '/api_auth.php';` (session + active check) |
| Data access | `classes/*.php` | One class per table; `myDB` wraps mysqli; errors return `false` (exceptions off) |
| Styles | `css/app.css` | Single design system; tokens in `:root` |
| JS helpers | `js/script.js` | `toast()`, `showAlert()`, `confirmDialog()`, `ajaxPost()`, `bindSearch()`, `emptyRow()`, `emptyStateHtml()`, `formatCurrency()`, `formatDate()`, `timeAgo()`, `renderStars()`, `initials()`, `setFormLoading()`, `setButtonLoading()` |
| Page JS | `js/transactions.js` (income+expenses via global `TX`), `investment.js`, `bank.js`, `advisor.js` (client: directory + advisor profile), `message.js` (global `CHAT`), `notification.js`, `profile.js`, `rating.js`, `login.js` | Some pages keep small inline scripts (clients, admin pages, client advice, data sharing) |

## Conventions

- Link local CSS/JS with `asset('js/file.js')` (adds `?v=<filemtime>` so browsers never run stale cached scripts).
- Escape output: PHP `e()`; JS `escapeHtml()`. Never echo user data raw.
- Endpoints return `{status: "success"|"error", message, data}`.
- Soft delete everywhere (`status = 'Deleted'`); users can be `Inactive`.
- Feedback = `toast()`; destructive actions = `confirmDialog()` (no `alert()`/`confirm()`).
- Permissions live in `advisor_permissions` (client_id, advisor_id, view_income, view_expenses, view_investments, view_bank_accounts, can_message, can_give_advice, can_rate). Grant = upsert; revoke = status `Deleted`.
- Notifications are created server-side on: new advice → client; new client / access updated / access removed / new rating → advisor.
- Currency: Philippine peso (₱).

## Pages

- **Client:** dashboard, income, expenses, investments, bank_accounts, advisors, advisor_profile (also lists that advisor's advice), messages, notifications, privacy_settings (Data Sharing), profile. `settings.php` → redirects to profile.
- **Advisor:** dashboard, clients, client_profile (advice is written + listed here), messages, notifications, ratings, profile. `give_advice.php` and `advice.php` → redirect to clients.
- **Admin:** dashboard, manage_users, permissions, ratings (with remove), settings (System info), profile.
- **Public:** `index.php` (sign in), `register.php`, `logout.php`.

## Testing

- Lint: `for f in $(find . -name "*.php" -not -path "./.git/*"); do /c/xampp/php/php.exe -l "$f"; done`
- JS syntax: `node --check js/<file>.js`
- Demo accounts exist in the local DB (one admin, advisors, clients; `client_pedro` ↔ `advisor_juan` is the best-connected pair with data). Passwords are kept out of this file because the repo is public.
- The `mysql` CLI fails (missing `caching_sha2_password` plugin) — query the DB with `php -r` instead.
- Headless screenshots: Chrome + Node DevTools script (Playwright isn't installed).
- Clean up any test users/data after testing.

## Status

- [x] Published to GitHub with credentials excluded
- [x] Full UI redesign + role guards + security fixes (commit `89818a1`)
- [ ] No database schema/seed file in the repo (`finance_db` tables must be created manually)
- [ ] No CSRF tokens on POST endpoints
- [ ] No pagination on long lists (all records load at once)
- [ ] Income/expenses don't change bank account balances (balances are entered manually)

## Log

- **2026-10-29** — Published repo; moved DB credentials to git-ignored config.
- **2026-10-29** — Removed page headings on Messages and My Profile. Removed advisor Advice page (and `js/advice.js`); dashboard links go to clients/client profiles. Old client `advice.php` URL redirects to dashboard.
- **2026-10-29** — Compact sidebar spacing; under 620px window height section labels hide. Client menu fits without scrolling down to ~480px tall.
- **2026-10-29** — Accounts page redesign: total + allocation bar (fixed categorical colors per account, >8 fold into "Other"), richer account cards (share of total, transaction count, this month in/out via `BankAccount::getAccountsWithActivity`), dashed "Add account" card.
- **2026-10-29** — Removed client "My Advice" page; dashboard advice items link to the advisor's profile.
- **2026-10-29** — Removed Notifications from sidebar and Messages from top bar. Fixed pages stuck on "Loading…" from stale cached JS: versioned asset URLs + script-error fallback in `script.js`.
- **2026-10-29** — Redesign: new app shell and design system, dashboard chart, two-pane messaging, My Advice + Data Sharing pages, toasts/confirm dialog. Fixes: role enforcement, password-hash leak in advisor list, XSS, duplicate-account crash, deactivated-user API access, investment summary, base-URL detection.
