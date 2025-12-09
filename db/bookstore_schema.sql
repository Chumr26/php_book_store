-- =============================================
-- BOOKSTORE DATABASE - MySQL Structure Only
-- Created: December 9, 2025
-- Description: Database schema and table structure for online bookstore
-- =============================================

-- Drop database if exists and create new one
DROP DATABASE IF EXISTS bookstore;
CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookstore;

-- =============================================
-- TABLE STRUCTURE
-- =============================================

-- Table: Admin
CREATE TABLE admin (
    id_admin INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Customers
CREATE TABLE customers (
    id_customer INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Categories
CREATE TABLE categories (
    id_category INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Authors
CREATE TABLE authors (
    id_author INT PRIMARY KEY AUTO_INCREMENT,
    author_name VARCHAR(100) NOT NULL,
    pen_name VARCHAR(100),
    biography TEXT,
    date_of_birth DATE,
    date_of_death DATE,
    nationality VARCHAR(50),
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (author_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Publishers
CREATE TABLE publishers (
    id_publisher INT PRIMARY KEY AUTO_INCREMENT,
    publisher_name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(15),
    email VARCHAR(100),
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (publisher_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Books
CREATE TABLE books (
    id_book INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    id_author INT,
    id_publisher INT,
    id_category INT,
    isbn VARCHAR(13) UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    cover_image VARCHAR(255),
    description TEXT,
    pages INT,
    publication_year YEAR,
    language VARCHAR(50) DEFAULT 'English',
    stock_quantity INT DEFAULT 0,
    view_count INT DEFAULT 0,
    sale_count INT DEFAULT 0,
    status ENUM('available', 'out_of_stock', 'discontinued') DEFAULT 'available',
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_author) REFERENCES authors(id_author) ON DELETE SET NULL,
    FOREIGN KEY (id_publisher) REFERENCES publishers(id_publisher) ON DELETE SET NULL,
    FOREIGN KEY (id_category) REFERENCES categories(id_category) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_isbn (isbn),
    INDEX idx_price (price),
    INDEX idx_category (id_category),
    INDEX idx_author (id_author),
    INDEX idx_publisher (id_publisher)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Orders
CREATE TABLE orders (
    id_order INT PRIMARY KEY AUTO_INCREMENT,
    id_customer INT,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('COD', 'transfer', 'card', 'paypal', 'stripe') DEFAULT 'COD',
    payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    recipient_name VARCHAR(100) NOT NULL,
    delivery_address TEXT NOT NULL,
    recipient_phone VARCHAR(15) NOT NULL,
    recipient_email VARCHAR(100),
    notes TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_customer) REFERENCES customers(id_customer) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_customer (id_customer),
    INDEX idx_status (status),
    INDEX idx_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Order Items
CREATE TABLE order_items (
    id_item INT PRIMARY KEY AUTO_INCREMENT,
    id_order INT NOT NULL,
    id_book INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_order) REFERENCES orders(id_order) ON DELETE CASCADE,
    FOREIGN KEY (id_book) REFERENCES books(id_book) ON DELETE RESTRICT,
    INDEX idx_order (id_order),
    INDEX idx_book (id_book)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Reviews
CREATE TABLE reviews (
    id_review INT PRIMARY KEY AUTO_INCREMENT,
    id_book INT NOT NULL,
    id_customer INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(255),
    content TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_book) REFERENCES books(id_book) ON DELETE CASCADE,
    FOREIGN KEY (id_customer) REFERENCES customers(id_customer) ON DELETE CASCADE,
    INDEX idx_book (id_book),
    INDEX idx_customer (id_customer),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Shopping Cart
CREATE TABLE shopping_cart (
    id_cart_item INT PRIMARY KEY AUTO_INCREMENT,
    id_customer INT NOT NULL,
    id_book INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_customer) REFERENCES customers(id_customer) ON DELETE CASCADE,
    FOREIGN KEY (id_book) REFERENCES books(id_book) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (id_customer, id_book),
    INDEX idx_customer (id_customer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Discount Coupons
CREATE TABLE coupons (
    id_coupon INT PRIMARY KEY AUTO_INCREMENT,
    coupon_code VARCHAR(20) UNIQUE NOT NULL,
    campaign_name VARCHAR(100) NOT NULL,
    discount_type ENUM('percent', 'fixed') DEFAULT 'percent',
    discount_value DECIMAL(10,2) NOT NULL,
    minimum_purchase DECIMAL(10,2) DEFAULT 0,
    maximum_discount DECIMAL(10,2),
    usage_limit INT DEFAULT 1,
    used_count INT DEFAULT 0,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (coupon_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Wishlist
CREATE TABLE wishlist (
    id_wishlist INT PRIMARY KEY AUTO_INCREMENT,
    id_customer INT NOT NULL,
    id_book INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_customer) REFERENCES customers(id_customer) ON DELETE CASCADE,
    FOREIGN KEY (id_book) REFERENCES books(id_book) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist_item (id_customer, id_book),
    INDEX idx_customer (id_customer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Banners
CREATE TABLE banners (
    id_banner INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    link_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- CREATE VIEWS FOR REPORTING
-- =============================================

-- View: Best Selling Books
CREATE VIEW v_best_selling_books AS
SELECT 
    b.id_book,
    b.title,
    a.author_name,
    p.publisher_name,
    b.price,
    b.sale_count,
    COUNT(DISTINCT r.id_review) as review_count,
    COALESCE(AVG(r.rating), 0) as average_rating
FROM books b
LEFT JOIN authors a ON b.id_author = a.id_author
LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
LEFT JOIN reviews r ON b.id_book = r.id_book AND r.status = 'approved'
GROUP BY b.id_book
ORDER BY b.sale_count DESC;

-- View: Monthly Revenue Report
CREATE VIEW v_monthly_revenue AS
SELECT 
    YEAR(order_date) as year,
    MONTH(order_date) as month,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    COUNT(DISTINCT id_customer) as total_customers
FROM orders
WHERE status IN ('completed', 'shipping')
GROUP BY YEAR(order_date), MONTH(order_date)
ORDER BY year DESC, month DESC;

-- View: Top Customers
CREATE VIEW v_top_customers AS
SELECT 
    c.id_customer,
    c.full_name,
    c.email,
    c.phone,
    COUNT(o.id_order) as total_orders,
    SUM(o.total_amount) as total_spent
FROM customers c
LEFT JOIN orders o ON c.id_customer = o.id_customer
WHERE o.status IN ('completed')
GROUP BY c.id_customer
ORDER BY total_spent DESC;

-- =============================================
-- CREATE STORED PROCEDURES
-- =============================================

-- Procedure: Add book to shopping cart
DELIMITER $$
CREATE PROCEDURE sp_add_to_cart(
    IN p_id_customer INT,
    IN p_id_book INT,
    IN p_quantity INT
)
BEGIN
    DECLARE v_count INT;
    
    -- Check if book already in cart
    SELECT COUNT(*) INTO v_count 
    FROM shopping_cart 
    WHERE id_customer = p_id_customer AND id_book = p_id_book;
    
    IF v_count > 0 THEN
        -- Update quantity
        UPDATE shopping_cart 
        SET quantity = quantity + p_quantity 
        WHERE id_customer = p_id_customer AND id_book = p_id_book;
    ELSE
        -- Add new item
        INSERT INTO shopping_cart (id_customer, id_book, quantity) 
        VALUES (p_id_customer, p_id_book, p_quantity);
    END IF;
END$$
DELIMITER ;

-- Procedure: Create order from shopping cart
DELIMITER $$
CREATE PROCEDURE sp_create_order(
    IN p_id_customer INT,
    IN p_recipient_name VARCHAR(100),
    IN p_delivery_address TEXT,
    IN p_recipient_phone VARCHAR(15),
    IN p_recipient_email VARCHAR(100),
    IN p_payment_method VARCHAR(20),
    IN p_notes TEXT,
    OUT p_id_order INT,
    OUT p_order_number VARCHAR(20)
)
BEGIN
    DECLARE v_total_amount DECIMAL(10,2);
    DECLARE v_order_number VARCHAR(20);
    
    -- Calculate total from shopping cart
    SELECT SUM(b.price * sc.quantity) INTO v_total_amount
    FROM shopping_cart sc
    JOIN books b ON sc.id_book = b.id_book
    WHERE sc.id_customer = p_id_customer;
    
    -- Generate order number
    SET v_order_number = CONCAT('ORD', LPAD(FLOOR(RAND() * 999999), 6, '0'));
    
    -- Create order
    INSERT INTO orders (
        id_customer, order_number, total_amount, payment_method,
        recipient_name, delivery_address, recipient_phone, recipient_email, notes
    ) VALUES (
        p_id_customer, v_order_number, v_total_amount, p_payment_method,
        p_recipient_name, p_delivery_address, p_recipient_phone, p_recipient_email, p_notes
    );
    
    SET p_id_order = LAST_INSERT_ID();
    SET p_order_number = v_order_number;
    
    -- Transfer shopping cart to order items
    INSERT INTO order_items (id_order, id_book, quantity, unit_price, total_price)
    SELECT 
        p_id_order,
        sc.id_book,
        sc.quantity,
        b.price,
        b.price * sc.quantity
    FROM shopping_cart sc
    JOIN books b ON sc.id_book = b.id_book
    WHERE sc.id_customer = p_id_customer;
    
    -- Update stock quantity and sale count
    UPDATE books b
    JOIN shopping_cart sc ON b.id_book = sc.id_book
    SET 
        b.stock_quantity = b.stock_quantity - sc.quantity,
        b.sale_count = b.sale_count + sc.quantity
    WHERE sc.id_customer = p_id_customer;
    
    -- Clear shopping cart
    DELETE FROM shopping_cart WHERE id_customer = p_id_customer;
END$$
DELIMITER ;

-- =============================================
-- CREATE TRIGGERS
-- =============================================

-- Trigger: Update view count when review is added
DELIMITER $$
CREATE TRIGGER tr_update_view_count
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    UPDATE books 
    SET view_count = view_count + 1 
    WHERE id_book = NEW.id_book;
END$$
DELIMITER ;

-- Trigger: Check stock quantity before adding to cart
DELIMITER $$
CREATE TRIGGER tr_check_stock_quantity
BEFORE INSERT ON shopping_cart
FOR EACH ROW
BEGIN
    DECLARE v_stock INT;
    
    SELECT stock_quantity INTO v_stock
    FROM books
    WHERE id_book = NEW.id_book;
    
    IF v_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock quantity for this book';
    END IF;
END$$
DELIMITER ;

-- =============================================
-- COMPLETION MESSAGE
-- =============================================
SELECT 'Database structure created successfully!' AS Status;
