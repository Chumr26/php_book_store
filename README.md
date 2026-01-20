# 📚 BookStore Project

A comprehensive e-commerce bookstore application built with **pure PHP** using the **MVC (Model-View-Controller)** architecture. This project serves as a robust example of building a scalable web application without relying on heavy frameworks, suitable for learning and production environments.

## 🚀 Features

### 👤 Customer Interface (Front-end)
*   **Book Browsing**: View books with pagination, sorting, and filtering by category, author, or publisher.
*   **Search**: Real-time search by book title & author.
*   **Product Details**: Comprehensive book info including description, reviews, rating, and related books.
*   **Shopping Cart**: Manage cart items, update quantities, and calculate totals dynamically.
*   **Checkout & Payment**:
    *   Secure checkout process.
    *   Multiple payment methods including **COD** and **PayOS** (Vietnam QR payment).
    *   Coupon code application.
*   **User Account**:
    *   Registration & Login.
    *   Profile management & Password change.
    *   Order history tracking with detailed status.
    *   Forgot Password via Email (SMTP).

### 🛠️ Admin Panel (Back-end)
*   **Dashboard**: Overview of sales statistics, recent orders, and key metrics.
*   **Product Management**: CRUD operations for Books, Categories, Authors, and Publishers.
*   **Order Management**: View, filter, and update order statuses (Pending, Shipping, Completed, Cancelled).
*   **User Management**: Manage customer accounts.
*   **Content Management**: Manage promotional banners.
*   **Statistics**: Detailed reports on revenue and best-selling products.

## 💻 Technlogy Stack

*   **Backend**: PHP 5.6+ (compatible with PHP 8.x)
*   **Database**: MySQL / MariaDB
*   **Frontend**: HTML5, CSS3, JavaScript, jQuery, Bootstrap 4
*   **Libraries**:
    *   `PHPMailer` (Email services)
    *   `PayOS` (Payment Gateway)
*   **Architecture**: Custom MVC Pattern

## ⚙️ Prerequisites

*   **XAMPP** (or WAMP/MAMP) with PHP >= 5.6 and MySQL.
*   **Composer**: Dependency manager for PHP.

## 📦 Installation

### 1. Clone the Repository
Clone this project into your web server's root directory (e.g., `C:/xampp/htdocs/`).

```bash
cd C:/xampp/htdocs
git clone <repository-url> book_store
```

### 2. Install Dependencies
Navigate to the project directory and install the required PHP libraries.
```bash
cd book_store
composer install
```

### 3. Database Setup
1.  Open **phpMyAdmin** (http://localhost/phpmyadmin).
2.  Import the database file located at `db/bookstore.sql`.
    *   This script creates the `bookstore` database, all tables, and **seeds sample data**.

### 4. Configuration
**Database Connection:**
Modify `Model/connect.php` and `Admin/Model/connect.php` if your MySQL credentials differ from the defaults:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookstore";
```

**Email Configuration (Optional):**
To enable "Forgot Password" functionality, copy the example config and add your SMTP details:
```bash
cp config/email.local.php.example config/email.local.php
```
Edit `config/email.local.php` with your SMTP credentials (e.g., Gmail App Password).

**PayOS Configuration (Optional):**
To enable online payments:
```bash
# Edit config/payos_config.php directly if needed
```

## 🔑 Default Credentials

The `db/bookstore.sql` file includes seed data for testing:

| Role | Username / Email | Password |
|------|------------------|----------|
| **Admin** | `admin` | `admin123` |
| **Customer** | `nguyenvana@gmail.com` | `123456` |

## 📂 Project Structure

```
book_store/
├── Admin/              # Admin Panel (Controllers, Views, Models)
├── config/             # Configuration files (Email, Payment)
├── Content/            # Static assets (CSS, Images)
├── Controller/         # Customer-facing Business Logic
├── db/                 # Database SQL scripts & ERD
├── docs/               # Documentation & Requirements
├── Model/              # Database Models & Entities
├── View/               # Customer-facing Views/Templates
├── composer.json       # Dependency definitions
└── index.php           # Main Entry Point
```

## 🤝 Contributing

1.  Fork the repository.
2.  Create your feature branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

## 📝 License

This project is open-source and available for educational purposes.
