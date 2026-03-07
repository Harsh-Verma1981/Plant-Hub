# Garden Portal (Plant-Hub)

Garden Portal (a.k.a. Plant-Hub) is a small PHP-based web application for a community gardening / e-commerce site. It provides pages for browsing garden projects, a shop with a simple cart, checkout and order storage, user signup/login, and a contact form for organizations. The project uses plain PHP, MySQL (mysqli / PDO), Tailwind for UI, and PHPMailer for transactional emails.

This README explains what's included, how to run the app locally, how to prepare the database, and recommended improvements and security hardening for developers who pick up the repository.

---

Table of contents
- Project overview
- Included files (brief)
- Requirements
- Quick start (install & run)
- Database schema (SQL)
- Configuration (database + mail)
- Development notes & recommended improvements
- Troubleshooting
- Contributing
- License

---

Project overview
- Purpose: Demo community gardening site with shop + cart, checkout, contact form for organizations and basic authentication.
- Built with: PHP (server-side), MySQL, HTML/CSS, Tailwind, PHPMailer (for email).
- Not production-ready: There are a couple of security issues (plain-text passwords in DB, raw SQL in some places, hard-coded SMTP credentials). See "Development notes" for fixes.

---

Included files (high-level)
- `index.php` — Landing page / home, sections for services and gardens.
- `Shop.php` — Product listing, add-to-cart, remove-from-cart and cart summary. Cart is stored in PHP session.
- `checkout.php` — Checkout form (stores orders in `orders` and `order_items` tables using PDO).
- `Contact.php` — Contact / organization signup form; saves to `organization` and sends confirmation email via PHPMailer.
- `signup.php` — User signup, inserts to `signup` table and sends welcome email via PHPMailer.
- `login.php` — Login form (currently validates against `signup` table).
- `logout.php` — Destroys session and redirects to `index.php`.
- `who_we_are.php`, `Developers.php`, `profile.html` — About pages and developer info.
- `SignupDatabase.php` — MySQL connection helper using mysqli (used by signup, login, Contact).
- `input.css` — Tailwind input file (imports tailwindcss).
- `output.css` — Compiled Tailwind stylesheet included by some pages.
- `PHPMailer/` — PHPMailer library folder (the code uses `require 'PHPMailer/PHPMailer.php'` etc.). If not present, install via Composer or add the library folder.
- Images referenced (png1.png, png2.png, png5.png, png6.png, png7.png) — example images used in pages (place appropriate assets in repo/public).

Important: There are two different database names referenced in code:
- `SignupDatabase.php` connects to database named `PlantHub`.
- `checkout.php` uses PDO and connects to database `planthub`.

Make sure you normalize the database name in your environment (choose one name and update files accordingly).

---

Requirements
- PHP 7.4+ (or newer)
- MySQL / MariaDB
- Web server (Apache, Nginx) or PHP built-in server for development
- Composer (recommended) — to install PHPMailer and other dependencies (optional if PHPMailer folder is included)
- An SMTP account for sending e-mails (Gmail App Passwords or other transactional provider)

---

Quick start — run locally (development)

1) Clone or copy the project into your web server document root (e.g., `htdocs` for XAMPP or `/var/www/html` for Apache). Example:
   - Place all .php, .css and `PHPMailer` directory under a single folder: `garden-portal/`.

2) Create the database and tables (see SQL below). Decide a DB name (for example `planthub` or `PlantHub`) and update the DB config in these files:
   - `SignupDatabase.php` (mysqli)
   - `checkout.php` (PDO DSN string)

3) (Optional but recommended) Install PHPMailer using Composer
   - In project root:
     ```
     composer require phpmailer/phpmailer
     ```
   - Then update `require` lines to use Composer autoload (example in PHP scripts):
     ```php
     require 'vendor/autoload.php';
     ```
   - Or keep the `PHPMailer/` folder and ensure files (`PHPMailer.php`, `SMTP.php`, `Exception.php`) are present.

4) Configure SMTP credentials and database credentials (see "Configuration" below).

5) Start server
   - Using built-in PHP server (development):
     ```
     php -S 0.0.0.0:8000
     ```
     Then open http://localhost:8000/index.php

6) Test signup/login, add items to cart from `Shop.php`, proceed to checkout and fill contact form.

---

Database schema (example SQL)
- Choose a database name and use consistently in code. Example uses `planthub`.

Create database:
```sql
CREATE DATABASE planthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE planthub;
```

Create `signup` table:
```sql
CREATE TABLE signup (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Create `organization` table (for Contact.php):
```sql
CREATE TABLE organization (
  id INT AUTO_INCREMENT PRIMARY KEY,
  orgname VARCHAR(255) NOT NULL UNIQUE,
  location VARCHAR(255),
  email VARCHAR(255),
  phone VARCHAR(50),
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Create `orders` and `order_items` tables (for checkout.php):
```sql
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  user_name VARCHAR(255),
  user_phone VARCHAR(50),
  user_location TEXT,
  user_zip VARCHAR(20),
  user_state VARCHAR(100),
  total_amount DECIMAL(10,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(512),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

Adjust types and indexes as needed.

---

Configuration

1. Database settings
   - Edit `SignupDatabase.php`:
     ```php
     $hostname = 'localhost';
     $username = 'root';
     $password = '';
     $database = 'planthub'; // make sure this matches your DB name
     ```
   - Edit `checkout.php` PDO DSN:
     ```php
     $pdo = new PDO('mysql:host=localhost;dbname=planthub', 'root', '');
     ```

2. SMTP settings
   - PHPMailer is currently configured in signup.php and Contact.php with Gmail credentials:
     ```php
     $mail->Host = 'smtp.gmail.com';
     $mail->SMTPAuth = true;
     $mail->Username = 'your-email@gmail.com';
     $mail->Password = 'your-app-password';
     $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
     $mail->Port = 465;
     ```
   - IMPORTANT: Do not commit real credentials. Use environment variables or a config file (see suggestions below).
   - For Gmail, enable 2FA and create an App Password for your app (recommended); do not use your normal password.

3. Recommended: use a .env or config.php
   - Create a `config.php` or `.env` (not committed) to centralize credentials, e.g.:
     ```php
     // config.php (example)
     return [
       'db' => [
         'host' => 'localhost',
         'name' => 'planthub',
         'user' => 'root',
         'pass' => '',
       ],
       'mail' => [
         'host' => 'smtp.gmail.com',
         'username' => 'you@example.com',
         'password' => 'app-password',
         'port' => 465
       ]
     ];
     ```
   - Then `include 'config.php'` and use values.

---

Development notes & recommended improvements (important)

Security / correctness fixes (prioritized)
- Passwords: signup currently stores plain-text passwords. Replace with password hashing:
  - On signup: `password_hash($password, PASSWORD_DEFAULT)`
  - On login: `password_verify($inputPassword, $dbHash)`
- Use prepared statements everywhere. Some files (e.g., `login.php`) use string interpolation; convert to prepared statements to prevent SQL injection.
- CSRF protection: Add CSRF tokens to forms to prevent cross-site request forgery.
- Validate and sanitize all user inputs on server-side even if front-end validation exists.
- Move sensitive credentials (DB, SMTP) to environment variables or config outside of version control.
- Avoid echoing raw database errors to users. Log errors server-side.
- Session security: Regenerate session id after login (session_regenerate_id) and set secure cookie flags (cookie_httponly, cookie_secure).
- Email credentials: Do not hard-code real credentials. Use app passwords (Gmail) or transactional email service (SendGrid, Mailgun).

UX / maintainability
- Use Composer for PHPMailer and autoloading.
- Organize code into directories:
  - `public/` for public-facing files
  - `src/` for server logic, helpers, DB access
  - `views/` for templates
- Consider implementing MVC or using a micro-framework (Slim, Laravel) for larger functionality.
- Add server-side pagination for product lists if size grows.

Bug & inconsistencies to fix
- Database name mismatch (`PlantHub` vs `planthub`) — standardize and update files.
- Two different SQL APIs used (mysqli and PDO). Standardize to one (recommended: PDO).
- Checkout and order code expect `$_SESSION['cart']` structure — be careful about reindexing when removing items.

---

Troubleshooting (common issues)

- Blank pages / parse error:
  - Check PHP error log and enable display_errors in dev environment:
    ```php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    ```
- Email not sending:
  - Ensure SMTP credentials are correct, that port and encryption match provider.
  - If using Gmail, enable 2FA and create an App Password; enable "Less secure apps" is deprecated.
  - Check firewall/network for outbound SMTP blocking.
- Database connection errors:
  - Verify host, username, password and database name.
  - Check MySQL server running and accessible.
- Assets missing (images/CSS):
  - Ensure referenced images (png1.png, png2.png, etc.) exist in the path the pages expect.
  - For Tailwind: some pages load compiled `output.css`; ensure it is present or use the CDN script included in many pages.

---

Contributing
- Please open issues for bugs and feature requests.
- For pull requests:
  - Use feature branches.
  - Provide a clear description and include steps to reproduce.
  - Add any DB migrations or SQL statements needed.

---

Sample .env example (do NOT commit real secrets)
```
DB_HOST=localhost
DB_NAME=planthub
DB_USER=root
DB_PASS=
MAIL_HOST=smtp.gmail.com
MAIL_USER=you@example.com
MAIL_PASS=app-password-here
MAIL_PORT=465
MAIL_SECURE=ssl
```

---

License
- This project does not contain a license file by default. You can add one (for example MIT) if you want to make the project open source.

---
