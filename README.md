# Investment Referral System

A PHP and MySQL prototype of a web platform where users sign up, choose an investment plan, and earn through referral codes. It is a learning project from early 2025. It is not production-ready and should not be used with real money or real users.

## What is in the repo

- User pages: registration and login, dashboard, investment plans, my investments, referral details (`index.php`, `login.php`, `dashboard.php`, `investment_plans.php`, `investment.php`).
- Admin area in `admin/`: manage users, add investment plans, edit bonuses, review payment requests and withdrawals.
- `payment_gateway.php`: a payment step with Stripe artwork. I have not checked or tested a real payment integration.
- `database_setup.sql`: creates the `users`, `investments` and `referrals` tables.
- `config.php` is expected by the pages but is not part of the repo. Create your own with your database settings.

## Run locally

1. Install PHP and MySQL (for example through XAMPP).
2. Import `database_setup.sql` into a new database.
3. Create `config.php` with your own connection settings.
4. Serve the folder with PHP and open `index.php`.

## Known limits

- No tests, no input hardening review, and the admin user view shows account passwords in a decryptable form. Do not reuse this design.
- Not maintained.
