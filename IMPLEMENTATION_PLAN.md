# BOOKSTORE PROJECT - COMPLETE IMPLEMENTATION PLAN

**Document Version:** 1.0  
**Created:** December 9, 2025  
**Project Type:** E-Commerce Website (PHP/MySQL)  
**Architecture:** MVC (Model-View-Controller)

---

## 📊 EXECUTIVE SUMMARY

This is a comprehensive implementation plan for an online bookstore built with pure PHP and MySQL. The project follows the MVC architecture pattern with a Vietnamese UI for both customer and admin interfaces.

### Project Scope
- **Front-end**: Customer-facing e-commerce website
- **Back-end**: Admin panel for business management
- **Database**: MySQL with 10+ relational tables
- **Core Features**: Browse, Search, Cart, Orders, Admin Management

---

## 🎯 PHASE 1: PROJECT SETUP & INFRASTRUCTURE

### Phase 1.1: Environment Setup
**Objective:** Establish the development environment

**Tasks:**
- [X] Install XAMPP (Apache + MySQL + PHP)
  - PHP Version: >= 5.6 (recommended: 7.4+)
  - MySQL Version: >= 5.6
  - Apache Version: Latest stable
- [X] Verify PHP installation: `php -v`
- [X] Start Apache server from XAMPP Control Panel
- [X] Start MySQL server from XAMPP Control Panel
- [X] Verify Apache is running: http://localhost/
- [X] Verify MySQL is accessible: http://localhost/phpmyadmin

**Deliverables:**
- ✅ XAMPP fully installed and running
- ✅ Apache and MySQL services active
- ✅ PHP CLI accessible in terminal

---

### Phase 1.2: Project Directory Structure
**Objective:** Create project folder and establish directory organization

**Tasks:**
- [ ] Create base folder: `C:\xampp\htdocs\book_store\`
- [ ] Create subdirectories:
  - [ ] `Controller/` - Business logic layer
  - [ ] `Model/` - Data access layer
  - [ ] `View/` - User interface templates
  - [ ] `Admin/` - Admin panel folder structure
    - [ ] `Admin/Controller/`
    - [ ] `Admin/Model/`
    - [ ] `Admin/View/`
  - [ ] `Content/` - Static resources
    - [ ] `Content/CSS/`
    - [ ] `Content/images/`
    - [ ] `Content/images/books/`
    - [ ] `Content/images/banners/`
  - [ ] `db/` - Database files (if separate)
- [ ] Create root files:
  - [ ] `index.php` - Main entry point
  - [ ] `.htaccess` - URL rewriting (optional)

**Deliverables:**
- ✅ Complete directory structure created
- ✅ All required folders exist and are accessible

---

### Phase 1.3: Database Configuration
**Objective:** Set up MySQL database and schema

**Tasks:**
1. **Create Database:**
   - [ ] Access phpMyAdmin: http://localhost/phpmyadmin
   - [ ] Create new database: `bookstore`
   - [ ] Set character set: UTF-8MB4
   - [ ] Set collation: utf8mb4_unicode_ci

2. **Import Database Schema:**
   - [ ] Open `db/bookstore_schema.sql`
   - [ ] Review table structure (see Database Structure section below)
   - [ ] Import into `bookstore` database
   - [ ] Verify all 10 tables created:
     - customers
     - categories
     - authors
     - publishers
     - books
     - orders
     - order_items
     - shopping_cart
     - reviews
     - coupons (optional)
     - wishlist (optional)
     - banners

3. **Import Sample Data:**
   - [ ] Open `db/bookstore_seeds.sql`
   - [ ] Import into `bookstore` database
   - [ ] Verify sample data loaded

**Deliverables:**
- ✅ `bookstore` database created
- ✅ All tables created with proper structure
- ✅ Sample data imported for testing

---

### Phase 1.4: Database Connection Files
**Objective:** Create database connection configuration

**Tasks:**
1. **Create Front-end Connection File:**
   - [ ] File: `Model/connect.php`
   - [ ] Configure connection parameters:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = ""; // Blank for default XAMPP
     $dbname = "bookstore";
     ```
   - [ ] Set UTF-8 charset: `utf8mb4`
   - [ ] Test connection with error handling

2. **Create Admin Connection File:**
   - [ ] File: `Admin/Model/connect.php`
   - [ ] Use same configuration as front-end
   - [ ] Test connection

**Deliverables:**
- ✅ `Model/connect.php` created and working
- ✅ `Admin/Model/connect.php` created and working
- ✅ Database connections tested successfully

---

## 🗄️ PHASE 2: DATABASE LAYER (MODEL)

### Phase 2.1: Database Schema Review
**Objective:** Understand and document database structure

**Tables to Create:**

1. **customers** - User accounts
   - id_customer (INT, PRIMARY KEY, AUTO_INCREMENT)
   - full_name (VARCHAR 255)
   - email (VARCHAR 255, UNIQUE)
   - password (VARCHAR 255, encrypted)
   - phone (VARCHAR 20)
   - address (TEXT)
   - date_of_birth (DATE)
   - gender (ENUM: male, female)
   - status (TINYINT: 0=inactive, 1=active)
   - created_at (TIMESTAMP)
   - updated_at (TIMESTAMP)

2. **categories** - Book categories
   - id_category (INT, PRIMARY KEY, AUTO_INCREMENT)
   - category_name (VARCHAR 255, UNIQUE)
   - description (TEXT)
   - sort_order (INT)
   - status (TINYINT: 0=inactive, 1=active)

3. **authors** - Authors
   - id_author (INT, PRIMARY KEY, AUTO_INCREMENT)
   - author_name (VARCHAR 255)
   - pen_name (VARCHAR 255)
   - biography (TEXT)
   - date_of_birth (DATE)
   - nationality (VARCHAR 100)

4. **publishers** - Publishers
   - id_publisher (INT, PRIMARY KEY, AUTO_INCREMENT)
   - publisher_name (VARCHAR 255)
   - address (TEXT)
   - phone (VARCHAR 20)
   - email (VARCHAR 255)
   - website (VARCHAR 255)

5. **books** - Books catalog
   - id_book (INT, PRIMARY KEY, AUTO_INCREMENT)
   - title (VARCHAR 255)
   - id_author (INT, FOREIGN KEY -> authors)
   - id_publisher (INT, FOREIGN KEY -> publishers)
   - id_category (INT, FOREIGN KEY -> categories)
   - isbn (VARCHAR 20, UNIQUE)
   - price (DECIMAL 10,2)
   - original_price (DECIMAL 10,2)
   - cover_image (VARCHAR 255)
   - description (TEXT)
   - pages (INT)
   - publication_year (YEAR)
   - language (VARCHAR 50)
   - stock_quantity (INT)
   - view_count (INT, default 0)
   - sale_count (INT, default 0)
   - status (TINYINT: 0=inactive, 1=active)
   - is_featured (TINYINT: 0=no, 1=yes)
   - created_at (TIMESTAMP)
   - updated_at (TIMESTAMP)

6. **orders** - Customer orders
   - id_order (INT, PRIMARY KEY, AUTO_INCREMENT)
   - id_customer (INT, FOREIGN KEY -> customers)
   - order_number (VARCHAR 50, UNIQUE)
   - order_date (DATETIME)
   - total_amount (DECIMAL 12,2)
   - status (ENUM: pending, processing, completed, cancelled)
   - payment_method (VARCHAR 50)
   - payment_status (ENUM: pending, paid, failed)
   - recipient_name (VARCHAR 255)
   - delivery_address (TEXT)
   - phone (VARCHAR 20)
   - created_at (TIMESTAMP)
   - updated_at (TIMESTAMP)

7. **order_items** - Order details/line items
   - id_item (INT, PRIMARY KEY, AUTO_INCREMENT)
   - id_order (INT, FOREIGN KEY -> orders)
   - id_book (INT, FOREIGN KEY -> books)
   - quantity (INT)
   - unit_price (DECIMAL 10,2)
   - total_price (DECIMAL 12,2)

8. **shopping_cart** - Active shopping carts
   - id_cart_item (INT, PRIMARY KEY, AUTO_INCREMENT)
   - id_customer (INT, FOREIGN KEY -> customers)
   - id_book (INT, FOREIGN KEY -> books)
   - quantity (INT)
   - added_at (TIMESTAMP)

9. **reviews** - Book reviews
   - id_review (INT, PRIMARY KEY, AUTO_INCREMENT)
   - id_book (INT, FOREIGN KEY -> books)
   - id_customer (INT, FOREIGN KEY -> customers)
   - rating (TINYINT: 1-5 stars)
   - title (VARCHAR 255)
   - content (TEXT)
   - status (ENUM: pending, approved)
   - created_at (TIMESTAMP)

10. **coupons** - Discount coupons (optional)
    - id_coupon (INT, PRIMARY KEY, AUTO_INCREMENT)
    - coupon_code (VARCHAR 50, UNIQUE)
    - campaign_name (VARCHAR 255)
    - discount_type (ENUM: percent, fixed)
    - discount_value (DECIMAL 10,2)
    - min_order_amount (DECIMAL 10,2)
    - max_discount (DECIMAL 10,2)
    - quantity (INT)
    - start_date (DATE)
    - end_date (DATE)
    - status (ENUM: active, inactive)

11. **wishlist** - User wishlists (optional)
    - id_wishlist (INT, PRIMARY KEY, AUTO_INCREMENT)
    - id_customer (INT, FOREIGN KEY -> customers)
    - id_book (INT, FOREIGN KEY -> books)
    - added_at (TIMESTAMP)

12. **banners** - Promotional banners
    - id_banner (INT, PRIMARY KEY, AUTO_INCREMENT)
    - title (VARCHAR 255)
    - image (VARCHAR 255)
    - link_url (VARCHAR 255)
    - sort_order (INT)
    - status (TINYINT: 0=inactive, 1=active)

**Deliverables:**
- ✅ All tables created with proper structure
- ✅ Primary keys defined
- ✅ Foreign keys configured
- ✅ Indexes created on frequently searched fields

---

### Phase 2.2: Create Model Classes
**Objective:** Create database abstraction layer classes

**Files to Create:**

1. **Model/books.php**
   - [ ] Create `Books` class
   - [ ] Methods:
     - `getAllBooks()` - Get all books with pagination
     - `getBookById($id)` - Get single book details
     - `searchBooks($keyword)` - Search by title/author
     - `getBooksByCategory($categoryId)` - Filter by category
     - `getFeaturedBooks($limit)` - Get featured books
     - `getTopSellingBooks($limit)` - Get best-sellers
     - `addBook($data)` - Add new book (admin)
     - `updateBook($id, $data)` - Update book (admin)
     - `deleteBook($id)` - Delete book (admin)
     - `updateStock($id, $quantity)` - Reduce stock after order

2. **Model/customers.php**
   - [ ] Create `Customers` class
   - [ ] Methods:
     - `registerCustomer($data)` - Create new account
     - `getCustomerById($id)` - Get customer info
     - `getCustomerByEmail($email)` - Check email existence
     - `updateCustomer($id, $data)` - Update profile
     - `verifyPassword($email, $password)` - Login verification
     - `updatePassword($id, $password)` - Change password
     - `getAllCustomers()` - Get all customers (admin)
     - `deleteCustomer($id)` - Delete customer (admin)

3. **Model/shopping_cart.php**
   - [ ] Create `ShoppingCart` class
   - [ ] Methods:
     - `addToCart($customerId, $bookId, $quantity)` - Add book
     - `getCartItems($customerId)` - Get cart items
     - `updateCartItem($cartItemId, $quantity)` - Update quantity
     - `removeFromCart($cartItemId)` - Remove item
     - `clearCart($customerId)` - Clear entire cart
     - `getCartTotal($customerId)` - Calculate total

4. **Model/orders.php**
   - [ ] Create `Orders` class
   - [ ] Methods:
     - `createOrder($customerId, $data)` - Create new order
     - `getOrderById($id)` - Get order details
     - `getOrderItems($orderId)` - Get items in order
     - `getCustomerOrders($customerId)` - Get customer's orders
     - `getAllOrders()` - Get all orders (admin)
     - `updateOrderStatus($orderId, $status)` - Update status
     - `calculateTotal($items)` - Calculate order total
     - `generateOrderNumber()` - Generate unique order number

5. **Model/pagination.php**
   - [ ] Create `Pagination` class
   - [ ] Methods:
     - `paginate($query, $page, $limit)` - Paginate results
     - `getTotalPages($total, $limit)` - Calculate pages
     - `getOffset($page, $limit)` - Get SQL offset

6. **Model/class.phpmailer.php**
   - [ ] Import PHPMailer library (if not already included)
   - [ ] Configure for email sending

7. **Model/class.smtp.php**
   - [ ] Configure SMTP settings for Gmail
   - [ ] Host: smtp.gmail.com
   - [ ] Port: 587
   - [ ] Username and Password fields (to be configured)

**Deliverables:**
- ✅ All Model classes created
- ✅ CRUD operations implemented
- ✅ Database queries optimized
- ✅ Error handling implemented

---

## 🎮 PHASE 3: BUSINESS LOGIC LAYER (CONTROLLER)

### Phase 3.1: Create Controller Classes
**Objective:** Implement business logic and request handling

**Files to Create:**

1. **Controller/home.php**
   - [ ] Display featured books on homepage
   - [ ] Display promotional banners
   - [ ] Display best-selling books
   - [ ] Display new arrivals
   - Methods:
     - `loadHome()` - Load homepage data
     - `getFeaturedBooks()` - Get featured items
     - `getBanners()` - Get active banners

2. **Controller/book.php**
   - [ ] Handle book browsing
   - [ ] Implement search functionality
   - [ ] Implement filtering by category
   - [ ] Implement pagination
   - Methods:
     - `listBooks()` - Display all books
     - `filterByCategory($categoryId)` - Filter by category
     - `searchBooks($keyword)` - Search functionality
     - `sortBooks($sortBy)` - Sort results
     - `getBookDetail($bookId)` - Get single book details

3. **Controller/registration.php**
   - [ ] Handle new customer registration
   - [ ] Validate form input
   - [ ] Hash password
   - [ ] Create new customer record
   - Methods:
     - `register()` - Process registration
     - `validateEmail($email)` - Validate email format
     - `validatePassword($password)` - Check password strength
     - `checkEmailExists($email)` - Check duplicate email

4. **Controller/login.php**
   - [ ] Handle customer login
   - [ ] Validate credentials
   - [ ] Create session
   - [ ] Remember me functionality (optional)
   - Methods:
     - `login()` - Process login
     - `logout()` - Destroy session
     - `verifyCredentials($email, $password)` - Verify login

5. **Controller/forget.php**
   - [ ] Handle password recovery
   - [ ] Generate reset token
   - [ ] Send recovery email
   - [ ] Handle password reset
   - Methods:
     - `requestPasswordReset($email)` - Request recovery
     - `generateResetToken()` - Create reset token
     - `sendResetEmail($email, $token)` - Send email with link
     - `resetPassword($token, $newPassword)` - Update password

6. **Controller/cart.php**
   - [ ] Handle shopping cart operations
   - [ ] Add/remove/update items
   - [ ] Calculate cart totals
   - [ ] Session management
   - Methods:
     - `addToCart($bookId, $quantity)` - Add item
     - `removeFromCart($cartItemId)` - Remove item
     - `updateCart($cartItemId, $quantity)` - Update quantity
     - `getCartSummary()` - Get cart totals
     - `clearCart()` - Clear all items

7. **Controller/order.php**
   - [ ] Handle order creation
   - [ ] Validate order data
   - [ ] Create order records
   - [ ] Send confirmation email
   - [ ] Reduce book stock
   - Methods:
     - `createOrder($cartItems, $customerData)` - Create order
     - `validateOrderData($data)` - Validate form
     - `processPayment($orderData)` - Process payment
     - `sendOrderConfirmation($orderId, $email)` - Send email
     - `updateBookStock($bookId, $quantity)` - Reduce stock

**Deliverables:**
- ✅ All Controller classes created
- ✅ Business logic implemented
- ✅ Form validation added
- ✅ Session management configured

---

## 🎨 PHASE 4: PRESENTATION LAYER (VIEW)

### Phase 4.1: Create Front-end Views
**Objective:** Build customer-facing UI templates (Vietnamese)

**Files to Create:**

1. **View/header.php** - Navigation & Logo
   - [ ] Logo and branding
   - [ ] Navigation menu
   - [ ] Search bar
   - [ ] Login/Logout link
   - [ ] Shopping cart icon
   - [ ] Account menu

2. **View/footer.php** - Bottom section
   - [ ] Contact information
   - [ ] Quick links
   - [ ] Company info
   - [ ] Social media links
   - [ ] Copyright notice

3. **View/home.php** - Homepage
   - [ ] Hero banner section
   - [ ] Promotional banners
   - [ ] Featured books carousel
   - [ ] Best-selling books section
   - [ ] New arrivals section
   - [ ] Category showcase
   - [ ] Call-to-action buttons

4. **View/books.php** - Book listing page
   - [ ] Category filter sidebar
   - [ ] Search bar
   - [ ] Sort options (price, popularity, newest)
   - [ ] Book grid/list display
   - [ ] Pagination controls
   - [ ] Book card with:
     - Cover image
     - Title
     - Author
     - Price
     - Rating (if available)
     - Add to cart button

5. **View/book_detail.php** - Single book detail page
   - [ ] Book cover image (large)
   - [ ] Book information:
     - Title
     - Author
     - Publisher
     - ISBN
     - Pages
     - Publication year
     - Language
     - Category
   - [ ] Description/Synopsis
   - [ ] Price display with discount (if any)
   - [ ] Stock status
   - [ ] Quantity selector
   - [ ] Add to cart button
   - [ ] Add to wishlist button (optional)
   - [ ] Customer reviews section
   - [ ] Related books (optional)

6. **View/registration.php** - Sign-up page
   - [ ] Registration form with fields:
     - Full name
     - Email
     - Password
     - Confirm password
     - Phone
     - Gender
     - Date of birth (optional)
     - Address
   - [ ] Form validation
   - [ ] Submit button
   - [ ] Link to login page

7. **View/login.php** - Login page
   - [ ] Login form with fields:
     - Email
     - Password
     - Remember me checkbox
   - [ ] Submit button
   - [ ] Forgot password link
   - [ ] Sign-up link

8. **View/forgot_password.php** - Password recovery request
   - [ ] Email input field
   - [ ] Submit button
   - [ ] Back to login link
   - [ ] Success/error messages

9. **View/reset_password.php** - Password reset page
   - [ ] New password field
   - [ ] Confirm password field
   - [ ] Submit button
   - [ ] Password strength indicator

10. **View/cart.php** - Shopping cart page
    - [ ] Cart items table with:
      - Book image (thumbnail)
      - Book title
      - Unit price
      - Quantity (editable)
      - Item total
      - Remove button
    - [ ] Cart summary:
      - Subtotal
      - Tax (if applicable)
      - Shipping (if applicable)
      - Coupon code input (optional)
      - Total amount
    - [ ] Continue shopping button
    - [ ] Proceed to checkout button
    - [ ] Empty cart message (if empty)

11. **View/order.php** - Checkout/Order form page
    - [ ] Order review section (items summary)
    - [ ] Delivery address form:
      - Recipient name
      - Street address
      - City/Province
      - Postal code
      - Phone number
    - [ ] Payment method selector:
      - Credit card
      - Debit card
      - PayPal (if integrated)
      - Other options
    - [ ] Order total display
    - [ ] Place order button
    - [ ] Cancel button

12. **View/banners.php** - Banner section (optional)
    - [ ] Promotional carousel
    - [ ] Banner rotation
    - [ ] CTA buttons

**Deliverables:**
- ✅ All view files created
- ✅ Responsive design implemented
- ✅ Forms with validation
- ✅ Vietnamese UI text

---

### Phase 4.2: Create Admin Views
**Objective:** Build admin management interface (Vietnamese)

**Files to Create:**

1. **Admin/View/login.php** - Admin login
   - [ ] Admin login form
   - [ ] Username/email field
   - [ ] Password field
   - [ ] Remember me checkbox

2. **Admin/View/header.php** - Admin header
   - [ ] Admin logo/branding
   - [ ] Admin navigation menu
   - [ ] Logout link
   - [ ] Admin profile link

3. **Admin/View/footer.php** - Admin footer
   - [ ] Company info
   - [ ] Copyright

4. **Admin/View/books.php** - Book management list
   - [ ] Book list table with columns:
     - Book ID
     - Title
     - Author
     - Publisher
     - Category
     - Price
     - Stock
     - Status
     - Actions (Edit, Delete)
   - [ ] Add new book button
   - [ ] Search/filter options
   - [ ] Pagination

5. **Admin/View/add_books.php** - Add new book
   - [ ] Book form with fields:
     - Title
     - Author (dropdown)
     - Publisher (dropdown)
     - Category (dropdown)
     - ISBN
     - Price
     - Original price
     - Description
     - Pages
     - Publication year
     - Language
     - Stock quantity
     - Cover image upload
     - Featured status checkbox
   - [ ] Submit button
   - [ ] Cancel button

6. **Admin/View/edit_books.php** - Edit existing book
   - [ ] Pre-filled form with existing data
   - [ ] Same fields as add form
   - [ ] Update button
   - [ ] Delete button
   - [ ] Cancel button

7. **Admin/View/categories.php** - Category management
   - [ ] Category list table
   - [ ] Add category button
   - [ ] Edit/Delete actions

8. **Admin/View/add_category.php** - Add category
   - [ ] Category form with:
     - Category name
     - Description
     - Sort order
     - Status checkbox

9. **Admin/View/edit_category.php** - Edit category
   - [ ] Pre-filled form
   - [ ] Update button

10. **Admin/View/authors.php** - Author management
    - [ ] Author list table
    - [ ] Add/Edit/Delete actions

11. **Admin/View/publishers.php** - Publisher management
    - [ ] Publisher list table
    - [ ] Add/Edit/Delete actions

12. **Admin/View/orders.php** - Order management
    - [ ] Order list table with:
      - Order ID
      - Customer name
      - Order date
      - Total amount
      - Status
      - Actions
    - [ ] Filter by status
    - [ ] Search functionality

13. **Admin/View/reports.php** - Statistics/Reports
    - [ ] Dashboard with KPIs:
      - Total revenue
      - Total orders
      - Total customers
      - Top-selling books
    - [ ] Revenue chart (by day/month/year)
    - [ ] Order count chart
    - [ ] Popular categories
    - [ ] Best-selling books

**Deliverables:**
- ✅ All admin views created
- ✅ Admin dashboard designed
- ✅ CRUD forms for all entities
- ✅ Reporting interface created

---

## 🎨 PHASE 5: STYLING & ASSETS

### Phase 5.1: CSS Styling
**Objective:** Create responsive design and styling

**Files to Create:**

1. **Content/CSS/bookstore.css** - Main stylesheet
   - [ ] Global styles
     - Body, typography
     - Colors and variables
     - Responsive breakpoints
   - [ ] Layout styles
     - Header/navigation
     - Footer
     - Main container
     - Sidebar
   - [ ] Component styles
     - Buttons
     - Forms
     - Cards
     - Modals
   - [ ] Page-specific styles
     - Homepage
     - Book listing
     - Book detail
     - Cart
     - Admin panels
   - [ ] Responsive design
     - Mobile (< 768px)
     - Tablet (768px - 1024px)
     - Desktop (> 1024px)

**Deliverables:**
- ✅ Complete CSS framework
- ✅ Bootstrap integration
- ✅ Responsive design
- ✅ Consistent branding

---

### Phase 5.2: JavaScript Libraries
**Objective:** Integrate frontend libraries via npm/CDN

**Libraries to Install:**
- [ ] Bootstrap 4
- [ ] jQuery
- [ ] Font Awesome (icons)
- [ ] Moment.js (date formatting)
- [ ] Chart.js (for admin reports)
- [ ] DataTables (for admin tables)

**Tasks:**
- [ ] Setup package.json
- [ ] Install dependencies via npm
- [ ] Or use CDN links in HTML files
- [ ] Create custom JavaScript file for interactions

**Deliverables:**
- ✅ All libraries installed/linked
- ✅ Custom scripts created

---

### Phase 5.3: Images & Assets
**Objective:** Organize image assets

**Folder Structure:**
- [ ] `Content/images/books/` - Book cover images (600x800px recommended)
- [ ] `Content/images/banners/` - Promotional banners (1200x400px recommended)
- [ ] `Content/images/icons/` - UI icons
- [ ] `Content/images/logo/` - Company logo

**Image Optimization:**
- [ ] Compress all images
- [ ] Use appropriate formats (JPG for photos, PNG for transparent)
- [ ] Lazy load images for performance

**Deliverables:**
- ✅ Image directories created
- ✅ Sample images added
- ✅ Optimization guidelines documented

---

## 🔐 PHASE 6: SECURITY & AUTHENTICATION

### Phase 6.1: Password Security
**Objective:** Implement secure password handling

**Tasks:**
- [ ] Use `password_hash()` for storing passwords
  - Algorithm: bcrypt (PASSWORD_BCRYPT)
  - Cost: 10-12 (default is fine)
- [ ] Use `password_verify()` for password comparison
- [ ] Never store plain text passwords
- [ ] Implement password strength requirements:
  - Minimum 8 characters
  - Mix of uppercase and lowercase
  - Include numbers
  - Include special characters

**Code Example:**
```php
// Hashing password during registration
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Verifying password during login
$stored_hash = $customer['password'];
$entered_password = $_POST['password'];
if (password_verify($entered_password, $stored_hash)) {
    // Login successful
} else {
    // Login failed
}
```

**Deliverables:**
- ✅ Password hashing implemented
- ✅ Password verification working
- ✅ Security guidelines documented

---

### Phase 6.2: SQL Injection Prevention
**Objective:** Use prepared statements for all queries

**Tasks:**
- [ ] Replace all direct SQL queries with prepared statements
- [ ] Use MySQLi prepared statements or PDO
- [ ] Never concatenate user input directly into SQL

**Code Example:**
```php
// ❌ WRONG - Vulnerable to SQL injection
$query = "SELECT * FROM customers WHERE email = '" . $_POST['email'] . "'";

// ✅ CORRECT - Using prepared statements
$stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
$stmt->bind_param("s", $_POST['email']);
$stmt->execute();
$result = $stmt->get_result();
```

**Deliverables:**
- ✅ All queries converted to prepared statements
- ✅ SQL injection vulnerabilities eliminated

---

### Phase 6.3: Input Validation & Sanitization
**Objective:** Validate and sanitize all user inputs

**Tasks:**
- [ ] Validate email format: `filter_var($email, FILTER_VALIDATE_EMAIL)`
- [ ] Sanitize text inputs: `htmlspecialchars()`, `trim()`
- [ ] Validate file uploads (type, size)
- [ ] Implement CSRF protection with tokens
- [ ] Validate numeric inputs (quantity, price)

**Deliverables:**
- ✅ Input validation implemented
- ✅ Data sanitization applied
- ✅ File upload restrictions configured

---

### Phase 6.4: Session Management
**Objective:** Secure session handling

**Tasks:**
- [ ] Initialize session at start of app: `session_start()`
- [ ] Store user ID and email in session
- [ ] Implement session timeout (30 minutes of inactivity)
- [ ] Regenerate session ID after login
- [ ] Use secure session cookies:
  ```php
  session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'secure' => true, // HTTPS only
      'httponly' => true, // No JavaScript access
      'samesite' => 'Lax'
  ]);
  ```

**Deliverables:**
- ✅ Session management configured
- ✅ Session timeout implemented
- ✅ Security best practices applied

---

## 📧 PHASE 7: EMAIL CONFIGURATION

### Phase 7.1: SMTP Setup
**Objective:** Configure email sending functionality

**Tasks:**
1. **Setup PHPMailer:**
   - [ ] Include PHPMailer library files
   - [ ] Configure SMTP settings in `Model/class.smtp.php`

2. **Gmail Configuration:**
   - [ ] Create Gmail account (or use existing)
   - [ ] Enable 2-Factor Authentication on Gmail
   - [ ] Generate App Password (https://myaccount.google.com/apppasswords)
   - [ ] Store credentials in configuration:
     ```php
     $mail->Host = 'smtp.gmail.com';
     $mail->Port = 587;
     $mail->SMTPAuth = true;
     $mail->Username = 'your-email@gmail.com';
     $mail->Password = 'your-app-password'; // NOT regular Gmail password
     $mail->SMTPSecure = 'tls';
     ```

3. **Alternative Email Providers:**
   - [ ] Support for Outlook (smtp.office365.com)
   - [ ] Support for SendGrid
   - [ ] Support for AWS SES
   - [ ] Support for other SMTP services

**Deliverables:**
- ✅ PHPMailer configured
- ✅ SMTP connection working
- ✅ Email sending tested

---

### Phase 7.2: Email Templates
**Objective:** Create email templates for notifications

**Email Types to Create:**

1. **Registration Confirmation**
   - Confirmation message
   - Account activation link (optional)
   - Login link

2. **Password Reset**
   - Reset link with token
   - Expiration time
   - Security warning

3. **Order Confirmation**
   - Order number
   - Item list
   - Total amount
   - Delivery address
   - Tracking information (optional)

4. **Order Status Update**
   - Current status (processing, shipped, delivered)
   - Updated tracking information
   - Estimated delivery date

5. **Newsletter/Promotional**
   - New book announcements
   - Special offers
   - Unsubscribe link

**Deliverables:**
- ✅ Email templates created
- ✅ Dynamic content insertion working
- ✅ Email sending tested for all types

---

## ✅ PHASE 8: TESTING & VALIDATION

### Phase 8.1: Database Testing
**Objective:** Verify database functionality

**Tests:**
- [ ] CRUD operations for each model:
  - [ ] Create new records
  - [ ] Read/retrieve records
  - [ ] Update existing records
  - [ ] Delete records
- [ ] Foreign key relationships working
- [ ] Cascading deletes working correctly
- [ ] Indexes improving query performance
- [ ] Data integrity constraints enforced

**Deliverables:**
- ✅ All database tests passed
- ✅ Sample data verified

---

### Phase 8.2: Functionality Testing
**Objective:** Test all features work as expected

**Customer Features to Test:**
- [ ] Homepage loading with featured books
- [ ] Book browsing and listing
- [ ] Book search functionality
- [ ] Book filtering by category
- [ ] Book detail page display
- [ ] User registration process
- [ ] User login functionality
- [ ] Password reset flow
- [ ] Add to cart functionality
- [ ] Remove from cart
- [ ] Update cart quantities
- [ ] Checkout process
- [ ] Order creation and confirmation
- [ ] Email receipt delivery
- [ ] Order history viewing
- [ ] User profile update

**Admin Features to Test:**
- [ ] Admin login
- [ ] Book CRUD operations
- [ ] Category management
- [ ] Author management
- [ ] Publisher management
- [ ] Order management
- [ ] Order status updates
- [ ] Report generation
- [ ] User management
- [ ] Stock level updates

**Deliverables:**
- ✅ All features tested and working
- ✅ Bug list compiled and fixed
- ✅ Test report documented

---

### Phase 8.3: Security Testing
**Objective:** Verify security measures are in place

**Tests:**
- [ ] SQL injection prevention
- [ ] XSS (Cross-Site Scripting) prevention
- [ ] CSRF (Cross-Site Request Forgery) protection
- [ ] Password encryption verified
- [ ] Session security validated
- [ ] File upload restrictions working
- [ ] Input validation functioning
- [ ] Access control (admin vs customer)

**Deliverables:**
- ✅ Security tests passed
- ✅ Vulnerabilities identified and fixed

---

### Phase 8.4: Performance Testing
**Objective:** Optimize and validate performance

**Tests:**
- [ ] Homepage load time < 3 seconds
- [ ] Book search response < 1 second
- [ ] Database queries optimized
- [ ] Image loading optimized (lazy loading)
- [ ] Browser caching configured
- [ ] Pagination working efficiently
- [ ] Large dataset handling (1000+ books)

**Optimizations:**
- [ ] Add database indexes
- [ ] Implement query caching
- [ ] Minify CSS/JavaScript
- [ ] Compress images
- [ ] Enable gzip compression
- [ ] Use CDN for static assets (optional)

**Deliverables:**
- ✅ Performance optimized
- ✅ Load times improved
- ✅ Scalability verified

---

### Phase 8.5: Browser Compatibility
**Objective:** Test across different browsers

**Browsers to Test:**
- [ ] Google Chrome (latest)
- [ ] Mozilla Firefox (latest)
- [ ] Microsoft Edge (latest)
- [ ] Safari (if on Mac)
- [ ] Mobile browsers (Chrome Mobile, Safari iOS)

**Tests:**
- [ ] Responsive design on mobile
- [ ] Responsive design on tablet
- [ ] Responsive design on desktop
- [ ] Form functionality
- [ ] JavaScript execution
- [ ] CSS rendering

**Deliverables:**
- ✅ Cross-browser compatibility confirmed
- ✅ Responsive design verified

---

## 🚀 PHASE 9: DEPLOYMENT

### Phase 9.1: Production Environment Setup
**Objective:** Prepare production server

**Tasks:**
- [ ] Choose hosting provider (shared/VPS/cloud)
- [ ] Setup production server (Apache + MySQL + PHP)
- [ ] Configure SSL certificate (HTTPS)
- [ ] Setup domain name and DNS
- [ ] Configure server security:
  - [ ] Firewall rules
  - [ ] SSH key authentication
  - [ ] Regular backups

**Deliverables:**
- ✅ Production server ready
- ✅ SSL certificate installed
- ✅ Domain configured

---

### Phase 9.2: Code Deployment
**Objective:** Deploy application to production

**Tasks:**
- [ ] Copy all project files to server
- [ ] Update database connection for production
- [ ] Update email SMTP for production
- [ ] Set proper file permissions (755 for folders, 644 for files)
- [ ] Enable production error handling (don't display errors publicly)
- [ ] Setup automated backups
- [ ] Configure log files

**Deliverables:**
- ✅ Code deployed
- ✅ Configuration updated
- ✅ Website accessible at domain

---

### Phase 9.3: Database Migration
**Objective:** Migrate database to production

**Tasks:**
- [ ] Create production database
- [ ] Import database schema
- [ ] Import/setup initial data
- [ ] Configure database user with limited privileges
- [ ] Setup regular backups
- [ ] Test database connections

**Deliverables:**
- ✅ Production database created
- ✅ Data migrated successfully
- ✅ Backups configured

---

### Phase 9.4: Monitoring & Maintenance
**Objective:** Setup monitoring and maintenance procedures

**Tasks:**
- [ ] Setup error logging (error.log file)
- [ ] Monitor server performance
- [ ] Monitor database performance
- [ ] Setup uptime monitoring
- [ ] Regular security updates
- [ ] Regular backups
- [ ] Database optimization (monthly)
- [ ] Cache clearing procedures

**Deliverables:**
- ✅ Monitoring configured
- ✅ Maintenance schedule created
- ✅ Emergency procedures documented

---

## 📚 PHASE 10: DOCUMENTATION & HANDOVER

### Phase 10.1: Code Documentation
**Objective:** Document codebase for future maintenance

**Documentation to Create:**
- [ ] Code comments (inline)
  - [ ] Class documentation (PHPDoc)
  - [ ] Method documentation
  - [ ] Complex logic explanation
- [ ] README.md file
  - [ ] Project overview
  - [ ] Installation instructions
  - [ ] Configuration guide
  - [ ] Usage guide
  - [ ] Troubleshooting
- [ ] Database documentation
  - [ ] ER diagram
  - [ ] Table relationships
  - [ ] Field descriptions
- [ ] API documentation (if API exists)
  - [ ] Endpoints
  - [ ] Parameters
  - [ ] Response formats
  - [ ] Error handling

**Deliverables:**
- ✅ Code well-documented
- ✅ README completed
- ✅ Database documentation created

---

### Phase 10.2: User Manuals
**Objective:** Create guides for end users

**Manuals to Create:**
- [ ] **Customer User Guide**
  - Browsing books
  - Creating account
  - Login/logout
  - Adding to cart
  - Checkout process
  - Viewing orders
  - Resetting password
  - Contacting support

- [ ] **Admin User Guide**
  - Admin login
  - Managing books
  - Managing categories
  - Managing authors/publishers
  - Managing orders
  - Viewing reports
  - User management
  - System settings

**Deliverables:**
- ✅ Customer manual created
- ✅ Admin manual created
- ✅ Video tutorials (optional)

---

### Phase 10.3: Training & Handover
**Objective:** Train team on system usage and maintenance

**Training Topics:**
- [ ] System architecture overview
- [ ] Code structure and organization
- [ ] Database schema
- [ ] Common maintenance tasks
- [ ] Troubleshooting procedures
- [ ] How to add new features
- [ ] Security best practices
- [ ] Backup and recovery procedures

**Deliverables:**
- ✅ Training session conducted
- ✅ Training materials provided
- ✅ Knowledge transfer completed

---

## 🎁 PHASE 11: OPTIONAL ENHANCEMENTS

### Phase 11.1: Advanced Features
**Recommended Enhancements:**

**Customer Features:**
- [ ] Wishlist functionality
- [ ] Book comparison tool
- [ ] Advanced filtering and sorting
- [ ] Book ratings and reviews
- [ ] Book recommendations (AI-based)
- [ ] Discount coupon system
- [ ] Newsletter subscription
- [ ] Social sharing
- [ ] Save for later
- [ ] Order tracking

**Admin Features:**
- [ ] Advanced reporting with charts
- [ ] Inventory alerts (low stock)
- [ ] Email campaign management
- [ ] Customer analytics
- [ ] Sales forecasting
- [ ] Coupon management
- [ ] Bulk import/export books
- [ ] Multi-user admin support
- [ ] Audit logs

**Technical Enhancements:**
- [ ] REST API for mobile apps
- [ ] Payment gateway integration (Stripe, PayPal)
- [ ] SMS notifications
- [ ] Social media login (Google, Facebook)
- [ ] Two-factor authentication
- [ ] Caching layer (Redis)
- [ ] Search optimization (Elasticsearch)
- [ ] CDN integration
- [ ] Multi-language support

**Deliverables:**
- ✅ Enhancements implemented (as prioritized)
- ✅ Additional testing performed
- ✅ Documentation updated

---

### Phase 11.2: Mobile App Development
**Optional:** Develop mobile apps using the REST API

**Platforms:**
- [ ] iOS app (Swift)
- [ ] Android app (Kotlin/Java)
- [ ] Progressive Web App (PWA)

**Deliverables:**
- ✅ Mobile apps created (if applicable)
- ✅ API integration completed
- ✅ App testing completed

---

## 📊 PROJECT TIMELINE ESTIMATE

| Phase | Tasks | Duration | Status |
|-------|-------|----------|--------|
| 1. Setup & Infrastructure | 4 tasks | 2-3 days | Not Started |
| 2. Database Layer | 2 tasks | 3-4 days | Not Started |
| 3. Business Logic | 1 task | 5-7 days | Not Started |
| 4. Presentation Layer | 2 tasks | 7-10 days | Not Started |
| 5. Styling & Assets | 3 tasks | 3-5 days | Not Started |
| 6. Security | 4 tasks | 3-5 days | Not Started |
| 7. Email Configuration | 2 tasks | 1-2 days | Not Started |
| 8. Testing & Validation | 5 tasks | 5-7 days | Not Started |
| 9. Deployment | 4 tasks | 2-3 days | Not Started |
| 10. Documentation | 3 tasks | 3-4 days | Not Started |
| **TOTAL** | **33 tasks** | **38-54 days** | **~2 months** |

---

## 👥 TEAM REQUIREMENTS

**For Small Team (3-5 people):**
- 1 Project Manager
- 2 Backend Developers (PHP, MySQL)
- 1 Frontend Developer (HTML, CSS, JavaScript)
- 1 QA/Tester

**For Solo Developer:**
- Handle all roles
- Estimated time: 3-4 months

---

## 🎯 SUCCESS CRITERIA

### Phase Completion Checklist:

**Phase 1:** ✅ Environment ready, project structure created  
**Phase 2:** ✅ Database created with sample data  
**Phase 3:** ✅ All business logic implemented and tested  
**Phase 4:** ✅ All UI templates created and styled  
**Phase 5:** ✅ Responsive design verified  
**Phase 6:** ✅ Security measures implemented  
**Phase 7:** ✅ Email sending working  
**Phase 8:** ✅ All tests passed, no critical bugs  
**Phase 9:** ✅ Live on production server  
**Phase 10:** ✅ Documentation complete  

---

## 📞 SUPPORT & RESOURCES

**Helpful Resources:**
- PHP Manual: https://www.php.net/manual/
- MySQL Documentation: https://dev.mysql.com/doc/
- Bootstrap Documentation: https://getbootstrap.com/docs/4.6/
- PHPMailer Documentation: https://github.com/PHPMailer/PHPMailer
- OWASP Security Guide: https://owasp.org/

**Getting Help:**
- Check project logs for error messages
- Review SMTP_FIX_GUIDE.md for email issues
- Test database connection using phpMyAdmin
- Use browser developer tools for frontend debugging
- Check server error logs for PHP errors

---

## 🏁 CONCLUSION

This comprehensive implementation plan provides a structured approach to building the BookStore e-commerce website. Follow the phases sequentially, and use this document as a checklist to track progress.

**Key Success Factors:**
✅ Follow the MVC architecture strictly  
✅ Implement security best practices from the start  
✅ Test thoroughly at each phase  
✅ Document code as you develop  
✅ Backup frequently during development  
✅ Get user feedback early and often  

---

**Document Version:** 1.0  
**Last Updated:** December 9, 2025  
**Status:** Ready for Implementation

