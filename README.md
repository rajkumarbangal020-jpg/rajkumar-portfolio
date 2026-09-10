# Rajkumar Portfolio

Professional PHP developer portfolio for **Rajkumar Bangal**, built with PHP, MySQL, Bootstrap, JavaScript and jQuery. The project includes a dynamic public portfolio, project detail views, contact enquiries and an admin panel for managing portfolio content.

## Features

- Responsive light/dark portfolio UI
- About, skills, work experience, projects, education and certifications
- Dynamic project detail modal/page
- PHP + MySQL backend
- Admin dashboard and CRUD management
- Contact enquiry storage
- CSRF protection and PDO prepared statements
- Project image upload and fallback handling

## Tech Stack

PHP, MySQL, HTML5, CSS3, Bootstrap 5, JavaScript, jQuery and Font Awesome.

## Local Setup

1. Clone this repository into your XAMPP `htdocs` directory.
2. Start Apache and MySQL.
3. Import `database/portfolio.sql` in phpMyAdmin.
4. Database defaults are `localhost`, user `root`, blank password and database `rajkumar_portfolio`. For other environments set `PORTFOLIO_DB_HOST`, `PORTFOLIO_DB_USER`, `PORTFOLIO_DB_PASS` and `PORTFOLIO_DB_NAME`.
5. Open the project in your browser.
6. Visit `admin/setup_admin.php` once to create the first admin account.
7. Delete or disable `admin/setup_admin.php` after the first admin is created on a production server.

## Security

No production database password, API key or hard-coded admin password is included in this repository. Never commit `.env` files, API tokens or real customer data.

## GitHub

Profile: https://github.com/rajkumarbangal020-jpg

> Project demo/source links should only be added in the admin panel after the corresponding repositories or live demos actually exist.
