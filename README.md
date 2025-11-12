NAMA Passenger Feedback – Deployment Guide

Overview

- Simple PHP + MySQL app to collect passenger feedback (cleanliness, staff, security) and view analytics in an admin dashboard.
- Works on local XAMPP (Apache + MySQL) and standard shared hosting via cPanel.

Requirements

- PHP 8.0 or later
- MySQL 5.7+ (or MariaDB with equivalent features)
- PDO MySQL extension enabled
- Web server (Apache on XAMPP or cPanel shared hosting)

Project Structure

- `index.php` – Public feedback form
- `feedback.php` – Alternate feedback form page
- `submit.php` – Handles form submission
- `thanks.php` – Confirmation page after submission
- `dashboard.php` – Admin analytics dashboard (requires login)
- `admin_login.php` / `admin_signup.php` / `admin_manage.php` – Admin auth and management
- `db_config.php` – Database connection settings
- `config.php` – App config (e.g., allow admin signup)
- `manual_setup.sql` – SQL to initialize required tables
- `static/` – CSS, JS, and images

Local Installation (XAMPP on Windows)

1) Install and Start Services
- Install XAMPP for Windows.
- Open XAMPP Control Panel and start `Apache` and `MySQL`.

2) Copy Project into htdocs
- Copy the project folder (e.g., `Feedback App PHP`) to `C:\xampp\htdocs\`.
- Final path example: `C:\xampp\htdocs\feedback-app` (rename as you prefer).

3) Create Database
- Open `http://localhost/phpmyadmin`.
- Create a database named `feedback_app` (or any name you prefer).
- In phpMyAdmin, select your database and use the `Import` tab to import `manual_setup.sql` from the project root.

4) Configure Database Connection
- Open `db_config.php` and set:
  - `$host = 'localhost';`
  - `$dbname = 'feedback_app';` (or your chosen name)
  - `$username = 'root';`
  - `$password = '';` (default XAMPP MySQL has no password; change if you set one)

5) Allow Initial Admin Signup
- Open `config.php` and ensure:
  - `define('ALLOW_ADMIN_SIGNUP', true);`
- After creating the first admin, set it to `false` in production.

6) Visit the App Locally
- Feedback form: `http://localhost/feedback-app/index.php`
- Admin login: `http://localhost/feedback-app/admin_login.php`
- Dashboard: `http://localhost/feedback-app/dashboard.php` (requires login)

Deployment (cPanel Shared Hosting)

1) Upload Files
- Compress the project folder into a `.zip` on your computer.
- Log in to cPanel → `File Manager` → open `public_html/`.
- Upload the `.zip` and `Extract` it into `public_html/` (you may place it in a subfolder, e.g., `public_html/feedback`).

2) Create Database and User
- In cPanel, open `MySQL® Databases`.
- Create a new database (e.g., `accountname_feedback_app`).
- Create a new MySQL user and set a strong password.
- Add the user to the database with `ALL PRIVILEGES`.

3) Import Schema
- Open `phpMyAdmin` from cPanel.
- Select your new database and use the `Import` tab to import `manual_setup.sql`.

4) Configure Database Connection
- Edit `db_config.php` in `public_html`:
  - `$host = 'localhost';` (most cPanel hosts use `localhost`)
  - `$dbname = 'accountname_feedback_app';`
  - `$username = 'accountname_dbuser';`
  - `$password = 'yourStrongPassword';`

5) Configure Admin Signup
- In `config.php`, use:
  - `define('ALLOW_ADMIN_SIGNUP', true);`
- Visit `admin_signup.php` and create the first admin.
- Then change to `define('ALLOW_ADMIN_SIGNUP', false);` to disable public signups.

6) Verify App URLs
- If deployed at `public_html/` root:
  - Feedback form: `https://yourdomain.com/index.php`
  - Admin login: `https://yourdomain.com/admin_login.php`
- If in a subfolder (e.g., `feedback`):
  - Feedback form: `https://yourdomain.com/feedback/index.php`
  - Admin login: `https://yourdomain.com/feedback/admin_login.php`

Security and Hardening

- Disable admin signup in production after creating the first admin.
- Use strong MySQL credentials and rotate them periodically.
- Ensure file permissions: typically `644` for files and `755` for directories.
- Keep PHP updated to the latest supported version in cPanel.
- Consider restricting access to admin pages by IP via cPanel `IP Blocker` if needed.

Troubleshooting

- Database connection failed:
  - Check `db_config.php` host/db/user/password.
  - Verify database and tables exist in phpMyAdmin.
- Unauthorized accessing `dashboard.php`:
  - Login first via `admin_login.php`; sessions are required.
- Blank page or 500 error:
  - Check PHP version and error logs (`error_log` in cPanel or XAMPP console).
- Assets not loading:
  - Confirm paths to `static/` are correct relative to your deployment folder.

Maintenance Tips

- Back up the database regularly from phpMyAdmin (Export).
- Keep the `static/styles.css` and `static/app.js` updated for any UI tweaks.
- Consider enabling HTTPS for your domain via cPanel (AutoSSL/Let’s Encrypt).

Quick Test (optional)

- You can also run the built-in PHP dev server during development:
  - From the project root: `php -S localhost:8000`
  - Visit `http://localhost:8000/index.php`
