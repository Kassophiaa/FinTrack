# FinTrack — Personal Finance Dashboard

A PHP/MySQL web app for tracking personal finances and getting help from financial advisors. Clients choose exactly what data each advisor can see.

## Features

**Clients**
- Dashboard with monthly income, expenses, net savings, investment return, a 6-month income vs. expenses chart and top spending categories
- Record income, expenses, investments and bank accounts / e-wallets (add, edit, delete, live search)
- Browse advisors, share selected data with them, and change or stop sharing at any time
- Receive advice, message advisors, and rate them

**Advisors**
- See clients who shared data with them, with locked sections for anything not shared
- Write, edit and delete advice; message clients; view ratings and reviews
- Public bio and experience shown to clients

**Administrators**
- Platform overview, user management (roles, activate/deactivate), all data-sharing permissions, rating moderation, system info

In-app notifications are created for new advice, new clients, access changes and ratings.

## Tech stack

PHP 8 (mysqli, prepared statements), MySQL, jQuery, Bootstrap 5, Bootstrap Icons, Chart.js.

## Setup

1. Put the project anywhere under your web server root (e.g. `xampp/htdocs/finance_web`). The base URL is detected automatically.
2. Create a MySQL database named `finance_db` and its tables.
3. Copy `db/db_config.example.php` to `db/db_config.php` and fill in your database credentials.
4. Open the project folder in your browser (e.g. `http://localhost/finance_web`).

## Project structure

```
admin/  advisor/  client/   Pages for each role (each area only allows its own role)
classes/                    Data-access classes
db/                         JSON endpoints used by the pages (AJAX)
includes/                   Layout (header, sidebar/top bar, footer) and shared page templates
css/app.css                 Design system
js/                         Page scripts; script.js holds shared helpers (toasts, confirm dialog, formatting)
```
