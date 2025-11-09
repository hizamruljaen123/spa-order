# Spa Booking (CodeIgniter 3) — Setup & Smoke Test

This document explains how to set up the application on Laragon/Windows, configure database and Telegram, install dompdf later via PowerShell, and run a smoke test across frontend and admin features.

## 1) Requirements

- Laragon (Apache + MySQL/MariaDB) running.
- PHP CLI accessible, or use Laragon terminal. If `php` is not recognized, use Laragon's terminal or add PHP to PATH (see Troubleshooting).
- Composer (for dompdf installation). If `composer` is not recognized, install Composer for Windows or run Composer from Laragon terminal.

## 2) Base URL

We have set the base URL to http://localhost/spa/ in:
- application/config/config.php → `$config['base_url']` [already set].

If your site path differs (e.g. http://localhost/your-folder/), update:
- application/config/config.php → `$config['base_url']` (line may vary).

## 3) Database Configuration

1. Create a new MySQL database (e.g. `spa_ci3`).

2. Import schema:
   - File: database/schema.sql

3. Configure DB credentials:
   - Edit application/config/database.php:
     - hostname (default: localhost)
     - username
     - password
     - database (e.g. spa_ci3)

4. Ensure the DB driver is `mysqli` (default).

## 4) Sessions

We configured a writable sessions path:
- application/config/config.php → `$config['sess_save_path'] = APPPATH.'cache/sessions';`

Make sure folder exists and is writable:
- application/cache/sessions/ (already added placeholder index.html)

## 5) Autoload

We enabled essential libraries/helpers in application/config/autoload.php:
- Libraries: database, session, form_validation
- Helpers: url, form, html, security

## 6) Routes Overview

Routes configured in application/config/routes.php:

- Default (frontend): `/` → Booking form
- Frontend Booking:
  - GET `/booking`
  - POST `/booking/submit`
- Admin:
  - GET `/admin` (Dashboard)
  - Therapists CRUD:
    - GET `/admin/therapists`
    - POST `/admin/therapist/create`
    - GET/POST `/admin/therapist/edit/{id}`
    - GET `/admin/therapist/delete/{id}`
  - Packages CRUD:
    - GET `/admin/packages`
    - POST `/admin/package/create`
    - GET/POST `/admin/package/edit/{id}`
    - GET `/admin/package/delete/{id}`
  - Schedule / FullCalendar:
    - GET `/admin/schedule` (page)
    - GET `/admin/schedule?start=YYYY-MM-DD&end=YYYY-MM-DD[&therapist_id=N]` (JSON)
  - Reports:
    - GET `/admin/report`
- Reports API (optional):
  - GET `/report/monthly?year=YYYY`
- Telegram API:
  - POST `/api/telegram/send`

Note: If you do not have Apache rewrite enabled, use `index.php` in URLs:
- http://localhost/spa/index.php/booking
- http://localhost/spa/index.php/admin

## 7) Telegram Notification

Edit application/controllers/Api.php and set:
- `$botToken = 'YOUR_TELEGRAM_BOT_TOKEN'`
- `$chatId = 'YOUR_CHAT_ID'`

Test via HTTP (optional):
- POST to `/api/telegram/send` with fields:
  - customer_name, address, therapist_name, package_name, date (YYYY-MM-DD), time (HH:MM)

The booking submission triggers this automatically.

## 8) Invoice PDF (dompdf)

PDF generation is implemented in Admin::generate_invoice(). Until dompdf is installed, the controller will fall back to serving printable HTML.

To install dompdf later via PowerShell:
- Open PowerShell in project root (c:\laragon\www\spa)
- Ensure PHP is accessible (Laragon terminal recommended)
- Run:

  composer require dompdf/dompdf:^1.2
  composer dump-autoload -o

If `php` or `composer` is not recognized, see Troubleshooting.

After installation:
- Access `/admin/invoice/{booking_id}` (HTML view)
- Click "Download PDF" button (`/admin/invoice/generate/{booking_id}`) to stream the generated PDF.

## 9) Optional: Remove index.php from URLs

If you want clean URLs:
- Set application/config/config.php:

  $config['index_page'] = '';

- Create .htaccess in the project root with:

  <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /spa/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
  </IfModule>

- Ensure Apache mod_rewrite is enabled.

## 10) Smoke Test Plan

Follow these steps to verify core features:

A. Frontend Booking
1) Open booking form:
   - http://localhost/spa/index.php/booking (or /booking if clean URLs)
2) Fill the form:
   - Nama, Alamat
   - Package: select (seeded in database/schema.sql)
   - Therapist (optional)
   - Tanggal (YYYY-MM-DD), Jam (HH:MM)
3) Submit.
4) Expected:
   - Record inserted to `booking` table.
   - Success alert on the form page.
   - Telegram POST sent (if token/chat configured; otherwise logged as error, flow continues).

B. Admin Dashboard
1) Open http://localhost/spa/index.php/admin
2) Expected:
   - Today’s summary displayed.
   - Popular packages (last 30 days) visible if data exists.

C. Manage Therapists
1) Open `/admin/therapists`
2) Add new therapist, edit existing, delete one.
3) Expected:
   - CRUD affects `therapist` table.

D. Manage Packages
1) Open `/admin/packages`
2) Add, edit, delete package.
3) Expected:
   - CRUD affects `package` table.

E. Calendar (FullCalendar)
1) Open `/admin/schedule` (page)
2) Verify events load; click events to see booking details.
3) Test JSON endpoint (replace date range):
   - `/admin/schedule?start=2025-01-01&end=2025-12-31`
4) Expected:
   - Booked slots appear in red for pending/confirmed.
   - Completed = grey; Canceled = orange.

F. Reports (Chart.js)
1) Open `/admin/report`
2) Select year, click Refresh.
3) Expected:
   - Revenue bars (Rp)
   - Booking counts as line
   - Table totals match chart

G. Invoice
1) After confirming a booking (set status to `confirmed` via DB or future admin action), open:
   - `/admin/invoice/{booking_id}`
2) Click "Download PDF":
   - `/admin/invoice/generate/{booking_id}`
3) Expected:
   - If dompdf installed → PDF download
   - If not → HTML fallback displayed; use browser Print to Save as PDF.

## 11) Troubleshooting

- php/composer not recognized:
  - Use Laragon Terminal (Menu → Terminal) so PATH is preconfigured.
  - Or add Laragon PHP to PATH, e.g.:
    `C:\laragon\bin\php\php-8.x.x\` (adjust version).
  - Verify: `php -v` and `composer -V`

- Database connection errors:
  - Check application/config/database.php (hostname/user/password/database).
  - Ensure DB server is running and schema imported.

- 404 routing:
  - If mod_rewrite not set, include `index.php` in URLs.
  - Confirm base_url matches your folder.

- FullCalendar shows no events:
  - Open the JSON endpoint with `start` and `end` query params.
  - Ensure bookings exist in date range.

- Telegram not sending:
  - Set `$botToken` and `$chatId` in Api.php.
  - Server needs outbound internet access.

- Invoice PDF classes not found:
  - Install dompdf (see section 8).
  - Ensure application/config/config.php has:
    `$config['composer_autoload'] = FCPATH.'vendor/autoload.php';`

## 12) Security Notes (Basic)
- Consider enabling CSRF in production (`$config['csrf_protection'] = TRUE`) and update forms accordingly.
- Validate and sanitize inputs (form_validation already used).
- Lock down admin area with authentication (not included in this demo scope).

## 13) File Map (Key)
- Frontend: application/controllers/Booking.php, application/views/booking_form.php
- Admin: application/controllers/Admin.php, application/views/admin/*
- Models: application/models/* (Booking_model, Therapist_model, Package_model, Report_model, Invoice_model)
- API: application/controllers/Api.php (Telegram)
- Reports API: application/controllers/Report.php
- Routes: application/config/routes.php
- Config: application/config/config.php, application/config/autoload.php, application/config/database.php
- DB schema: database/schema.sql

You can now proceed to install dompdf later and run through the Smoke Test to validate end-to-end flows.