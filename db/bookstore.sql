-- =============================================
-- BOOKSTORE DATABASE - MySQL
-- Created: December 9, 2025
-- Description: Complete database structure and seed data for online bookstore
-- =============================================

-- Drop database if exists and create new one
DROP DATABASE IF EXISTS bookstore;
CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookstore;

-- Ensure the import session interprets this script as UTF-8
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- =============================================
-- TABLE STRUCTURE
-- =============================================

-- Table: Admin (Quản trị viên)
CREATE TABLE admin (
    id_admin INT PRIMARY KEY AUTO_INCREMENT,
    ten_admin VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    quyen VARCHAR(20) DEFAULT 'admin',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Khách hàng (Customers)
CREATE TABLE khachhang (
    id_khachhang INT PRIMARY KEY AUTO_INCREMENT,
    ten_khachhang VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    dien_thoai VARCHAR(15),
    dia_chi TEXT,
    ngay_sinh DATE,
    gioi_tinh ENUM('Nam', 'Nữ', 'Khác'),
    trang_thai ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    ngay_dang_ky TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_dien_thoai (dien_thoai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Thể loại sách (Book Categories)
CREATE TABLE theloai (
    id_theloai INT PRIMARY KEY AUTO_INCREMENT,
    ten_theloai VARCHAR(100) NOT NULL,
    mo_ta TEXT,
    thu_tu INT DEFAULT 0,
    trang_thai ENUM('active', 'inactive') DEFAULT 'active',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ten (ten_theloai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Tác giả (Authors)
CREATE TABLE tacgia (
    id_tacgia INT PRIMARY KEY AUTO_INCREMENT,
    ten_tacgia VARCHAR(100) NOT NULL,
    but_danh VARCHAR(100),
    tieu_su TEXT,
    ngay_sinh DATE,
    ngay_mat DATE,
    quoc_tich VARCHAR(50),
    hinh_anh VARCHAR(255),
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ten (ten_tacgia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Nhà xuất bản (Publishers)
CREATE TABLE nhaxuatban (
    id_nxb INT PRIMARY KEY AUTO_INCREMENT,
    ten_nxb VARCHAR(100) NOT NULL,
    dia_chi TEXT,
    dien_thoai VARCHAR(15),
    email VARCHAR(100),
    website VARCHAR(255),
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ten (ten_nxb)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Sách (Books)
CREATE TABLE sach (
    id_sach INT PRIMARY KEY AUTO_INCREMENT,
    ten_sach VARCHAR(255) NOT NULL,
    id_tacgia INT,
    id_nxb INT,
    id_theloai INT,
    isbn VARCHAR(13) UNIQUE,
    gia DECIMAL(10,2) NOT NULL,
    gia_goc DECIMAL(10,2),
    hinh_anh VARCHAR(255),
    mo_ta TEXT,
    so_trang INT,
    nam_xuat_ban YEAR,
    ngon_ngu VARCHAR(50) DEFAULT 'Tiếng Việt',
    so_luong_ton INT DEFAULT 0,
    luot_xem INT DEFAULT 0,
    luot_ban INT DEFAULT 0,
    trang_thai ENUM('available', 'out_of_stock', 'discontinued') DEFAULT 'available',
    noi_bat BOOLEAN DEFAULT FALSE,
    ngay_them TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tacgia) REFERENCES tacgia(id_tacgia) ON DELETE SET NULL,
    FOREIGN KEY (id_nxb) REFERENCES nhaxuatban(id_nxb) ON DELETE SET NULL,
    FOREIGN KEY (id_theloai) REFERENCES theloai(id_theloai) ON DELETE SET NULL,
    INDEX idx_ten (ten_sach),
    INDEX idx_isbn (isbn),
    INDEX idx_gia (gia),
    INDEX idx_theloai (id_theloai),
    INDEX idx_tacgia (id_tacgia),
    INDEX idx_nxb (id_nxb)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Hóa đơn (Orders)
CREATE TABLE hoadon (
    id_hoadon INT PRIMARY KEY AUTO_INCREMENT,
    id_khachhang INT,
    ma_hoadon VARCHAR(20) UNIQUE NOT NULL,
    ngay_dat_hang TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tong_tien DECIMAL(10,2) NOT NULL,
    trang_thai ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    phuong_thuc_thanh_toan ENUM('COD', 'transfer', 'card', 'momo', 'vnpay') DEFAULT 'COD',
    trang_thai_thanh_toan ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    ten_nguoi_nhan VARCHAR(100) NOT NULL,
    dia_chi_giao TEXT NOT NULL,
    sdt_giao VARCHAR(15) NOT NULL,
    email_giao VARCHAR(100),
    ghi_chu TEXT,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_khachhang) REFERENCES khachhang(id_khachhang) ON DELETE SET NULL,
    INDEX idx_ma_hoadon (ma_hoadon),
    INDEX idx_khachhang (id_khachhang),
    INDEX idx_trang_thai (trang_thai),
    INDEX idx_ngay (ngay_dat_hang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Chi tiết hóa đơn (Order Details)
CREATE TABLE chitiet_hoadon (
    id_chitiet INT PRIMARY KEY AUTO_INCREMENT,
    id_hoadon INT NOT NULL,
    id_sach INT NOT NULL,
    so_luong INT NOT NULL,
    gia DECIMAL(10,2) NOT NULL,
    thanh_tien DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_hoadon) REFERENCES hoadon(id_hoadon) ON DELETE CASCADE,
    FOREIGN KEY (id_sach) REFERENCES sach(id_sach) ON DELETE RESTRICT,
    INDEX idx_hoadon (id_hoadon),
    INDEX idx_sach (id_sach)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Đánh giá (Reviews)
CREATE TABLE danhgia (
    id_danhgia INT PRIMARY KEY AUTO_INCREMENT,
    id_sach INT NOT NULL,
    id_khachhang INT NOT NULL,
    so_sao INT CHECK (so_sao BETWEEN 1 AND 5),
    tieu_de VARCHAR(255),
    noi_dung TEXT,
    trang_thai ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    ngay_danh_gia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sach) REFERENCES sach(id_sach) ON DELETE CASCADE,
    FOREIGN KEY (id_khachhang) REFERENCES khachhang(id_khachhang) ON DELETE CASCADE,
    INDEX idx_sach (id_sach),
    INDEX idx_khachhang (id_khachhang),
    INDEX idx_so_sao (so_sao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Giỏ hàng (Cart)
CREATE TABLE giohang (
    id_giohang INT PRIMARY KEY AUTO_INCREMENT,
    id_khachhang INT NOT NULL,
    id_sach INT NOT NULL,
    so_luong INT DEFAULT 1,
    ngay_them TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_khachhang) REFERENCES khachhang(id_khachhang) ON DELETE CASCADE,
    FOREIGN KEY (id_sach) REFERENCES sach(id_sach) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (id_khachhang, id_sach),
    INDEX idx_khachhang (id_khachhang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Mã giảm giá (Coupons)
CREATE TABLE magiamgia (
    id_magiamgia INT PRIMARY KEY AUTO_INCREMENT,
    ma_code VARCHAR(20) UNIQUE NOT NULL,
    ten_chuongtrinh VARCHAR(100) NOT NULL,
    loai_giam ENUM('percent', 'fixed') DEFAULT 'percent',
    gia_tri_giam DECIMAL(10,2) NOT NULL,
    gia_tri_toi_thieu DECIMAL(10,2) DEFAULT 0,
    giam_toi_da DECIMAL(10,2),
    so_luong INT DEFAULT 1,
    da_su_dung INT DEFAULT 0,
    ngay_bat_dau DATETIME NOT NULL,
    ngay_ket_thuc DATETIME NOT NULL,
    trang_thai ENUM('active', 'inactive') DEFAULT 'active',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ma_code (ma_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Wishlist (Danh sách yêu thích)
CREATE TABLE wishlist (
    id_wishlist INT PRIMARY KEY AUTO_INCREMENT,
    id_khachhang INT NOT NULL,
    id_sach INT NOT NULL,
    ngay_them TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_khachhang) REFERENCES khachhang(id_khachhang) ON DELETE CASCADE,
    FOREIGN KEY (id_sach) REFERENCES sach(id_sach) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist_item (id_khachhang, id_sach),
    INDEX idx_khachhang (id_khachhang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Banner quảng cáo (Banners)
CREATE TABLE banner (
    id_banner INT PRIMARY KEY AUTO_INCREMENT,
    tieu_de VARCHAR(255),
    hinh_anh VARCHAR(255) NOT NULL,
    link_url VARCHAR(255),
    thu_tu INT DEFAULT 0,
    trang_thai ENUM('active', 'inactive') DEFAULT 'active',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SEED DATA - ADMIN
-- =============================================
-- Password: admin123 (hashed with PASSWORD_HASH)
INSERT INTO admin (ten_admin, username, password, email) VALUES
('Quản trị viên', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bookstore.com');

-- =============================================
-- SEED DATA - THỂ LOẠI
-- =============================================
INSERT INTO theloai (ten_theloai, mo_ta, thu_tu) VALUES
('Văn học Việt Nam', 'Các tác phẩm văn học của tác giả Việt Nam', 1),
('Văn học nước ngoài', 'Các tác phẩm văn học được dịch từ nước ngoài', 2),
('Kinh tế - Quản lý', 'Sách về kinh doanh, quản lý, khởi nghiệp', 3),
('Kỹ năng sống', 'Sách phát triển bản thân, kỹ năng mềm', 4),
('Tiểu thuyết', 'Các tác phẩm tiểu thuyết trong và ngoài nước', 5),
('Tâm lý - Tình cảm', 'Sách về tâm lý học, tình cảm, gia đình', 6),
('Lịch sử - Chính trị', 'Sách về lịch sử, chính trị, xã hội', 7),
('Khoa học - Công nghệ', 'Sách về khoa học, công nghệ, lập trình', 8),
('Thiếu nhi', 'Sách dành cho trẻ em và thiếu niên', 9),
('Truyện tranh - Manga', 'Truyện tranh, manga, comic', 10);

-- =============================================
-- SEED DATA - TÁC GIẢ
-- =============================================
INSERT INTO tacgia (ten_tacgia, but_danh, tieu_su, ngay_sinh, quoc_tich, hinh_anh) VALUES
('Paulo Coelho', NULL, 'Nhà văn Brazil nổi tiếng với tác phẩm The Alchemist, đã bán hơn 150 triệu bản trên toàn thế giới', '1947-08-24', 'Brazil', 'paulo_coelho.jpg'),
('Haruki Murakami', NULL, 'Nhà văn Nhật Bản đương đại, tác phẩm của ông được dịch ra hơn 50 ngôn ngữ', '1949-01-12', 'Nhật Bản', 'haruki_murakami.jpg'),
('J.K. Rowling', NULL, 'Tác giả series Harry Potter - bộ sách bán chạy nhất mọi thời đại với hơn 500 triệu bản', '1965-07-31', 'Anh', 'jk_rowling.jpg'),
('George R.R. Martin', NULL, 'Tác giả series A Song of Ice and Fire, được chuyển thể thành series Game of Thrones', '1948-09-20', 'Mỹ', 'george_rr_martin.jpg'),
('Stephen King', NULL, 'Ông hoàng truyện kinh dị với hơn 60 tiểu thuyết và 200 truyện ngắn', '1947-09-21', 'Mỹ', 'stephen_king.jpg'),
('Agatha Christie', NULL, 'Nữ hoàng truyện trinh thám, đã bán hơn 2 tỷ bản sách trên toàn thế giới', '1890-09-15', 'Anh', 'agatha_christie.jpg'),
('Dan Brown', NULL, 'Tác giả của The Da Vinci Code và series Robert Langdon', '1964-06-22', 'Mỹ', 'dan_brown.jpg'),
('John Green', NULL, 'Tác giả trẻ nổi tiếng với The Fault in Our Stars và Looking for Alaska', '1977-08-24', 'Mỹ', 'john_green.jpg'),
('Yuval Noah Harari', NULL, 'Nhà sử học và tác giả của Sapiens - một trong những cuốn sách hay nhất thế kỷ 21', '1976-02-24', 'Israel', 'yuval_harari.jpg'),
('Malcolm Gladwell', NULL, 'Nhà báo và tác giả nổi tiếng với các sách về tâm lý xã hội', '1963-09-03', 'Canada', 'malcolm_gladwell.jpg'),
('Dale Carnegie', NULL, 'Tác giả kinh điển về kỹ năng sống và giao tiếp', '1888-11-24', 'Mỹ', 'dale_carnegie.jpg'),
('Robert Kiyosaki', NULL, 'Tác giả sách về tài chính cá nhân và đầu tư', '1947-04-08', 'Mỹ', 'robert_kiyosaki.jpg'),
('James Clear', NULL, 'Tác giả của Atomic Habits - cuốn sách về xây dựng thói quen tốt', '1986-01-22', 'Mỹ', 'james_clear.jpg'),
('Mark Manson', NULL, 'Tác giả của The Subtle Art of Not Giving a F*ck', '1984-03-09', 'Mỹ', 'mark_manson.jpg'),
('Suzanne Collins', NULL, 'Tác giả của series The Hunger Games', '1962-08-10', 'Mỹ', 'suzanne_collins.jpg'),
('Rick Riordan', NULL, 'Tác giả của series Percy Jackson & The Olympians', '1964-06-05', 'Mỹ', 'rick_riordan.jpg'),
('Jeff Kinney', NULL, 'Tác giả và minh họa của series Diary of a Wimpy Kid', '1971-02-19', 'Mỹ', 'jeff_kinney.jpg'),
('Dav Pilkey', NULL, 'Tác giả của series Dog Man và Captain Underpants', '1966-03-04', 'Mỹ', 'dav_pilkey.jpg'),
('Robert C. Martin', NULL, 'Uncle Bob - chuyên gia về kỹ thuật phần mềm và tác giả Clean Code', '1952-12-05', 'Mỹ', 'robert_martin.jpg'),
('Eric Ries', NULL, 'Tác giả của The Lean Startup, chuyên gia về khởi nghiệp', '1978-09-22', 'Mỹ', 'eric_ries.jpg');

-- =============================================
-- SEED DATA - NHÀ XUẤT BẢN
-- =============================================
INSERT INTO nhaxuatban (ten_nxb, dia_chi, dien_thoai, email, website) VALUES
('Penguin Random House', '1745 Broadway, New York, NY 10019, USA', '+1-212-782-9000', 'info@penguinrandomhouse.com', 'https://www.penguinrandomhouse.com'),
('HarperCollins', '195 Broadway, New York, NY 10007, USA', '+1-212-207-7000', 'info@harpercollins.com', 'https://www.harpercollins.com'),
('Simon & Schuster', '1230 Avenue of the Americas, New York, NY 10020, USA', '+1-212-698-7000', 'info@simonandschuster.com', 'https://www.simonandschuster.com'),
('Hachette Book Group', '1290 Avenue of the Americas, New York, NY 10104, USA', '+1-212-364-1100', 'info@hbgusa.com', 'https://www.hachettebookgroup.com'),
('Macmillan Publishers', '120 Broadway, New York, NY 10271, USA', '+1-646-307-5151', 'info@macmillan.com', 'https://www.macmillan.com'),
('Scholastic', '557 Broadway, New York, NY 10012, USA', '+1-212-343-6100', 'info@scholastic.com', 'https://www.scholastic.com'),
('Bloomsbury Publishing', '50 Bedford Square, London WC1B 3DP, UK', '+44-20-7631-5600', 'info@bloomsbury.com', 'https://www.bloomsbury.com'),
('Oxford University Press', 'Great Clarendon Street, Oxford OX2 6DP, UK', '+44-1865-556767', 'enquiry@oup.com', 'https://global.oup.com'),
('Pearson Education', '330 Hudson Street, New York, NY 10013, USA', '+1-212-641-2400', 'info@pearson.com', 'https://www.pearson.com'),
("O'Reilly Media", '1005 Gravenstein Highway North, Sebastopol, CA 95472, USA', '+1-707-827-7000', 'info@oreilly.com', 'https://www.oreilly.com'),
('Kodansha', '2-12-21 Otowa, Bunkyo-ku, Tokyo 112-8001, Japan', '+81-3-5395-3535', 'info@kodansha.co.jp', 'https://www.kodansha.co.jp'),
('Vintage Books', '1745 Broadway, New York, NY 10019, USA', '+1-212-751-2600', 'info@penguinrandomhouse.com', 'https://www.penguinrandomhouse.com/imprints/vintage-books'),
('Crown Publishing', '1745 Broadway, New York, NY 10019, USA', '+1-212-782-9000', 'crownpublicity@penguinrandomhouse.com', 'https://crownpublishing.com'),
('Portfolio Penguin', '1745 Broadway, New York, NY 10019, USA', '+1-212-366-2000', 'portfolio@penguinrandomhouse.com', 'https://www.penguinrandomhouse.com/publishers/portfolio'),
('Little, Brown and Company', '1290 Avenue of the Americas, New York, NY 10104, USA', '+1-212-364-1100', 'publicity@littlebrown.com', 'https://www.littlebrown.com');

-- =============================================
-- SEED DATA - SÁCH
-- =============================================
INSERT INTO sach (ten_sach, id_tacgia, id_nxb, id_theloai, isbn, gia, gia_goc, hinh_anh, mo_ta, so_trang, nam_xuat_ban, so_luong_ton, noi_bat) VALUES
-- Văn học nước ngoài
('The Alchemist', 1, 4, 2, '9780062315007', 285000, 320000, 'the_alchemist.jpg', 'Câu chuyện về hành trình tìm kiếm kho báu và ý nghĩa cuộc sống của cậu bé chăn cừu Santiago qua sa mạc Ai Cập.', 208, 2014, 250, TRUE),
('Norwegian Wood', 2, 11, 2, '9780375704024', 320000, 360000, 'norwegian_wood.jpg', 'Một câu chuyện tình yêu đầy cảm xúc và nỗi đau của tuổi trẻ trong bối cảnh Nhật Bản những năm 1960s.', 296, 2000, 180, TRUE),
('Kafka on the Shore', 2, 11, 2, '9781400079278', 380000, 420000, 'kafka_shore.jpg', 'Tiểu thuyết siêu thực kết hợp hiện thực và ảo giác, kể về cuộc hành trình của cậu bé Kafka Tamura.', 505, 2005, 120, TRUE),
('1Q84', 2, 11, 2, '9780307476463', 450000, 500000, '1q84.jpg', 'Tác phẩm đồ sộ về hai nhân vật sống trong hai thế giới song song của Tokyo năm 1984.', 925, 2011, 95, FALSE),

-- Tiểu thuyết
('Harry Potter and the Philosopher\'s Stone', 3, 7, 5, '9781408855652', 295000, 330000, 'harry_potter_1.jpg', 'Cuốn sách đầu tiên trong series Harry Potter - câu chuyện về cậu bé phù thủy và cuộc phiêu lưu tại trường Hogwarts.', 352, 2014, 300, TRUE),
('Harry Potter and the Chamber of Secrets', 3, 7, 5, '9781408855669', 295000, 330000, 'harry_potter_2.jpg', 'Harry Potter tiếp tục cuộc phiêu lưu tại Hogwarts khi bí mật về Phòng Chứa Bí Mật được hé mở.', 384, 2014, 280, TRUE),
('Harry Potter and the Prisoner of Azkaban', 3, 7, 5, '9781408855676', 320000, 360000, 'harry_potter_3.jpg', 'Harry đối mặt với tên tù nhân nguy hiểm vừa trốn thoát từ nhà tù Azkaban.', 468, 2014, 260, TRUE),
('A Game of Thrones', 4, 1, 5, '9780553593716', 420000, 470000, 'game_of_thrones.jpg', 'Cuốn đầu tiên của series A Song of Ice and Fire - cuộc chiến giành ngôi Vua của Seven Kingdoms.', 694, 2011, 200, TRUE),
('The Shining', 5, 2, 5, '9780385121675', 350000, 390000, 'the_shining.jpg', 'Câu chuyện kinh dị về gia đình caretaker tại khách sạn Overlook bị ma ám vào mùa đông.', 447, 1977, 150, TRUE),
('IT', 5, 2, 5, '9781501142970', 480000, 540000, 'it.jpg', 'Một nhóm trẻ em đối đầu với thực thể ác quỷ xuất hiện dưới dạng chú hề Pennywise.', 1138, 2016, 135, TRUE),

-- Trinh thám
('Murder on the Orient Express', 6, 2, 5, '9780062693662', 280000, 315000, 'orient_express.jpg', 'Thám tử Hercule Poirot điều tra vụ án giết người trên chuyến tàu Orient Express nổi tiếng.', 256, 2017, 170, TRUE),
('And Then There Were None', 6, 2, 5, '9780062073488', 260000, 295000, 'and_then_there_were_none.jpg', 'Mười người xa lạ bị mời đến một hòn đảo hẻo lánh và lần lượt bị giết theo một bài thơ đồng dao.', 272, 2011, 160, TRUE),
('The Da Vinci Code', 7, 1, 5, '9780307474278', 350000, 390000, 'da_vinci_code.jpg', 'Robert Langdon giải mã các biểu tượng bí ẩn để khám phá một bí mật lịch sử kinh thiên động địa.', 597, 2009, 220, TRUE),
('Angels & Demons', 7, 1, 5, '9781416524793', 340000, 380000, 'angels_demons.jpg', 'Langdon đối đầu với hội Illuminati trong cuộc đua với thời gian tại Vatican.', 736, 2005, 180, FALSE),

-- Tâm lý - Tình cảm
('The Fault in Our Stars', 8, 2, 6, '9780142424179', 265000, 300000, 'fault_in_our_stars.jpg', 'Câu chuyện tình yêu cảm động giữa hai thiếu niên mắc bệnh ung thư.', 318, 2013, 190, TRUE),
('Looking for Alaska', 8, 2, 6, '9780142402511', 255000, 290000, 'looking_alaska.jpg', 'Cuộc sống của Miles thay đổi khi anh gặp Alaska Young - cô gái xinh đẹp và bí ẩn.', 221, 2006, 140, FALSE),

-- Lịch sử - Chính trị
('Sapiens: A Brief History of Humankind', 9, 3, 7, '9780062316097', 420000, 470000, 'sapiens.jpg', 'Lịch sử nhân loại từ Homo Sapiens đến thời đại ngày nay qua góc nhìn độc đáo.', 443, 2015, 250, TRUE),
('Homo Deus: A Brief History of Tomorrow', 9, 3, 7, '9780062464316', 440000, 490000, 'homo_deus.jpg', 'Tương lai của nhân loại khi công nghệ và trí tuệ nhân tạo phát triển.', 450, 2017, 180, TRUE),
('21 Lessons for the 21st Century', 9, 3, 7, '9780525512172', 380000, 425000, '21_lessons.jpg', '21 bài học quan trọng về các thách thức lớn của thế kỷ 21.', 372, 2018, 160, TRUE),

-- Kinh tế - Quản lý
('Rich Dad Poor Dad', 12, 14, 3, '9781612680194', 320000, 360000, 'rich_dad_poor_dad.jpg', 'Những bài học về tài chính từ hai người cha với quan điểm khác biệt.', 336, 2017, 280, TRUE),
('The Lean Startup', 20, 13, 3, '9780307887894', 380000, 425000, 'lean_startup.jpg', 'Phương pháp khởi nghiệp tinh gọn giúp startup tăng tốc và giảm rủi ro.', 336, 2011, 150, TRUE),
('Outliers', 10, 15, 3, '9780316017930', 350000, 390000, 'outliers.jpg', 'Bí mật đằng sau thành công của những người xuất chúng.', 309, 2008, 170, TRUE),
('Blink', 10, 15, 3, '9780316010665', 340000, 380000, 'blink.jpg', 'Sức mạnh của tư duy không suy nghĩ - quyết định trong chớp mắt.', 296, 2007, 145, FALSE),

-- Kỹ năng sống
('How to Win Friends and Influence People', 11, 3, 4, '9780671027032', 295000, 330000, 'how_win_friends.jpg', 'Cuốn sách kinh điển về nghệ thuật giao tiếp và tạo ảnh hưởng.', 288, 1998, 320, TRUE),
('Atomic Habits', 13, 5, 4, '9780735211292', 350000, 390000, 'atomic_habits.jpg', 'Cách xây dựng thói quen tốt, phá bỏ thói quen xấu một cách khoa học.', 320, 2018, 290, TRUE),
('The Subtle Art of Not Giving a F*ck', 14, 3, 4, '9780062457714', 320000, 360000, 'subtle_art.jpg', 'Cách sống phản trực quan để có một cuộc sống tốt đẹp.', 224, 2016, 240, TRUE),
('Think and Grow Rich', 12, 5, 4, '9781585424337', 280000, 315000, 'think_grow_rich.jpg', '13 nguyên tắc để đạt được thành công và sự giàu có.', 238, 2005, 200, FALSE),

-- Khoa học - Công nghệ
('Clean Code', 19, 10, 8, '9780132350884', 650000, 720000, 'clean_code.jpg', 'Cẩm nang viết code sạch và chuyên nghiệp cho mọi lập trình viên.', 464, 2008, 120, TRUE),
('The Pragmatic Programmer', 19, 10, 8, '9780135957059', 680000, 750000, 'pragmatic_programmer.jpg', 'Hướng dẫn từ học viên đến bậc thầy trong nghề lập trình.', 352, 2019, 95, TRUE),
('Design Patterns', 19, 10, 8, '9780201633610', 720000, 800000, 'design_patterns.jpg', 'Các mẫu thiết kế phần mềm tái sử dụng trong lập trình hướng đối tượng.', 416, 1994, 80, FALSE),
('Cracking the Coding Interview', 19, 10, 8, '9780984782857', 580000, 640000, 'cracking_coding.jpg', '189 câu hỏi lập trình và giải pháp để vượt qua phỏng vấn kỹ thuật.', 687, 2015, 110, TRUE),

-- Thiếu nhi
('The Hunger Games', 15, 6, 9, '9780439023481', 280000, 315000, 'hunger_games.jpg', 'Katniss tham gia vào trò chơi sinh tử trong thế giới tương lai dystopian.', 374, 2008, 240, TRUE),
('Catching Fire', 15, 6, 9, '9780439023498', 290000, 325000, 'catching_fire.jpg', 'Katniss trở lại đấu trường trong Quý Quarter Quell của The Hunger Games.', 391, 2009, 220, TRUE),
('The Lightning Thief', 16, 12, 9, '9780786838653', 260000, 295000, 'lightning_thief.jpg', 'Percy Jackson khám phá mình là con của thần Poseidon và phải ngăn chặn cuộc chiến giữa các vị thần.', 377, 2005, 280, TRUE),
('The Sea of Monsters', 16, 12, 9, '9780786856294', 265000, 300000, 'sea_monsters.jpg', 'Percy và bạn bè phiêu lưu vào Biển quái vật để cứu trại Half-Blood.', 279, 2006, 250, TRUE),
('Diary of a Wimpy Kid', 17, 6, 9, '9780810993136', 220000, 250000, 'wimpy_kid_1.jpg', 'Nhật ký hài hước về cuộc sống học đường của cậu bé Greg Heffley.', 217, 2007, 300, TRUE),
('Dog Man', 18, 6, 9, '9780545581608', 210000, 240000, 'dog_man_1.jpg', 'Truyện tranh hài hước về chú chó cảnh sát nửa người nửa chó.', 240, 2016, 270, TRUE),

-- Truyện tranh - Manga
('One Piece Vol.1', NULL, 11, 10, '9781569319017', 180000, 200000, 'one_piece_1.jpg', 'Monkey D. Luffy bắt đầu hành trình tìm kho báu One Piece để trở thành Vua Hải Tặc.', 216, 2003, 320, TRUE),
('Naruto Vol.1', NULL, 11, 10, '9781569319000', 180000, 200000, 'naruto_1.jpg', 'Naruto Uzumaki theo đuổi giấc mơ trở thành Hokage của làng Lá.', 192, 2003, 300, TRUE),
('Attack on Titan Vol.1', NULL, 11, 10, '9781612620244', 195000, 220000, 'aot_1.jpg', 'Nhân loại chiến đấu sinh tồn chống lại Titan trong thế giới hậu tận thế.', 194, 2012, 260, TRUE),
('Death Note Vol.1', NULL, 11, 10, '9781421501680', 185000, 210000, 'death_note_1.jpg', 'Light Yagami tìm thấy quyển sổ tử thần có thể giết người chỉ bằng cách viết tên.', 200, 2005, 280, TRUE),
('My Hero Academia Vol.1', NULL, 11, 10, '9781421582696', 180000, 200000, 'mha_1.jpg', 'Izuku Midoriya sinh ra không có siêu năng lực nhưng vẫn mơ ước trở thành anh hùng.', 192, 2015, 290, TRUE);

-- =============================================
-- SEED DATA - KHÁCH HÀNG
-- =============================================
-- Password: 123456 (hashed)
INSERT INTO khachhang (ten_khachhang, email, password, dien_thoai, dia_chi, ngay_sinh, gioi_tinh) VALUES
('Nguyễn Văn A', 'nguyenvana@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901234567', '123 Nguyễn Huệ, Q1, TP.HCM', '1990-05-15', 'Nam'),
('Trần Thị B', 'tranthib@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0912345678', '456 Lê Lợi, Q3, TP.HCM', '1995-08-20', 'Nữ'),
('Lê Văn C', 'levanc@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0923456789', '789 Trần Hưng Đạo, Q5, TP.HCM', '1988-12-10', 'Nam'),
('Phạm Thị D', 'phamthid@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0934567890', '321 Võ Văn Tần, Q3, TP.HCM', '1992-03-25', 'Nữ'),
('Hoàng Văn E', 'hoangvane@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0945678901', '654 Pasteur, Q1, TP.HCM', '1985-07-18', 'Nam');

-- =============================================
-- SEED DATA - HÓA ĐƠN
-- =============================================
INSERT INTO hoadon (id_khachhang, ma_hoadon, ngay_dat_hang, tong_tien, trang_thai, phuong_thuc_thanh_toan, trang_thai_thanh_toan, ten_nguoi_nhan, dia_chi_giao, sdt_giao, email_giao) VALUES
(1, 'HD001', '2025-01-08 10:20:00', 580000, 'completed', 'COD', 'paid', 'Nguyễn Văn A', '123 Nguyễn Huệ, Q1, TP.HCM', '0901234567', 'nguyenvana@gmail.com'),
(2, 'HD002', '2025-02-14 15:45:00', 1375000, 'shipping', 'transfer', 'paid', 'Trần Thị B', '456 Lê Lợi, Q3, TP.HCM', '0912345678', 'tranthib@gmail.com'),
(3, 'HD003', '2025-03-02 09:05:00', 635000, 'pending', 'COD', 'unpaid', 'Lê Văn C', '789 Trần Hưng Đạo, Q5, TP.HCM', '0923456789', 'levanc@gmail.com'),
(4, 'HD004', '2025-04-21 11:30:00', 1785000, 'confirmed', 'momo', 'paid', 'Phạm Thị D', '321 Võ Văn Tần, Q3, TP.HCM', '0934567890', 'phamthid@gmail.com'),
(5, 'HD005', '2025-05-10 18:10:00', 1165000, 'completed', 'vnpay', 'paid', 'Hoàng Văn E', '654 Pasteur, Q1, TP.HCM', '0945678901', 'hoangvane@gmail.com');

-- =============================================
-- SEED DATA - CHI TIẾT HÓA ĐƠN
-- =============================================
INSERT INTO chitiet_hoadon (id_hoadon, id_sach, so_luong, gia, thanh_tien) VALUES
-- HD001
(1, 1, 1, 285000, 285000),
(1, 5, 1, 295000, 295000),
-- HD002
(2, 21, 1, 320000, 320000),
(2, 26, 1, 350000, 350000),
(2, 17, 1, 265000, 265000),
(2, 35, 1, 260000, 260000),
(2, 40, 1, 180000, 180000),
-- HD003
(3, 1, 1, 285000, 285000),
(3, 14, 1, 350000, 350000),
-- HD004
(4, 19, 1, 420000, 420000),
(4, 8, 1, 420000, 420000),
(4, 29, 1, 650000, 650000),
(4, 25, 1, 295000, 295000),
-- HD005
(5, 37, 2, 220000, 440000),
(5, 40, 3, 180000, 540000),
(5, 41, 1, 185000, 185000);

-- =============================================
-- SEED DATA - ĐÁNH GIÁ
-- =============================================
INSERT INTO danhgia (id_sach, id_khachhang, so_sao, tieu_de, noi_dung, trang_thai) VALUES
(1, 1, 5, 'Masterpiece!', 'The Alchemist là một kiệt tác văn học. Mỗi trang sách đều chứa đựng triết lý sâu sắc về cuộc sống.', 'approved'),
(1, 2, 5, 'Life-changing', 'Cuốn sách thay đổi cách nhìn của tôi về cuộc sống. Must read!', 'approved'),
(5, 3, 5, 'Magical World', 'Harry Potter là series tuyệt vời nhất mà tôi từng đọc. Thế giới phép thuật quá hấp dẫn!', 'approved'),
(2, 4, 4, 'Beautiful but melancholic', 'Norwegian Wood viết rất đẹp nhưng có phần buồn. Murakami thật tài năng.', 'approved'),
(21, 5, 5, 'Best financial book', 'Rich Dad Poor Dad thay đổi tư duy tài chính của tôi hoàn toàn. Highly recommended!', 'approved'),
(26, 1, 5, 'Game changer', 'Atomic Habits dạy tôi cách xây dựng thói quen hiệu quả. Rất thực tế!', 'approved'),
(14, 2, 5, 'Gripping mystery', 'The Da Vinci Code hấp dẫn từ trang đầu đến trang cuối. Không thể rời mắt!', 'approved'),
(17, 3, 5, 'Emotional rollercoaster', 'The Fault in Our Stars làm tôi khóc rất nhiều. Câu chuyện tình yêu đẹp và đau.', 'approved'),
(19, 4, 5, 'Mind-blowing', 'Sapiens mở mang tầm nhìn về lịch sử loài người. Xuất sắc!', 'approved'),
(29, 5, 4, 'Essential for developers', 'Clean Code là must-have cho mọi programmer. Viết code clean hơn nhiều sau khi đọc.', 'approved'),
(8, 1, 5, 'Epic Fantasy', 'Game of Thrones mở đầu series tuyệt vời. Thế giới Westeros thật sống động!', 'approved'),
(35, 2, 5, 'Addictive series', 'Percy Jackson rất vui và thú vị. Con tôi đọc không thể rời mắt!', 'approved');

-- =============================================
-- SEED DATA - GIỎ HÀNG
-- =============================================
INSERT INTO giohang (id_khachhang, id_sach, so_luong) VALUES
(1, 10, 1),
(1, 26, 2),
(2, 17, 1),
(3, 1, 1),
(3, 21, 1),
(4, 5, 2),
(5, 29, 1),
(5, 40, 3);

-- =============================================
-- SEED DATA - MÃ GIẢM GIÁ
-- =============================================
INSERT INTO magiamgia (ma_code, ten_chuongtrinh, loai_giam, gia_tri_giam, gia_tri_toi_thieu, giam_toi_da, so_luong, ngay_bat_dau, ngay_ket_thuc) VALUES
('NEWYEAR2025', 'Khuyến mãi năm mới 2025', 'percent', 15, 200000, 50000, 100, '2025-01-01 00:00:00', '2025-01-31 23:59:59'),
('BOOKFEST', 'Lễ hội sách', 'percent', 20, 300000, 100000, 50, '2025-04-01 00:00:00', '2025-04-30 23:59:59'),
('FREESHIP', 'Miễn phí vận chuyển', 'fixed', 30000, 150000, 30000, 200, '2025-01-01 00:00:00', '2025-12-31 23:59:59'),
('STUDENT10', 'Giảm giá sinh viên', 'percent', 10, 100000, 30000, 500, '2025-01-01 00:00:00', '2025-12-31 23:59:59');

-- =============================================
-- SEED DATA - WISHLIST
-- =============================================
INSERT INTO wishlist (id_khachhang, id_sach) VALUES
(1, 8),
(1, 19),
(2, 21),
(3, 1),
(3, 14),
(4, 9),
(5, 29),
(5, 35),
(1, 40),
(2, 42);

-- =============================================
-- SEED DATA - BANNER
-- =============================================
INSERT INTO banner (tieu_de, hinh_anh, link_url, thu_tu, trang_thai) VALUES
('Khuyến mãi năm mới 2025', 'banner_new_year.jpg', '/promotion/newyear2025', 1, 'active'),
('Sách best seller', 'banner_bestseller.jpg', '/books/bestseller', 2, 'active'),
('Giảm giá sách thiếu nhi', 'banner_thieunhi.jpg', '/category/thieunhi', 3, 'active'),
('Mua 2 tặng 1', 'banner_mua2tang1.jpg', '/promotion/buy2get1', 4, 'active');

-- =============================================
-- EXTRA SEED DATA - MORE CUSTOMERS + MANY ORDERS (FOR DASHBOARD ANALYTICS)
-- Note: This section generates many orders across months when you re-import.
-- =============================================

-- Add more customers (Password: 123456, same hash as existing seeds)
INSERT INTO khachhang (ten_khachhang, email, password, dien_thoai, dia_chi, ngay_sinh, gioi_tinh) VALUES
('Đặng Minh Anh', 'minhanh@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0902223344', '12 CMT8, Q10, TP.HCM', '1998-11-02', 'Nữ'),
('Võ Quốc Huy', 'quochuy@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0903334455', '88 Lý Tự Trọng, Q1, TP.HCM', '1993-04-19', 'Nam'),
('Ngô Thảo Vy', 'thaovy@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0904445566', '25 Nguyễn Trãi, Q5, TP.HCM', '1996-09-12', 'Nữ'),
('Bùi Gia Bảo', 'giabao@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0905556677', '102 Hai Bà Trưng, Q1, TP.HCM', '1991-01-30', 'Nam'),
('Phan Hoài Nam', 'hoainam@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0906667788', '15 Điện Biên Phủ, Bình Thạnh, TP.HCM', '1989-06-08', 'Nam'),
('Lý Ngọc Trâm', 'ngoctram@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0907778899', '9 Pasteur, Q3, TP.HCM', '1997-02-22', 'Nữ'),
('Trịnh Khánh Linh', 'khanhlinh@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0908889900', '70 Võ Thị Sáu, Q3, TP.HCM', '1999-07-05', 'Nữ'),
('Nguyễn Đức Long', 'duclong@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0911112233', '5 Nguyễn Văn Cừ, Q5, TP.HCM', '1994-12-14', 'Nam'),
('Hà Thu Hà', 'thuha@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0912223344', '120 Phan Xích Long, Phú Nhuận, TP.HCM', '1992-10-01', 'Nữ'),
('Lâm Tuấn Kiệt', 'tuankiet@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0913334455', '33 Nguyễn Oanh, Gò Vấp, TP.HCM', '1990-03-03', 'Nam'),
('Đỗ Thanh Tùng', 'thanhtung@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0914445566', '77 Tôn Đức Thắng, Q1, TP.HCM', '1987-08-09', 'Nam'),
('Mai Phương Thảo', 'phuongthao@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0915556677', '18 Nguyễn Hữu Cảnh, Bình Thạnh, TP.HCM', '1995-05-27', 'Nữ'),
('Phạm Quốc Tuấn', 'quoctuan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0916667788', '9 Lê Văn Sỹ, Q3, TP.HCM', '1988-02-17', 'Nam'),
('Trần Bảo Ngọc', 'baongoc@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0917778899', '66 Nguyễn Đình Chiểu, Q3, TP.HCM', '2000-12-31', 'Nữ'),
('Vũ Hoàng Phúc', 'hoangphuc@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0918889900', '140 Lê Lai, Q1, TP.HCM', '1993-09-09', 'Nam');

-- Bulk generate orders + order details for analytics
DELIMITER $$
CREATE PROCEDURE sp_seed_bulk_orders(
    IN p_start_date DATE,
    IN p_months INT,
    IN p_orders_per_month INT
)
BEGIN
    DECLARE m INT DEFAULT 0;
    DECLARE o INT;
    DECLARE v_order_id INT;
    DECLARE v_customer_id INT;
    DECLARE v_book_id INT;
    DECLARE v_items INT;
    DECLARE v_qty INT;
    DECLARE v_total DECIMAL(10,2);
    DECLARE v_ma VARCHAR(20);
    DECLARE v_date DATETIME;
    DECLARE v_status VARCHAR(20);
    DECLARE v_paid VARCHAR(10);
    DECLARE v_pay_method VARCHAR(10);

    WHILE m < p_months DO
        SET o = 0;
        WHILE o < p_orders_per_month DO
            -- Pick a random existing customer
            SELECT id_khachhang INTO v_customer_id
            FROM khachhang
            ORDER BY RAND()
            LIMIT 1;

            -- Random date within the month (avoid month-length edge cases)
            SET v_date = DATE_ADD(DATE_ADD(p_start_date, INTERVAL m MONTH), INTERVAL FLOOR(RAND() * 28) DAY);
            SET v_date = DATE_ADD(v_date, INTERVAL FLOOR(RAND() * 24) HOUR);
            SET v_date = DATE_ADD(v_date, INTERVAL FLOOR(RAND() * 60) MINUTE);

            -- Status distribution (most are completed/shipping to feed analytics)
            IF RAND() < 0.60 THEN
                SET v_status = 'completed';
            ELSEIF RAND() < 0.85 THEN
                SET v_status = 'shipping';
            ELSEIF RAND() < 0.93 THEN
                SET v_status = 'confirmed';
            ELSEIF RAND() < 0.97 THEN
                SET v_status = 'pending';
            ELSE
                SET v_status = 'cancelled';
            END IF;

            -- Payment method
            SET v_pay_method = ELT(1 + FLOOR(RAND() * 5), 'COD', 'transfer', 'card', 'momo', 'vnpay');

            -- Payment status: cancelled always unpaid; completed/shipping usually paid
            IF v_status = 'cancelled' THEN
                SET v_paid = 'unpaid';
            ELSEIF v_status IN ('completed', 'shipping') AND RAND() < 0.85 THEN
                SET v_paid = 'paid';
            ELSEIF v_pay_method = 'COD' AND RAND() < 0.40 THEN
                SET v_paid = 'unpaid';
            ELSE
                SET v_paid = 'paid';
            END IF;

            -- Unique-ish order code
            SET v_ma = CONCAT('HD', UPPER(SUBSTRING(REPLACE(UUID(), '-', ''), 1, 10)));

            -- Insert order with tong_tien=0 then fill from details
            INSERT INTO hoadon (
                id_khachhang, ma_hoadon, ngay_dat_hang, tong_tien,
                trang_thai, phuong_thuc_thanh_toan, trang_thai_thanh_toan,
                ten_nguoi_nhan, dia_chi_giao, sdt_giao, email_giao, ghi_chu
            )
            SELECT
                k.id_khachhang,
                v_ma,
                v_date,
                0,
                v_status,
                v_pay_method,
                v_paid,
                k.ten_khachhang,
                COALESCE(k.dia_chi, 'N/A'),
                COALESCE(k.dien_thoai, '0000000000'),
                k.email,
                NULL
            FROM khachhang k
            WHERE k.id_khachhang = v_customer_id;

            SET v_order_id = LAST_INSERT_ID();
            SET v_items = 1 + FLOOR(RAND() * 4); -- 1..4 line items
            SET v_total = 0;

            WHILE v_items > 0 DO
                -- Pick a random book
                SELECT id_sach INTO v_book_id
                FROM sach
                ORDER BY RAND()
                LIMIT 1;

                SET v_qty = 1 + FLOOR(RAND() * 3); -- 1..3 qty

                INSERT INTO chitiet_hoadon (id_hoadon, id_sach, so_luong, gia, thanh_tien)
                SELECT v_order_id, s.id_sach, v_qty, s.gia, (s.gia * v_qty)
                FROM sach s
                WHERE s.id_sach = v_book_id;

                SET v_total = v_total + (
                    SELECT s.gia * v_qty FROM sach s WHERE s.id_sach = v_book_id
                );

                SET v_items = v_items - 1;
            END WHILE;

            UPDATE hoadon
            SET tong_tien = v_total
            WHERE id_hoadon = v_order_id;

            -- Update inventory/sales only for shipped/completed orders
            IF v_status IN ('completed', 'shipping') THEN
                UPDATE sach s
                JOIN (
                    SELECT id_sach, SUM(so_luong) AS qty
                    FROM chitiet_hoadon
                    WHERE id_hoadon = v_order_id
                    GROUP BY id_sach
                ) x ON x.id_sach = s.id_sach
                SET
                    s.luot_ban = s.luot_ban + x.qty,
                    s.so_luong_ton = GREATEST(s.so_luong_ton - x.qty, 0);
            END IF;

            SET o = o + 1;
        END WHILE;

        SET m = m + 1;
    END WHILE;
END$$
DELIMITER ;

-- Generate 18 months of data x 20 orders/month = 360 extra orders
CALL sp_seed_bulk_orders('2024-01-01', 18, 20);

DROP PROCEDURE sp_seed_bulk_orders;

-- =============================================
-- CREATE VIEWS FOR REPORTING
-- =============================================

-- View: Sách bán chạy
CREATE VIEW v_sach_ban_chay AS
SELECT 
    s.id_sach,
    s.ten_sach,
    t.ten_tacgia,
    n.ten_nxb,
    s.gia,
    s.luot_ban,
    COUNT(DISTINCT d.id_danhgia) as so_luong_danhgia,
    COALESCE(AVG(d.so_sao), 0) as diem_trung_binh
FROM sach s
LEFT JOIN tacgia t ON s.id_tacgia = t.id_tacgia
LEFT JOIN nhaxuatban n ON s.id_nxb = n.id_nxb
LEFT JOIN danhgia d ON s.id_sach = d.id_sach AND d.trang_thai = 'approved'
GROUP BY s.id_sach
ORDER BY s.luot_ban DESC;

-- View: Thống kê doanh thu theo tháng
CREATE VIEW v_doanh_thu_thang AS
SELECT 
    YEAR(ngay_dat_hang) as nam,
    MONTH(ngay_dat_hang) as thang,
    COUNT(*) as so_don_hang,
    SUM(tong_tien) as doanh_thu,
    COUNT(DISTINCT id_khachhang) as so_khach_hang
FROM hoadon
WHERE trang_thai IN ('completed', 'shipping')
GROUP BY YEAR(ngay_dat_hang), MONTH(ngay_dat_hang)
ORDER BY nam DESC, thang DESC;

-- View: Top khách hàng
CREATE VIEW v_top_khachhang AS
SELECT 
    k.id_khachhang,
    k.ten_khachhang,
    k.email,
    k.dien_thoai,
    COUNT(h.id_hoadon) as so_don_hang,
    SUM(h.tong_tien) as tong_chi_tieu
FROM khachhang k
LEFT JOIN hoadon h ON k.id_khachhang = h.id_khachhang
WHERE h.trang_thai IN ('completed')
GROUP BY k.id_khachhang
ORDER BY tong_chi_tieu DESC;

-- =============================================
-- CREATE STORED PROCEDURES
-- =============================================

-- Procedure: Thêm sách vào giỏ hàng
DELIMITER $$
CREATE PROCEDURE sp_them_vao_gio(
    IN p_id_khachhang INT,
    IN p_id_sach INT,
    IN p_so_luong INT
)
BEGIN
    DECLARE v_count INT;
    
    -- Kiểm tra sách đã có trong giỏ chưa
    SELECT COUNT(*) INTO v_count 
    FROM giohang 
    WHERE id_khachhang = p_id_khachhang AND id_sach = p_id_sach;
    
    IF v_count > 0 THEN
        -- Cập nhật số lượng
        UPDATE giohang 
        SET so_luong = so_luong + p_so_luong 
        WHERE id_khachhang = p_id_khachhang AND id_sach = p_id_sach;
    ELSE
        -- Thêm mới
        INSERT INTO giohang (id_khachhang, id_sach, so_luong) 
        VALUES (p_id_khachhang, p_id_sach, p_so_luong);
    END IF;
END$$
DELIMITER ;

-- Procedure: Tạo đơn hàng từ giỏ hàng
DELIMITER $$
CREATE PROCEDURE sp_tao_don_hang(
    IN p_id_khachhang INT,
    IN p_ten_nguoi_nhan VARCHAR(100),
    IN p_dia_chi_giao TEXT,
    IN p_sdt_giao VARCHAR(15),
    IN p_email_giao VARCHAR(100),
    IN p_phuong_thuc_thanh_toan VARCHAR(20),
    IN p_ghi_chu TEXT,
    OUT p_id_hoadon INT,
    OUT p_ma_hoadon VARCHAR(20)
)
BEGIN
    DECLARE v_tong_tien DECIMAL(10,2);
    DECLARE v_ma_hoadon VARCHAR(20);
    
    -- Tính tổng tiền từ giỏ hàng
    SELECT SUM(s.gia * g.so_luong) INTO v_tong_tien
    FROM giohang g
    JOIN sach s ON g.id_sach = s.id_sach
    WHERE g.id_khachhang = p_id_khachhang;
    
    -- Tạo mã hóa đơn
    SET v_ma_hoadon = CONCAT('HD', LPAD(FLOOR(RAND() * 999999), 6, '0'));
    
    -- Tạo hóa đơn
    INSERT INTO hoadon (
        id_khachhang, ma_hoadon, tong_tien, phuong_thuc_thanh_toan,
        ten_nguoi_nhan, dia_chi_giao, sdt_giao, email_giao, ghi_chu
    ) VALUES (
        p_id_khachhang, v_ma_hoadon, v_tong_tien, p_phuong_thuc_thanh_toan,
        p_ten_nguoi_nhan, p_dia_chi_giao, p_sdt_giao, p_email_giao, p_ghi_chu
    );
    
    SET p_id_hoadon = LAST_INSERT_ID();
    SET p_ma_hoadon = v_ma_hoadon;
    
    -- Chuyển giỏ hàng sang chi tiết hóa đơn
    INSERT INTO chitiet_hoadon (id_hoadon, id_sach, so_luong, gia, thanh_tien)
    SELECT 
        p_id_hoadon,
        g.id_sach,
        g.so_luong,
        s.gia,
        s.gia * g.so_luong
    FROM giohang g
    JOIN sach s ON g.id_sach = s.id_sach
    WHERE g.id_khachhang = p_id_khachhang;
    
    -- Cập nhật số lượng tồn và lượt bán
    UPDATE sach s
    JOIN giohang g ON s.id_sach = g.id_sach
    SET 
        s.so_luong_ton = s.so_luong_ton - g.so_luong,
        s.luot_ban = s.luot_ban + g.so_luong
    WHERE g.id_khachhang = p_id_khachhang;
    
    -- Xóa giỏ hàng
    DELETE FROM giohang WHERE id_khachhang = p_id_khachhang;
END$$
DELIMITER ;

-- =============================================
-- CREATE TRIGGERS
-- =============================================

-- Trigger: Cập nhật lượt xem khi xem chi tiết sách
DELIMITER $$
CREATE TRIGGER tr_cap_nhat_luot_xem
AFTER INSERT ON danhgia
FOR EACH ROW
BEGIN
    UPDATE sach 
    SET luot_xem = luot_xem + 1 
    WHERE id_sach = NEW.id_sach;
END$$
DELIMITER ;

-- Trigger: Kiểm tra số lượng tồn trước khi thêm vào giỏ
DELIMITER $$
CREATE TRIGGER tr_kiem_tra_ton_kho
BEFORE INSERT ON giohang
FOR EACH ROW
BEGIN
    DECLARE v_ton_kho INT;
    
    SELECT so_luong_ton INTO v_ton_kho
    FROM sach
    WHERE id_sach = NEW.id_sach;
    
    IF v_ton_kho < NEW.so_luong THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Số lượng sách trong kho không đủ';
    END IF;
END$$
DELIMITER ;

-- =============================================
-- COMPLETION MESSAGE
-- =============================================
SELECT 'Database bookstore created successfully!' AS Status;
SELECT COUNT(*) AS 'Total Books' FROM sach;
SELECT COUNT(*) AS 'Total Authors' FROM tacgia;
SELECT COUNT(*) AS 'Total Publishers' FROM nhaxuatban;
SELECT COUNT(*) AS 'Total Categories' FROM theloai;
SELECT COUNT(*) AS 'Total Customers' FROM khachhang;
SELECT COUNT(*) AS 'Total Orders' FROM hoadon;
