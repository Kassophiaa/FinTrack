# Finance Web

A PHP/MySQL personal finance management web app with three roles:

- **Client** – track income, expenses, bank accounts and investments, message advisors, rate advisors, manage privacy settings
- **Advisor** – view permitted client profiles, give advice, send messages, receive ratings
- **Admin** – manage users, advisor permissions, ratings and system settings

## Tech stack

PHP (mysqli), MySQL, jQuery, Bootstrap.

## Setup

1. Clone the repo into your web server root (e.g. `xampp/htdocs/finance_web`).
2. Create a MySQL database named `finance_db` and its tables.
3. Copy `db/db_config.example.php` to `db/db_config.php` and fill in your database credentials.
4. If your URL is different, update `$base_url` in `includes/config.php`.
5. Open `http://localhost/finance_web` in your browser.
