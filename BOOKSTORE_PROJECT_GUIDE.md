# BOOKSTORE PROJECT GUIDE - ONLINE BOOKSTORE WEBSITE

## ⚠️ IMPORTANT NOTE

**Language Convention:**
- ✅ **Database Schema**: English (tables, columns, field names)
- ✅ **Code & Documentation**: English (all PHP files, comments, guides)
- 🇻🇳 **User Interface (UI)**: Vietnamese (website pages, forms, menus, buttons)

The HTML templates and frontend views remain in Vietnamese to match the original project design. Only the backend database structure and code documentation have been converted to English for international development.

## 📋 PROJECT OVERVIEW

This is an e-commerce website project for an online bookstore built with pure PHP using the MVC (Model-View-Controller) architecture. The project has 2 main parts:

- **Front-end**: Customer user interface (Vietnamese UI)
- **Back-end (Admin)**: Admin management interface (Vietnamese UI)

## 🏗 PROJECT DIRECTORY STRUCTURE

```
BookStore/
├── index.php                 # Main entry point for the website
├── package-lock.json         # Node.js dependencies file
├── SMTP_FIX_GUIDE.md         # Guide for email (SMTP) configuration
│
├── Admin/                    # ADMIN PANEL
│   ├── index.php             # Admin portal entry point
│   ├── Content/              # Admin resources
│   ├── Controller/           # Admin business logic
│   ├── Model/                # Admin database connection
│   └── View/                 # Admin interface
│
├── Controller/               # BUSINESS LOGIC (Front-end)
│   ├── cart.php              # Shopping cart handling
│   ├── forget.php            # Password recovery handling
│   ├── order.php             # Order management
│   ├── home.php              # Homepage logic
│   ├── login.php             # Login handling
│   ├── registration.php      # Account registration
│   └── book.php              # Book browsing logic
│
├── Model/                    # DATA LAYER (Database)
│   ├── connect.php           # Database connection
│   ├── shopping_cart.php     # Shopping cart model
│   ├── books.php             # Books model
│   ├── orders.php            # Orders model
│   ├── customers.php         # Customers model
│   ├── pagination.php        # Pagination model
│   ├── bookstore_schema.sql  # Database schema file
│   ├── bookstore_seeds.sql   # Sample data file
│   ├── class.phpmailer.php   # Email library
│   └── class.smtp.php        # SMTP configuration
│
├── View/                     # USER INTERFACE (Front-end Templates)
│   ├── header.php            # Header (page top)
│   ├── footer.php            # Footer (page bottom)
│   ├── home.php              # Homepage
│   ├── login.php             # Login page
│   ├── registration.php      # Registration page
│   ├── forgot_password.php   # Forgot password page
│   ├── reset_password.php    # Password reset page
│   ├── books.php             # Book list page
│   ├── book_detail.php       # Book detail page
│   ├── cart.php              # Shopping cart page
│   ├── order.php             # Order page
│   └── banners.php           # Banner/advertising section
│
├── Content/                  # STATIC RESOURCES
│   ├── CSS/                  # CSS files
│   │   └── bookstore.css     # Main stylesheet
│   └── images/               # Images folder
│       ├── books/            # Book cover images
│       └── banners/          # Advertisement banners
│
└── node_modules/             # JAVASCRIPT LIBRARIES
    ├── bootstrap/            # Bootstrap CSS/JS framework
    ├── jquery/               # jQuery library
    └── [other libraries]
```

## 📁 DETAILED DIRECTORY DESCRIPTION

### 1️⃣ Controller/ Folder (Business Logic)

| File | Function |
|---|---|
| `cart.php` | Handle adding/removing/updating books in shopping cart |
| `forget.php` | Handle password recovery requests and send recovery email |
| `order.php` | Handle order creation and save orders |
| `home.php` | Handle displaying featured books on homepage |
| `login.php` | Handle user login and authentication |
| `registration.php` | Handle new account registration |
| `book.php` | Handle book filtering, searching, and pagination |

### 2️⃣ Model/ Folder (Database Layer)

| File | Function |
|---|---|
| `connect.php` | MySQL database connection |
| `shopping_cart.php` | CRUD operations for shopping cart |
| `books.php` | CRUD operations for books |
| `orders.php` | CRUD operations for orders |
| `customers.php` | CRUD operations for customers/users |
| `pagination.php` | Handle pagination for book lists |
| `bookstore_schema.sql` | Database structure and schema |
| `bookstore_seeds.sql` | Sample data for testing |
| `class.phpmailer.php` | PHPMailer email library |
| `class.smtp.php` | SMTP configuration for emails |

### 3️⃣ View/ Folder (User Interface)

| File | Function |
|---|---|
| `header.php` | Navigation menu, logo, search bar |
| `footer.php` | Contact information, links, copyright |
| `home.php` | Display featured books, bestsellers, banners |
| `login.php` | Login form |
| `registration.php` | Account registration form |
| `forgot_password.php` | Form for password recovery request |
| `reset_password.php` | New password entry form |
| `books.php` | Grid-style book list |
| `book_detail.php` | Book details (cover image, description, price, author, publisher) |
| `cart.php` | Shopping cart display |
| `order.php` | Order form, delivery address entry |
| `banners.php` | Promotional banners and slideshow |

### 4️⃣ Admin/ Folder (Admin Panel)

```
Admin/
├── index.php              # Admin main page
├── Model/
│   └── connect.php        # Database connection (may differ from front-end)
├── View/
│   ├── login.php          # Admin login page
│   ├── header.php         # Admin panel header
│   ├── footer.php         # Admin panel footer
│   ├── books.php          # Book management
│   ├── edit_books.php     # Edit book
│   ├── add_category.php   # Add book category
│   ├── edit_category.php  # Edit category
│   ├── authors.php        # Author management
│   ├── publishers.php     # Publisher management
│   └── reports.php        # Revenue and order statistics
└── Content/
    ├── CSS/               # CSS specific to admin
    └── images/            # Book images upload directory
```

### 5️⃣ Content/ Folder (Static Resources)

- `CSS/bookstore.css`: Main stylesheet for the entire site
- `images/books/`: Folder containing book cover images
- `images/banners/`: Folder containing promotional banners

### 6️⃣ node_modules/ Folder (JavaScript Libraries)

- **Bootstrap**: CSS/JS framework for responsive design
- **jQuery**: JavaScript library for DOM manipulation
- Various supporting libraries

## 🚀 PROJECT DEPLOYMENT STEPS

### STEP 1: INSTALL ENVIRONMENT

1. **Install XAMPP (or WAMP/MAMP)**
   - Download XAMPP from: https://www.apachefriends.org
   - Install XAMPP (includes Apache, MySQL, PHP)
   - Start Apache and MySQL from XAMPP Control Panel

2. **Check PHP Version**
   ```bash
   php -v
   # Requires PHP >= 5.6 or higher
   ```

### STEP 2: EXTRACT AND COPY PROJECT

```bash
# Copy the BookStore folder into XAMPP's htdocs directory
# Example: C:/xampp/htdocs/BookStore
```

### STEP 3: CREATE DATABASE

1. **Access phpMyAdmin**
   - Open browser: http://localhost/phpmyadmin

2. **Create new database**
   ```sql
   CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import SQL files**
   - Select `bookstore` database
   - Click "Import" tab
   - First, import `Model/bookstore_schema.sql` to create tables
   - Then, import `Model/bookstore_seeds.sql` for sample data
   - Click "Go" to execute

### STEP 4: CONFIGURE DATABASE CONNECTION

Open file `Model/connect.php` and edit:

```php
<?php
$servername = "localhost"; // Server name (default: localhost)
$username = "root";        // MySQL username (default: root)
$password = "";            // MySQL password (default: blank)
$dbname = "bookstore";     // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set UTF-8 charset
$conn->set_charset("utf8mb4");
?>
```

Do the same for `Admin/Model/connect.php`

### STEP 5: CONFIGURE SMTP (Email Sending)

Refer to `SMTP_FIX_GUIDE.md` for configuration:

```php
// File: Model/class.smtp.php or within Controller/forget.php
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password'; // Gmail app password
```

**Note**: For Gmail, you need to create an "App Password" from security settings.

### STEP 6: RUN PROJECT

1. **Access front-end website**
   - http://localhost/BookStore/

2. **Access admin panel**
   - http://localhost/BookStore/Admin/

### STEP 7: VERIFY FUNCTIONALITY

**Front-end (Customer Features):**
- ✅ View book list
- ✅ View book details (title, author, publisher, price, description)
- ✅ Search books by title, author, category
- ✅ Add books to shopping cart
- ✅ Create account
- ✅ Login to account
- ✅ Forgot password (requires SMTP configuration)
- ✅ Place order
- ✅ View order history

**Back-end (Admin Features):**
- ✅ Admin login
- ✅ Book management (CRUD)
- ✅ Category management
- ✅ Author management
- ✅ Publisher management
- ✅ Order management
- ✅ Revenue statistics

## 🔧 DATABASE STRUCTURE

**customers table (Customers)**
- `id_customer` (PRIMARY KEY)
- `full_name`
- `email`
- `password` (encrypted)
- `phone`
- `address`
- `date_of_birth`
- `gender`
- `status`

**categories table (Book Categories)**
- `id_category` (PRIMARY KEY)
- `category_name`
- `description`
- `sort_order`

**authors table (Authors)**
- `id_author` (PRIMARY KEY)
- `author_name`
- `pen_name`
- `biography`
- `date_of_birth`
- `nationality`

**publishers table (Publishers)**
- `id_publisher` (PRIMARY KEY)
- `publisher_name`
- `address`
- `phone`
- `email`
- `website`

**books table (Books)**
- `id_book` (PRIMARY KEY)
- `title`
- `id_author` (FOREIGN KEY)
- `id_publisher` (FOREIGN KEY)
- `id_category` (FOREIGN KEY)
- `isbn`
- `price`
- `original_price` (original price before discount)
- `cover_image`
- `description`
- `pages`
- `publication_year`
- `language`
- `stock_quantity`
- `view_count`
- `sale_count`
- `status`
- `is_featured`
- `created_at`

**orders table (Orders)**
- `id_order` (PRIMARY KEY)
- `id_customer` (FOREIGN KEY)
- `order_number`
- `order_date`
- `total_amount`
- `status` (pending/processing/completed/cancelled)
- `payment_method` (credit_card/debit_card/paypal/etc.)
- `payment_status` (pending/paid/failed)
- `recipient_name`
- `delivery_address`
- `phone`
- `created_at`

**order_items table (Order Details)**
- `id_item` (PRIMARY KEY)
- `id_order` (FOREIGN KEY)
- `id_book` (FOREIGN KEY)
- `quantity`
- `unit_price`
- `total_price`

**shopping_cart table (Shopping Cart)**
- `id_cart_item` (PRIMARY KEY)
- `id_customer` (FOREIGN KEY)
- `id_book` (FOREIGN KEY)
- `quantity`
- `added_at`

**reviews table (Book Reviews)**
- `id_review` (PRIMARY KEY)
- `id_book` (FOREIGN KEY)
- `id_customer` (FOREIGN KEY)
- `rating` (1-5 stars)
- `title`
- `content`
- `status` (pending/approved)
- `created_at`

**coupons table (Discount Coupons)**
- `id_coupon` (PRIMARY KEY)
- `coupon_code`
- `campaign_name`
- `discount_type` (percent/fixed)
- `discount_value`
- `min_order_amount`
- `max_discount`
- `quantity`
- `start_date`
- `end_date`
- `status` (active/inactive)

**wishlist table (Wishlist)**
- `id_wishlist` (PRIMARY KEY)
- `id_customer` (FOREIGN KEY)
- `id_book` (FOREIGN KEY)
- `added_at`

**banners table (Promotional Banners)**
- `id_banner` (PRIMARY KEY)
- `title`
- `image`
- `link_url`
- `sort_order`
- `status`

## 🎯 WORKFLOW FLOW

### 1. Customer Flow
1. Visit homepage (`index.php`)
2. Browse books by category (`View/books.php`)
3. Search books by title/author
4. View book details (`View/book_detail.php`)
5. Read reviews from other customers
6. Add to shopping cart (`Controller/cart.php`)
7. View shopping cart (`View/cart.php`)
8. Login/Register (`Controller/login.php` or `registration.php`)
9. Fill order information (`View/order.php`)
10. Choose payment method
11. Confirm order (`Controller/order.php`)
12. Receive confirmation email (`Model/class.phpmailer.php`)

### 2. Admin Flow
1. Access admin panel (`Admin/index.php`)
2. Login to admin (`Admin/View/login.php`)
3. Manage books:
   - View list (`Admin/View/books.php`)
   - Add new book (enter info, upload cover image)
   - Edit book (`Admin/View/edit_books.php`)
   - Delete book
4. Manage categories:
   - Add category (`Admin/View/add_category.php`)
   - Edit category (`Admin/View/edit_category.php`)
5. Manage authors and publishers
6. Manage orders (confirm, update status)
7. View statistics (`Admin/View/reports.php`)
   - Revenue by day/month/year
   - Order count
   - Best-selling books
   - Popular categories

## 🛠 TECHNOLOGIES USED

**Back-end:**
- **PHP (5.6+)**: Main programming language
- **MySQL**: Database management system
- **PHPMailer**: Email sending library

**Front-end:**
- **HTML5 & CSS3**: Interface building
- **Bootstrap 4**: Responsive CSS framework
- **jQuery**: JavaScript library
- **JavaScript**: Client-side interactions
- **Font Awesome**: Icon library

**Architecture Pattern:**
- **MVC (Model-View-Controller)**: Code organization pattern

## 📝 IMPORTANT NOTES

**Language Implementation:**
- The database tables and columns use English names for international compatibility
- All PHP code comments and variable names are in English
- This documentation is in English
- **However, the HTML templates and user-facing pages remain in Vietnamese** (header.php, footer.php, login.php, book.php, etc.)
- Admin interface pages are also in Vietnamese
- If you need to translate the UI to English, you'll need to update the View files separately

**⚠ Security:**
- Encrypt passwords using `password_hash()` and `password_verify()`
- Use Prepared Statements to prevent SQL Injection
- Validate and sanitize user input
- Use HTTPS in production
- Protect payment information

**⚠ Performance:**
- Optimize database queries (index frequently searched fields)
- Use caching for static data
- Compress images before uploading
- Lazy load book cover images

**⚠ Sessions:**
- The project uses PHP Sessions for:
  - Storing login information
  - Storing shopping cart
  - Managing user state

**⚠ File Upload:**
- Check file type (allow only images: jpg, png, webp)
- Limit file size (max 2MB)
- Rename files on upload to avoid duplication
- Validate image dimensions (recommended: 600x800px for book covers)

**⚠ Business Rules:**
- Validate ISBN (10 or 13 digits)
- Check stock before allowing order
- Automatically reduce stock when order is placed
- Send email notification when order status changes

## 🐛 DEBUGGING AND ERROR HANDLING

**Enable PHP error display (during development):**
```php
// Add to the top of index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Check database connection:**
```php
// File: test_connection.php
<?php
include 'Model/connect.php';
if ($conn) {
    echo "Connection successful!";
} else {
    echo "Connection failed!";
}
?>
```

**Check sessions:**
```php
// At the top of each file that needs sessions
session_start();
// Debug sessions
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

**Test email sending:**
```php
// File: test_email.php
<?php
require 'Model/class.phpmailer.php';
require 'Model/class.smtp.php';

$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';

$mail->setFrom('your-email@gmail.com', 'BookStore');
$mail->addAddress('test@example.com');
$mail->Subject = 'Test Email';
$mail->Body = 'This is a test email from BookStore';

if($mail->send()) {
    echo 'Email sent successfully!';
} else {
    echo 'Email error: ' . $mail->ErrorInfo;
}
?>
```

## 🎨 ADVANCED FEATURES (Optional Enhancements)

**Front-end:**
- ✨ Wishlist (Favorite books list)
- ✨ Book comparison
- ✨ Advanced filtering (by price, author, publisher, year)
- ✨ Sorting (newest, best-selling, price ascending/descending)
- ✨ Book ratings and comments
- ✨ Content preview (preview sample pages)
- ✨ Related book suggestions
- ✨ Discount coupons/Vouchers

**Admin:**
- ✨ Manage discount coupons
- ✨ Detailed reports (revenue charts)
- ✨ Inventory management (low stock alerts)
- ✨ Export orders to Excel
- ✨ Marketing email campaigns

## 📚 REFERENCES
- PHP Manual: https://www.php.net/manual/en/
- MySQL Documentation: https://dev.mysql.com/doc/
- Bootstrap 4: https://getbootstrap.com/docs/4.6/
- PHPMailer: https://github.com/PHPMailer/PHPMailer
- Font Awesome: https://fontawesome.com/

## 🎓 CONCLUSION

This project is a typical example of an online bookstore website using pure PHP with MVC architecture. Through this project, you can learn:
- ✅ How to organize code using MVC pattern
- ✅ CRUD operations with MySQL
- ✅ Building login/registration system
- ✅ Shopping cart and order processing
- ✅ Sending emails with PHPMailer
- ✅ Integrating Bootstrap and jQuery
- ✅ Admin and user role separation
- ✅ Managing multiple related tables (books, authors, publishers, categories)
- ✅ Image upload and management
- ✅ Building search and filtering systems

**Key Differences from Shoe Store Project:**
- Addition of author and publisher management
- More detailed book information (ISBN, page count, publication year)
- Book rating and review system
- Multiple classification categories (genre, author, publisher)

Happy learning and project development! 📚🚀

---

## 💡 PROJECT EXPANSION IDEAS

1. **Online Payment Integration**: VNPay, MoMo, PayPal, Stripe
2. **E-book Support**: PDF and EPUB format support for digital books
3. **RESTful API**: Create API for mobile app integration
4. **Multi-language Support**: Vietnamese and English language support
5. **Social Media Integration**: Login via Facebook, Google
6. **Chatbot**: Automated book recommendation chatbot
7. **Notifications**: Push notifications for new books and promotions
8. **Blog**: Book reviews, literature news section

---

**Project Version:** 1.0  
**Last Updated:** December 9, 2025  
**License:** Open Source  
**Author:** BookStore Development Team
