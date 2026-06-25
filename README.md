# KasiTrade – C2C E‑Commerce Platform

KasiTrade is a mobile‑first, customer‑to‑customer marketplace designed for South Africa’s informal township economy.

## Tech Stack
- Frontend: HTML5, CSS3, Bootstrap 5, JavaScript
- Backend: PHP 8, MySQL
- Architecture: Session‑based authentication, RBAC, AJAX cart updates

## Live Demo
- **Website:** [https://kasitrade.freehosting.dev](https://kasitrade.freehosting.dev)
- **Admin Panel:** [https://kasitrade.freehosting.dev/admin/login.php](https://kasitrade.freehosting.dev/admin/login.php)

## Demo Credentials
| Role       | Email               | Password |
|------------|---------------------|----------|
| Admin      | admin@test.com      | 1234     |
| Buyer      | buyer@test.com      | 1234     |
| Seller     | seller@test.com     | 1234     |

## Local Installation
1. Clone the repository.
2. Import kasitrade.sql into MySQL, with the database exported first.
3. Update config/database.php with your credentials.
4. Place the project in XAMPP's htdocs.
5. Open http://localhost/kasitrade.

## Features
- Full C2C marketplace (list, browse, cart, checkout)
- RBAC with Admin, Moderator, Dispatcher, Seller, Buyer
- Admin user management (CRUD, soft‑delete)
- Listing approval workflow
- Product reviews & ratings
- Simulated Ozow payments
- Fully responsive design

## Project Structure
kasitrade/
├── assets/ # CSS, JS, images
├── config/ # Database config
├── includes/ # Header, footer
├── admin/ # Admin dashboard, users, listings
├── uploads/ # Product images
└── ...

## Contact
Created for academic deliverable – all rights reserved.
