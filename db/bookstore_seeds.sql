-- =============================================
-- BOOKSTORE DATABASE - Seed Data Only
-- Created: December 9, 2025
-- Description: Sample data for online bookstore with international books
-- Note: Run bookstore_schema.sql first before importing this file
-- =============================================

USE bookstore;

-- =============================================
-- SEED DATA - ADMIN
-- =============================================
-- Password: admin123 (hashed with PASSWORD_HASH)
INSERT INTO admin (full_name, username, password, email) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bookstore.com');

-- =============================================
-- SEED DATA - CATEGORIES
-- =============================================
INSERT INTO categories (category_name, description, sort_order) VALUES
('Fiction', 'Novels and short stories', 1),
('Non-Fiction', 'Educational and informative books', 2),
('Business', 'Books about business, management, and entrepreneurship', 3),
('Self-Help', 'Personal development and self-improvement books', 4),
('Science & Technology', 'Books about science, technology, and programming', 5),
('History', 'Historical books and chronicles', 6),
('Biography', 'Life stories and memoirs', 7),
('Mystery & Thriller', 'Mystery, crime, and thriller novels', 8),
('Children', 'Books for children and young readers', 9),
('Fantasy & Science Fiction', 'Fantasy and science fiction novels', 10);

-- =============================================
-- SEED DATA - AUTHORS
-- =============================================
INSERT INTO authors (author_name, pen_name, biography, date_of_birth, nationality) VALUES
('George Orwell', NULL, 'English novelist and political commentator, known for dystopian fiction', '1903-06-25', 'British'),
('J.K. Rowling', NULL, 'Author of the Harry Potter series', '1965-07-31', 'British'),
('Stephen King', NULL, 'American author of horror and thriller novels', '1947-09-21', 'American'),
('Harper Lee', NULL, 'American author of To Kill a Mockingbird', '1926-04-28', 'American'),
('Jane Austen', NULL, 'Romantic fiction novelist', '1775-12-16', 'British'),
('F. Scott Fitzgerald', NULL, 'American author of The Great Gatsby', '1896-09-24', 'American'),
('Agatha Christie', NULL, 'British writer of detective fiction', '1890-01-15', 'British'),
('Paulo Coelho', NULL, 'Brazilian author of philosophical fiction', '1947-08-24', 'Brazilian'),
('Haruki Murakami', NULL, 'Contemporary Japanese novelist', '1949-01-12', 'Japanese'),
('Malcolm Gladwell', NULL, 'Canadian author of popular non-fiction', '1954-09-03', 'Canadian');

-- =============================================
-- SEED DATA - PUBLISHERS
-- =============================================
INSERT INTO publishers (publisher_name, address, phone, email, website) VALUES
('Penguin Books', '80 Strand, London', '+44-207-010-3000', 'info@penguin.com', 'https://www.penguin.com'),
('Simon & Schuster', '1230 Avenue of the Americas, New York', '+1-212-698-7000', 'info@simonandschuster.com', 'https://www.simonandschuster.com'),
('Random House', '1745 Broadway, New York', '+1-212-782-9000', 'info@randomhouse.com', 'https://www.randomhouse.com'),
('HachetteLivre', '43, Quai de Grenelle, Paris', '+33-1-4316-1500', 'info@hachette.com', 'https://www.hachette.com'),
('Bloomsbury Publishing', '50 Bedford Square, London', '+44-207-494-2111', 'info@bloomsbury.com', 'https://www.bloomsbury.com'),
('Little, Brown and Company', '1290 Avenue of the Americas, New York', '+1-212-522-8700', 'info@littlebrown.com', 'https://www.littlebrown.com'),
('Scholastic Press', '557 Broadway, New York', '+1-212-343-6100', 'info@scholastic.com', 'https://www.scholastic.com'),
('Oxford University Press', 'Great Clarendon Street, Oxford', '+44-185-267-6767', 'info@oup.com', 'https://www.oup.com'),
('Norton Publisher', 'W. W. Norton & Company', '+1-212-790-4456', 'info@wwnorton.com', 'https://www.wwnorton.com'),
('Doubleday', '1745 Broadway, New York', '+1-212-782-9000', 'info@doubleday.com', 'https://www.doubleday.com');

-- =============================================
-- SEED DATA - BOOKS (International Bestsellers)
-- =============================================
INSERT INTO books (title, id_author, id_publisher, id_category, isbn, price, original_price, cover_image, description, pages, publication_year, language, stock_quantity, is_featured) VALUES
-- Fiction Bestsellers
('1984', 1, 1, 1, '9780451524935', 15.99, 18.99, '1984.jpg', 'A dystopian novel set in a totalitarian society where Big Brother watches everyone', 328, 2003, 'English', 150, TRUE),
('Harry Potter and the Philosopher\'s Stone', 2, 5, 10, '9780747532699', 12.99, 15.99, 'hp_ps.jpg', 'The beginning of the magical journey at Hogwarts School of Witchcraft and Wizardry', 309, 1997, 'English', 200, TRUE),
('The Shining', 3, 2, 8, '9780385333312', 14.99, 17.99, 'the_shining.jpg', 'A psychological horror novel about a family isolated in a haunted hotel during winter', 447, 1977, 'English', 120, TRUE),
('To Kill a Mockingbird', 4, 3, 1, '9780061120084', 13.99, 16.99, 'tkam.jpg', 'A gripping tale of racial injustice and childhood innocence in the Deep South', 324, 1960, 'English', 180, TRUE),
('Pride and Prejudice', 5, 1, 1, '9780141439518', 11.99, 14.99, 'pride_prejudice.jpg', 'A timeless romance about Elizabeth Bennet and Mr. Darcy', 279, 1813, 'English', 165, FALSE),
('The Great Gatsby', 6, 4, 1, '9780743273565', 13.99, 16.99, 'gatsby.jpg', 'A Jazz Age novel about wealth, love, and the American Dream', 180, 1925, 'English', 140, TRUE),
('Murder on the Orient Express', 7, 6, 8, '9780062693662', 12.99, 15.99, 'murder_orient.jpg', 'A classic detective mystery featuring Hercule Poirot', 256, 1934, 'English', 110, FALSE),
('The Alchemist', 8, 5, 1, '9780062315007', 11.99, 14.99, 'alchemist.jpg', 'A philosophical novel about personal destiny and following your dreams', 224, 1988, 'English', 190, TRUE),
('Kafka on the Shore', 9, 2, 10, '9780739313558', 16.99, 19.99, 'kafka_shore.jpg', 'A surreal journey combining fantasy with realism through parallel narratives', 505, 2002, 'English', 95, TRUE),
('Norwegian Wood', 9, 2, 1, '9780099282715', 15.99, 18.99, 'norwegian_wood.jpg', 'A romantic novel exploring love and loss in 1960s Tokyo', 389, 1987, 'English', 125, FALSE),

-- Non-Fiction & Business
('The Tipping Point', 10, 3, 3, '9780316346627', 16.99, 19.99, 'tipping_point.jpg', 'How little things can make a big difference in business and life', 301, 2000, 'English', 140, TRUE),
('Outliers', 10, 3, 3, '9780316017923', 17.99, 20.99, 'outliers.jpg', 'The story of success and what extraordinary achievers have in common', 309, 2008, 'English', 130, TRUE),
('Thinking, Fast and Slow', 10, 3, 4, '9780374533557', 18.99, 21.99, 'thinking_fast.jpg', 'Insights into the two systems of human thought and decision making', 499, 2011, 'English', 110, FALSE),

-- Science & Technology
('A Brief History of Time', NULL, 1, 5, '9780553380163', 14.99, 17.99, 'brief_history.jpg', 'From the Big Bang to Black Holes - A journey through space and time', 256, 1988, 'English', 100, TRUE),
('The Selfish Gene', NULL, 4, 5, '9780199291151', 13.99, 16.99, 'selfish_gene.jpg', 'A revolutionary look at evolution and natural selection', 360, 1976, 'English', 95, FALSE),

-- Children's Books
('Charlotte\'s Web', NULL, 7, 9, '9780061124952', 9.99, 12.99, 'charlottes_web.jpg', 'A heartwarming tale of friendship between a pig and a spider', 192, 1952, 'English', 200, TRUE),
('The Cat in the Hat', NULL, 7, 9, '9780394800011', 7.99, 9.99, 'cat_hat.jpg', 'A whimsical story about a mysterious cat visiting two children', 61, 1957, 'English', 250, TRUE),

-- Mystery & Thriller
('The Girl with the Dragon Tattoo', 3, 6, 8, '9780307269935', 16.99, 19.99, 'dragon_tattoo.jpg', 'A dark mystery involving a journalist and a hacker', 465, 2005, 'English', 130, TRUE),
('Gone Girl', 3, 6, 8, '9780307588371', 15.99, 18.99, 'gone_girl.jpg', 'A psychological thriller about a missing wife and suspicion', 422, 2012, 'English', 145, TRUE);

-- =============================================
-- SEED DATA - CUSTOMERS
-- =============================================
INSERT INTO customers (full_name, email, phone, address, date_of_birth, gender, status) VALUES
('John Smith', 'john.smith@email.com', '+1-212-555-0101', '123 Main Street, New York, NY 10001', '1990-05-15', 'M', 'active'),
('Sarah Johnson', 'sarah.johnson@email.com', '+1-415-555-0102', '456 Market Street, San Francisco, CA 94103', '1992-08-20', 'F', 'active'),
('Michael Brown', 'michael.brown@email.com', '+1-313-555-0103', '789 Michigan Avenue, Detroit, MI 48226', '1988-03-10', 'M', 'active'),
('Emma Davis', 'emma.davis@email.com', '+1-206-555-0104', '321 Pike Place, Seattle, WA 98101', '1995-11-30', 'F', 'active'),
('David Wilson', 'david.wilson@email.com', '+1-617-555-0105', '654 Newbury Street, Boston, MA 02215', '1998-07-25', 'M', 'active');

-- =============================================
-- SEED DATA - ORDERS
-- =============================================
INSERT INTO orders (id_customer, order_number, order_date, total_amount, status, payment_method, payment_status, recipient_name, delivery_address, phone) VALUES
(1, 'ORD000001', '2025-01-15 10:30:00', 89.97, 'completed', 'credit_card', 'paid', 'John Smith', '123 Main Street, New York, NY 10001', '+1-212-555-0101'),
(2, 'ORD000002', '2025-01-18 14:45:00', 154.96, 'completed', 'paypal', 'paid', 'Sarah Johnson', '456 Market Street, San Francisco, CA 94103', '+1-415-555-0102'),
(3, 'ORD000003', '2025-01-20 09:15:00', 127.97, 'processing', 'credit_card', 'paid', 'Michael Brown', '789 Michigan Avenue, Detroit, MI 48226', '+1-313-555-0103'),
(4, 'ORD000004', '2025-01-22 16:20:00', 182.96, 'completed', 'debit_card', 'paid', 'Emma Davis', '321 Pike Place, Seattle, WA 98101', '+1-206-555-0104'),
(5, 'ORD000005', '2025-01-25 11:50:00', 93.96, 'processing', 'credit_card', 'pending', 'David Wilson', '654 Newbury Street, Boston, MA 02215', '+1-617-555-0105');

-- =============================================
-- SEED DATA - ORDER ITEMS
-- =============================================
INSERT INTO order_items (id_order, id_book, quantity, unit_price, total_price) VALUES
-- Order 1 (ORD000001)
(1, 1, 1, 15.99, 15.99),
(1, 6, 2, 13.99, 27.98),
(1, 12, 1, 16.99, 16.99),
(1, 20, 1, 15.99, 15.99),
-- Order 2 (ORD000002)
(2, 2, 1, 12.99, 12.99),
(2, 5, 2, 11.99, 23.98),
(2, 11, 1, 16.99, 16.99),
(2, 17, 1, 9.99, 9.99),
(2, 19, 1, 16.99, 16.99),
(2, 9, 1, 16.99, 16.99),
(2, 7, 1, 11.99, 11.99),
(2, 14, 1, 14.99, 14.99),
(2, 3, 1, 14.99, 14.99),
(2, 18, 1, 15.99, 15.99),
-- Order 3 (ORD000003)
(3, 4, 1, 13.99, 13.99),
(3, 8, 1, 11.99, 11.99),
(3, 10, 2, 15.99, 31.98),
(3, 15, 1, 13.99, 13.99),
(3, 16, 1, 7.99, 7.99),
(3, 13, 1, 18.99, 18.99),
-- Order 4 (ORD000004)
(4, 11, 1, 16.99, 16.99),
(4, 1, 1, 15.99, 15.99),
(4, 6, 2, 13.99, 27.98),
(4, 12, 1, 16.99, 16.99),
(4, 2, 1, 12.99, 12.99),
(4, 9, 1, 16.99, 16.99),
(4, 20, 1, 15.99, 15.99),
(4, 5, 1, 11.99, 11.99),
(4, 7, 1, 11.99, 11.99),
(4, 14, 1, 14.99, 14.99),
(4, 3, 1, 14.99, 14.99),
(4, 18, 1, 15.99, 15.99),
-- Order 5 (ORD000005)
(5, 17, 1, 9.99, 9.99),
(5, 19, 2, 16.99, 33.98),
(5, 15, 1, 13.99, 13.99),
(5, 16, 1, 7.99, 7.99),
(5, 13, 1, 18.99, 18.99),
(5, 10, 1, 15.99, 15.99);

-- =============================================
-- SEED DATA - REVIEWS
-- =============================================
INSERT INTO reviews (id_book, id_customer, rating, title, content, status) VALUES
(1, 1, 5, 'Excellent dystopian novel!', 'A powerful and thought-provoking read. Orwell''s vision remains relevant today.', 'approved'),
(1, 2, 5, 'Must read classic', 'Every person should read this book at least once. Truly eye-opening.', 'approved'),
(2, 3, 5, 'Perfect for all ages', 'The Harry Potter series is magical. Great for both children and adults.', 'approved'),
(3, 4, 4, 'Terrifying and gripping', 'Stephen King at his best. Psychological horror that keeps you engaged throughout.', 'approved'),
(5, 5, 5, 'Timeless romance', 'Jane Austen''s writing is beautiful. A perfect love story that never gets old.', 'approved'),
(6, 1, 5, 'The Great Gatsby - unforgettable', 'Fitzgerald captures the Jazz Age perfectly. A true American classic.', 'approved'),
(7, 2, 5, 'Page-turning mystery', 'Agatha Christie''s best work. The twist ending was absolutely brilliant.', 'approved');

-- =============================================
-- SEED DATA - SHOPPING CART
-- =============================================
INSERT INTO shopping_cart (id_customer, id_book, quantity, added_at) VALUES
(1, 3, 1, '2025-01-26 08:30:00'),
(1, 8, 2, '2025-01-26 08:45:00'),
(2, 11, 1, '2025-01-26 09:15:00'),
(3, 1, 1, '2025-01-26 10:20:00'),
(3, 14, 1, '2025-01-26 10:35:00'),
(4, 6, 1, '2025-01-26 11:00:00');

-- =============================================
-- SEED DATA - COUPONS
-- =============================================
INSERT INTO coupons (coupon_code, campaign_name, discount_type, discount_value, min_order_amount, max_discount, quantity, start_date, end_date, status) VALUES
('NEWYEAR25', 'New Year 2025 Sale', 'percent', 25, 50.00, 100.00, 100, '2025-01-01 00:00:00', '2025-01-31 23:59:59', 'active'),
('BOOKFEST', 'Book Festival Promotion', 'percent', 20, 100.00, 50.00, 50, '2025-04-01 00:00:00', '2025-04-30 23:59:59', 'active'),
('FREESHIP25', 'Free Shipping on Orders Over $25', 'fixed', 5.00, 25.00, 5.00, 200, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'active'),
('STUDENT15', 'Student Discount 15%', 'percent', 15, 30.00, 30.00, 500, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'active');

-- =============================================
-- SEED DATA - WISHLIST
-- =============================================
INSERT INTO wishlist (id_customer, id_book, added_at) VALUES
(1, 4, '2025-01-20 12:30:00'),
(1, 9, '2025-01-20 12:45:00'),
(2, 15, '2025-01-21 14:15:00'),
(3, 2, '2025-01-22 10:00:00'),
(3, 7, '2025-01-22 10:30:00'),
(4, 11, '2025-01-23 15:45:00'),
(5, 18, '2025-01-24 09:20:00');

-- =============================================
-- SEED DATA - BANNERS
-- =============================================
INSERT INTO banners (title, image, link_url, sort_order, status) VALUES
('New Year Sale 2025', 'banner_new_year.jpg', '/promotion/newyear2025', 1, 'active'),
('Bestselling Books', 'banner_bestseller.jpg', '/books/bestseller', 2, 'active'),
('Children''s Books Special', 'banner_children.jpg', '/category/children', 3, 'active'),
('Spring Collection', 'banner_spring.jpg', '/promotion/spring2025', 4, 'active');

-- =============================================
-- COMPLETION MESSAGE
-- =============================================
SELECT 'Seed data inserted successfully!' AS Status;
SELECT COUNT(*) AS 'Total Books' FROM books;
SELECT COUNT(*) AS 'Total Authors' FROM authors;
SELECT COUNT(*) AS 'Total Publishers' FROM publishers;
SELECT COUNT(*) AS 'Total Categories' FROM categories;
SELECT COUNT(*) AS 'Total Customers' FROM customers;
SELECT COUNT(*) AS 'Total Orders' FROM orders;
