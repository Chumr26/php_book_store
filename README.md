# 📚 BookStore E-Commerce Project

> A comprehensive, production-ready online bookstore built with **Pure PHP (MVC)**, featuring a full-fledged customer front-end and a powerful admin dashboard.

## 📖 Table of Contents

*   [Project Overview](#-project-overview)
*   [Features](#-key-features)
    *   [Customer Interface](#customer-interface)
    *   [Admin Panel](#admin-panel)
*   [Technology Stack](#-technology-stack)
*   [Project Structure](#-project-structure)
*   [Installation & Setup](#-installation--setup)
*   [Configuration](#-configuration)
*   [Usage Guide](#-usage-guide)
*   [Default Credentials](#-default-credentials)
*   [Troubleshooting](#-troubleshooting)

---

## 📋 Project Overview

This project is a complete e-commerce solution for an online bookstore. It demonstrates how to build a scalable, maintainable web application using the **Model-View-Controller (MVC)** architectural pattern without relying on heavy frameworks like Laravel or Symfony. This makes it an excellent resource for understanding the core principles of web development, PHP sessions, database interactions, and secure coding practices.

**Key Highlights:**
*   **Separation of Concerns:** Distinct separation between logic (Controllers), data (Models), and presentation (Views).
*   **Real-world Functionality:** Includes cart management, order processing, email notifications, and payment gateway integration.
*   **Localization:** The user interface (UI) is designed in **Vietnamese** for a specific target audience, while the codebase and database schema use **English** for international developer standards.

---

## 🚀 Key Features

### Customer Interface
The front-end is designed to provide a seamless shopping experience:
*   **📚 Smart Book Browsing:**
    *   Grid and List views.
    *   Advanced filtering by Price Range, Category, Author, Publisher, and Rating.
    *   Dynamic sorting (Price, Newest, Best Selling).
*   **🔍 Instant Search:** Real-time search functionality for books and authors.
*   **🛒 Shopping Cart:**
    *   AJAX-powered updates (add, remove, update quantity) without page reloads.
    *   Real-time total calculation.
*   **💳 Secure Checkout:**
    *   Multiple payment methods: **COD** (Cash on Delivery), **Bank Transfer**, or **Online Payment** (via PayOS).
    *   Coupon code system for discounts.
*   **👤 User Profile:**
    *   Order history tracking with status updates (Pending -> Shipping -> Completed).
    *   Profile management (Avatar, Address, Phone).
    *   Secure password change.
    *   **Forgot Password** flow via email OTP/Link.
*   **⭐ Reviews & Ratings:** Customers can rate and review books they have purchased.

### Admin Panel
A comprehensive dashboard for store owners:
*   **📊 Analytics Dashboard:** Visual charts for Revenue, Order Status, and Top-selling products.
*   **📦 Inventory Management:**
    *   **Books:** Full CRUD (Create, Read, Update, Delete) with image upload.
    *   **Categories/Authors/Publishers:** Manage taxonomy and metadata.
*   **🧾 Order Management:**
    *   View order details and customer info.
    *   Update order status (Process orders from Pending to Completed).
*   **👥 Customer Management:** View and manage registered users.
*   **📢 Marketing:** Manage promotional banners and discount coupons.
*   **📈 Reports:** Exportable reports on sales performance.

---

## 💻 Technology Stack

*   **Backend Language:** PHP 5.6+ (Compatible with PHP 7.x and 8.x).
*   **Database:** MySQL / MariaDB using `MySQLi` Extension.
*   **Frontend Library:** Bootstrap 4, jQuery, FontAwesome 5.
*   **Architecture:** Custom MVC Pattern.
*   **External Libraries (via Composer):**
    *   `phpmailer/phpmailer`: For reliable SMTP email sending.
    *   `payos/payos`: For integrating Vietnam's QR payment gateway.

---

## 📂 Project Structure

```text
BookStore/
├── Admin/                  # 🔒 Admin Panel Module
│   ├── Controller/         # Admin Logic (Auth, Stats, CRUD)
│   ├── Model/              # Admin Database Queries
│   └── View/               # Admin Templates (Dashboard, Forms)
├── config/                 # ⚙️ Global Configuration (SMTP, Keys)
├── Content/                # 🎨 Public Assets
│   ├── CSS/                # Stylesheets
│   └── images/             # Uploaded images (books, banners)
├── Controller/             # 🧠 Front-end Business Logic
├── db/                     # 💾 SQL Scripts
│   ├── bookstore.sql       # Schema & Seed Data
│   └── DATABASE_ERD.md     # Database Diagram
├── docs/                   # 📄 Documentation
├── Model/                  # 🗄️ Database Access Layer (ORM-like)
├── View/                   # 🖼️ Front-end Templates (HTML/PHP)
├── composer.json           # 📦 Dependency Manager
└── index.php               # 🚦 Main Entry Point (Front Controller)
```

---

## 📦 Installation & Setup

### Prerequisites
1.  **Web Server**: XAMPP, WAMP, or MAMP installed.
2.  **PHP Version**: 7.4 or higher recommended (5.6 minimum).
3.  **Composer**: Installed globally.

### Step 1: Clone the Project
Navigate to your web server's root directory (e.g., `htdocs` or `www`).
```bash
cd C:/xampp/htdocs
git clone https://github.com/yourusername/book_store.git
```

### Step 2: Install Dependencies
Install the required PHP packages defined in `composer.json`.
```bash
cd book_store
composer install
```

### Step 3: Database Setup
1.  Start **Apache** and **MySQL** in XAMPP.
2.  Open [phpMyAdmin](http://localhost/phpmyadmin).
3.  Create a new database named `bookstore`.
4.  Select the `bookstore` database and click **Import**.
5.  Choose the file `db/bookstore.sql` and click **Go**.
    *   *Note: This script will create all tables and populate them with sample data.*

### Step 4: Deployment (Optional)
To deploy this project live, we recommend using **Render** (Web Service) and **TiDB Cloud** (Database).

**1. Database (TiDB Cloud):**
*   Create a free Serverless cluster on [TiDB Cloud](https://tidbcloud.com/).
*   Connect to it using a MySQL client and import `db/bookstore.sql`.

**2. Web Service (Render):**
*   Connect your GitHub repository to [Render](https://render.com/).
*   Select **Docker** as the runtime.
*   Add the following Environment Variables in the Render Dashboard:
    *   `DB_HOST`: Your TiDB Host (e.g., `gateway01...tidbcloud.com`)
    *   `DB_USER`: Your TiDB User
    *   `DB_PASS`: Your TiDB Password
    *   `DB_NAME`: `bookstore`
    *   `DB_PORT`: `4000`
    *   `DB_SSL`: `true`
    *   `BASE_URL`: Your Render URL (e.g., `https://your-app.onrender.com/`)

---

## ⚙️ Configuration

### Database Connection
If you set a password for your root MySQL user, update the connection files:

*   **Front-end:** Modify `Model/connect.php`
*   **Admin:** Modify `Admin/Model/connect.php`

```php
$servername = "localhost";
$username = "root";
$password = "YOUR_PASSWORD"; // Update this if needed
$dbname = "bookstore";
```

### Email Configuration (SMTP)
To enable the "Forgot Password" feature:
1.  Copy the example config:
    ```bash
    cp config/email.local.php.example config/email.local.php
    ```
2.  Edit `config/email.local.php` with your credentials:
    ```php
    return [
        'host' => 'smtp.gmail.com',
        'username' => 'your_email@gmail.com',
        'password' => 'your_app_password', // Use App Password, not Gmail login password
        'port' => 587
    ];
    ```

---

## 🔑 Default Credentials

The system comes pre-loaded with users for testing `db/bookstore.sql`:

| Role | Email / Username | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` |
| **Customer** | `nguyenvana@gmail.com` | `password` |

*Note: All passwords in the database are hashed using `password_hash()` (Bcrypt).*
