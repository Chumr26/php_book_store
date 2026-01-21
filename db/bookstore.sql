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
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    email_verify_token_hash VARCHAR(64) NULL,
    email_verify_expires_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_dien_thoai (dien_thoai),
    INDEX idx_email_verify_token (email_verify_token_hash)
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
    dien_thoai VARCHAR(20),
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
    tu_khoa VARCHAR(255) DEFAULT NULL,
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
    id_magiamgia INT DEFAULT NULL,
    so_tien_giam DECIMAL(10,2) DEFAULT 0,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_khachhang) REFERENCES khachhang(id_khachhang) ON DELETE SET NULL,
    INDEX idx_ma_hoadon (ma_hoadon),
    INDEX idx_khachhang (id_khachhang),
    INDEX idx_magiamgia (id_magiamgia),
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
    ngay_cap_nhat TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sach) REFERENCES sach(id_sach) ON DELETE CASCADE,
    FOREIGN KEY (id_khachhang) REFERENCES khachhang(id_khachhang) ON DELETE CASCADE,
    UNIQUE KEY uq_danhgia_customer_book (id_sach, id_khachhang),
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
    loai_giam ENUM('percent', 'fixed', 'free_shipping') DEFAULT 'percent',
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
('O''Reilly Media', '1005 Gravenstein Highway North, Sebastopol, CA 95472, USA', '+1-707-827-7000', 'info@oreilly.com', 'https://www.oreilly.com'),
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
('The Alchemist', 1, 4, 2, '9780062315007', 285000, 320000, 'the_alchemist.jpg', 'Tiểu thuyết giả tưởng kể về cuộc hành trình mạo hiểm theo đuổi giấc mơ của Santiago, một cậu bé chăn cừu người Tây Ban Nha. Tin vào một giấc mơ tiên tri, cậu rời bỏ quê hương để đi tìm kho báu được chôn giấu tại các kim tự tháp ở Ai Cập. Trên đường đi, cậu gặp một người phụ nữ Digan, một người đàn ông tự xưng là vua, và một nhà giả kim. Tất cả đều chỉ dẫn cậu đi theo hướng tìm kiếm kho báu của mình. Câu chuyện không chỉ là về việc tìm kiếm vật chất, mà còn là bài học sâu sắc về việc lắng nghe trái tim và dũng cảm theo đuổi vận mệnh của chính mình.', 208, 2014, 250, TRUE),
('Norwegian Wood', 2, 11, 2, '9780375704024', 320000, 360000, 'norwegian_wood.jpg', 'Một câu chuyện đầy hoài niệm và u sầu về tuổi trẻ, tình yêu và sự mất mát trong bối cảnh nước Nhật những năm 1960. Toru Watanabe hồi tưởng về những ngày tháng sinh viên của mình, bị giằng xé giữa hai người phụ nữ: Naoko, mối tình đầu xinh đẹp nhưng mong manh về cảm xúc, và Midori, cô gái tràn đầy sức sống và nhiệt huyết. Khi Naoko ngày càng chìm sâu vào thế giới nội tâm u tối của mình, Toru phải đối mặt với nỗi cô đơn và sự trưởng thành đau đớn. Tác phẩm khắc họa sâu sắc tâm lý của một thế hệ thanh niên Nhật Bản đang loay hoay tìm kiếm ý nghĩa cuộc sống giữa những biến động xã hội và cá nhân.', 296, 2000, 180, TRUE),
('Kafka on the Shore', 2, 11, 2, '9781400079278', 380000, 420000, 'kafka_shore.jpg', 'Một tiểu thuyết siêu thực tráng lệ kết hợp giữa hiện thực và ảo giác, đan xen hai câu chuyện song song kỳ lạ. Kafka Tamura, một cậu bé 15 tuổi bỏ nhà ra đi để trốn chạy khỏi lời nguyền đen tối của cha mình và tìm kiếm mẹ cùng chị gái đã thất lạc. Song song đó là hành trình của Nakata, một ông già mất trí nhớ nhưng có khả năng trò chuyện với loài mèo. Số phận của họ dần dần giao nhau tại một thư viện nhỏ ven biển, nơi những bí ẩn từ quá khứ và hiện tại được hé lộ. Murakami đưa người đọc vào một mê cung của những giấc mơ, văn hóa đại chúng và triết học, nơi ranh giới giữa thực và ảo bị xóa nhòa.', 505, 2005, 120, TRUE),
('1Q84', 2, 11, 2, '9780307476463', 450000, 500000, '1q84.jpg', 'Tác phẩm đồ sộ và tham vọng nhất của Murakami, kể về Aomame, một nữ sát thủ chuyên trừng phạt những gã đàn ông bạo hành phụ nữ, và Tengo, một giáo viên toán kiêm tiểu thuyết gia đầy triển vọng. Cả hai vô tình bước vào một thực tại song song gọi là 1Q84, nơi có hai mặt trăng trên bầu trời và những quy luật vật lý bị bẻ cong. Khi họ dần nhận ra sự tồn tại của nhau và mối liên kết sâu sắc từ quá khứ, họ bị cuốn vào một âm mưu tôn giáo bí ẩn đe dọa đến sự tồn vong của thế giới. Cuốn sách là một bản tình ca về nỗi cô đơn, định mệnh và sức mạnh của tình yêu vượt qua mọi rào cản không gian và thời gian.', 925, 2011, 95, FALSE),

-- Tiểu thuyết
('Harry Potter and the Philosopher''s Stone', 3, 7, 5, '9781408855652', 295000, 330000, 'harry_potter_1.jpg', 'Harry Potter là một cậu bé mồ côi sống cùng dì dượng Dursley và luôn bị đối xử tệ bạc. Vào ngày sinh nhật thứ 11, cuộc đời cậu thay đổi hoàn toàn khi nhận được thư mời nhập học từ Trường Phù thủy và Pháp sư Hogwarts. Tại đây, cậu khám phá ra thân thế thực sự của mình, kết bạn với Ron Weasley và Hermione Granger, và bắt đầu cuộc hành trình khám phá thế giới phép thuật đầy màu sắc. Cậu cũng phải đối mặt với Chúa tể Voldemort, kẻ đã sát hại cha mẹ cậu và để lại vết sẹo hình tia chớp trên trán cậu. Cuốn sách mở ra một thế giới kỳ diệu nơi tình bạn và lòng dũng cảm chiến thắng mọi thế lực hắc ám.', 352, 2014, 300, TRUE),
('Harry Potter and the Chamber of Secrets', 3, 7, 5, '9781408855669', 295000, 330000, 'harry_potter_2.jpg', 'Harry Potter trở lại Hogwarts cho năm học thứ hai sau một kỳ nghỉ hè tồi tệ. Tuy nhiên, ngôi trường đang bị đe dọa bởi một thế lực đen tối cổ xưa. Những dòng thông điệp bằng máu xuất hiện trên tường, cảnh báo rằng "Phòng Chứa Bí Mật đã được mở". Học sinh gốc Muggle bị tấn công và hóa đá một cách bí ẩn. Harry, Ron và Hermione phải chạy đua với thời gian để giải mã bí ẩn 50 năm về trước và cứu ngôi trường khỏi nguy cơ đóng cửa vĩnh viễn, đồng thời đối mặt với nỗi sợ hãi sâu thẳm nhất của chính mình.', 384, 2014, 280, TRUE),
('Harry Potter and the Prisoner of Azkaban', 3, 7, 5, '9781408855676', 320000, 360000, 'harry_potter_3.jpg', 'Sirius Black, tên tội phạm nguy hiểm và là tay sai đắc lực của Chúa tể Voldemort, đã trốn thoát khỏi nhà tù Azkaban kiên cố. Mọi người đồn rằng hắn đang truy lùng Harry Potter để trả thù cho chủ nhân. Năm học thứ ba của Harry tại Hogwarts bao trùm bởi nỗi sợ hãi khi các Giám ngục Azkaban được điều đến để canh gác ngôi trường. Harry không chỉ phải đối mặt với mối đe dọa từ bên ngoài mà còn khám phá ra những bí mật đau lòng về cái chết của cha mẹ mình và sự thật về người cha đỡ đầu bị oan ức. Câu chuyện về lòng trung thành và sự phản bội.', 468, 2014, 260, TRUE),
('A Game of Thrones', 4, 1, 5, '9780553593716', 420000, 470000, 'game_of_thrones.jpg', 'Trong một thế giới nơi mùa hè kéo dài hàng thập kỷ và mùa đông có thể tồn tại suốt cả một đời người, cuộc chiến tranh giành Ngôi Báu Sắt đang bắt đầu. Từ phương Nam ấm áp đầy mưu mô đến phương Bắc lạnh giá nơi Bức Tường thành cổ đại ngăn chặn những thế lực đen tối, các gia tộc lớn nhỏ bị cuốn vào vòng xoáy của quyền lực, tình yêu và sự phản bội. Eddard Stark, lãnh chúa của Winterfell, bị cuốn vào những âm mưu chính trị tại King''s Landing khi ông chấp nhận trở thành Cánh Tay Phải của Vua. Một sử thi hào hùng và đẫm máu về danh dự và tham vọng.', 694, 2011, 200, TRUE),
('The Shining', 5, 2, 5, '9780385121675', 350000, 390000, 'the_shining.jpg', 'Jack Torrance, một nhà văn đang gặp khó khăn và nghiện rượu, chấp nhận công việc trông coi khách sạn Overlook vào mùa đông để tìm cảm hứng sáng tác và hàn gắn tình cảm gia đình. Tuy nhiên, khi cơn bão tuyết cô lập khách sạn khỏi thế giới bên ngoài, những thế lực ma quái ẩn mình trong các hành lang trống trải bắt đầu thức tỉnh. Chúng thao túng tâm trí Jack, biến anh thành mối đe dọa kinh hoàng đối với vợ và cậu con trai nhỏ Danny - người sở hữu khả năng thấu thị gọi là "The Shining". Một kiệt tác kinh dị tâm lý đầy ám ảnh.', 447, 1977, 150, TRUE),
('IT', 5, 2, 5, '9781501142970', 480000, 540000, 'it.jpg', 'Tại thị trấn Derry nhỏ bé, một thế lực tà ác cổ xưa tỉnh giấc mỗi 27 năm để săn lùng trẻ em, thường xuất hiện dưới hình dạng chú hề Pennywise. Một nhóm bảy đứa trẻ bị ruồng bỏ, tự gọi mình là "Hội Thất Bại", đã cùng nhau đối mặt với nỗi sợ hãi lớn nhất của đời mình để đánh bại con quái vật đó. Nhưng khi chúng trưởng thành và quên đi quá khứ, "Nó" lại trở lại. Họ phải quay về Derry một lần nữa để thực hiện lời thề máu năm xưa và tiêu diệt cái ác vĩnh viễn. Câu chuyện về tình bạn, ký ức và chấn thương tâm lý.', 1138, 2016, 135, TRUE),

-- Trinh thám
('Murder on the Orient Express', 6, 2, 5, '9780062693662', 280000, 315000, 'orient_express.jpg', 'Khi chuyến tàu Tốc hành Phương Đông sang trọng bị mắc kẹt trong bão tuyết giữa vùng núi Balkan, một vụ án mạng kinh hoàng đã xảy ra. Một hành khách giàu có bị sát hại dã man trong cabin bị khóa kín từ bên trong. Thám tử lừng danh Hercule Poirot tình cờ có mặt trên tàu và phải chạy đua với thời gian để tìm ra hung thủ trước khi hắn ra tay một lần nữa. Với manh mối mâu thuẫn và danh sách nghi phạm bao gồm tất cả hành khách trên toa tàu, Poirot phải vận dụng hết trí thông minh sắc bén của mình để lật tẩy một âm mưu trả thù hoàn hảo đến rợn người.', 256, 2017, 170, TRUE),
('And Then There Were None', 6, 2, 5, '9780062073488', 260000, 295000, 'and_then_there_were_none.jpg', 'Mười người xa lạ, mỗi người đều che giấu một bí mật đen tối từ quá khứ, được mời đến một biệt thự biệt lập trên hòn đảo Soldier Island đầy đá ngầm. Khi cơn bão ập đến cắt đứt mọi liên lạc với đất liền, từng người một bắt đầu bị sát hại theo đúng lời của một bài đồng dao trẻ con treo trong phòng. Nỗi kinh hoàng bao trùm khi họ nhận ra kẻ sát nhân đang ẩn mình ngay giữa họ. Không có thám tử nào để giải cứu, họ phải tự mình tìm ra hung thủ trước khi không còn ai sống sót. Một kiệt tác trinh thám tâm lý kinh điển.', 272, 2011, 160, TRUE),
('The Da Vinci Code', 7, 1, 5, '9780307474278', 350000, 390000, 'da_vinci_code.jpg', 'Một vụ án mạng kỳ lạ xảy ra tại bảo tàng Louvre (Paris) vào giữa đêm khuya. Người quản lý bảo tàng trước khi chết đã kịp để lại những mật mã bí ẩn bên cạnh thi thể mình. Giáo sư Ký tượng học Robert Langdon và nhà mật mã học Sophie Neveu bị cuốn vào một cuộc chạy trốn nghẹt thở và cuộc truy tìm Chén Thánh huyền thoại. Họ phải giải mã các dấu vết ẩn giấu trong các tác phẩm nghệ thuật của Leonardo da Vinci để lật tẩy một bí mật tôn giáo chấn động đã bị che giấu suốt 2000 năm qua bởi một hội kín quyền lực.', 597, 2009, 220, TRUE),
('Angels & Demons', 7, 1, 5, '9781416524793', 340000, 380000, 'angels_demons.jpg', 'Giáo sư Robert Langdon được triệu tập đến trung tâm nghiên cứu hạt nhân CERN ở Thụy Sĩ sau khi một nhà vật lý bị sát hại dã man với dấu ấn của hội kín Illuminati khắc trên ngực. Kẻ thù của Giáo hội Công giáo đã trỗi dậy và đánh cắp một hộp chứa phản vật chất có sức công phá khủng khiếp, đe dọa sẽ hủy diệt Vatican ngay trong đêm Mật nghị Hồng y bầu chọn Giáo hoàng mới. Langdon phải chạy đua với thời gian khắp Rome, giải mã Con đường Ánh sáng cổ xưa để ngăn chặn thảm họa và cứu lấy bốn vị Ứng viên Giáo hoàng bị bắt cóc.', 736, 2005, 180, FALSE),

-- Tâm lý - Tình cảm
('The Fault in Our Stars', 8, 2, 6, '9780142424179', 265000, 300000, 'fault_in_our_stars.jpg', 'Hazel Grace Lancaster, một cô gái 16 tuổi mắc bệnh ung thư tuyến giáp di căn, sống cuộc đời gắn liền với bình oxy và những buổi trị liệu nhóm nhàm chán. Cuộc sống tẻ nhạt của cô bỗng chốc đảo lộn khi gặp Augustus Waters, một chàng trai quyến rũ và hài hước từng bị mất một chân vì ung thư xương. Họ cùng nhau chia sẻ tình yêu văn học, những nỗi sợ hãi thầm kín và một chuyến đi đến Amsterdam để gặp tác giả của cuốn sách yêu thích. Một câu chuyện tình yêu đầy tiếng cười và nước mắt, khẳng định rằng ngay cả trong những khoảnh khắc ngắn ngủi nhất, tình yêu vẫn có thể kéo dài vô tận.', 318, 2013, 190, TRUE),
('Looking for Alaska', 8, 2, 6, '9780142402511', 255000, 290000, 'looking_alaska.jpg', 'Miles "Pudge" Halter, một cậu bé hướng nội và ám ảnh với những lời trăng trối của người nổi tiếng, quyết định rời bỏ cuộc sống an toàn ở nhà để đến học tại trường nội trú Culver Creek, tìm kiếm "Điều vĩ đại có thể". Tại đây, cậu bị cuốn hút bởi Alaska Young - một cô gái thông minh, xinh đẹp, vui tính nhưng cũng đầy rắc rối và tổn thương. Alaska kéo Pudge vào thế giới của cô và đánh cắp trái tim cậu. Nhưng khi bi kịch ập đến, Pudge và những người bạn phải đối mặt với những câu hỏi không có lời giải đáp về sự sống, cái chết và cách để tha thứ.', 221, 2006, 140, FALSE),

-- Lịch sử - Chính trị
('Sapiens: A Brief History of Humankind', 9, 3, 7, '9780062316097', 420000, 470000, 'sapiens.jpg', 'Yuval Noah Harari đưa người đọc vào một hành trình đầy mê hoặc qua toàn bộ lịch sử loài người, từ những vượn người đầu tiên đi bộ trên Trái đất cho đến những bước đột phá công nghệ hiện đại. Cuốn sách giải thích làm thế nào Homo Sapiens, một loài động vật không có gì đặc biệt, đã vươn lên thống trị hành tinh này nhờ vào khả năng tưởng tượng và tin vào những huyền thoại chung như tiền bạc, quốc gia và nhân quyền. Harari cũng đặt ra những câu hỏi khiêu khích về hạnh phúc, sự tiến hóa và tương lai của loài người khi chúng ta bắt đầu đóng vai Chúa trời với công nghệ sinh học và trí tuệ nhân tạo.', 443, 2015, 250, TRUE),
('Homo Deus: A Brief History of Tomorrow', 9, 3, 7, '9780062464316', 440000, 490000, 'homo_deus.jpg', 'Tiếp nối thành công của Sapiens, Homo Deus khám phá tương lai của nhân loại trong thế kỷ 21 và xa hơn nữa. Khi nạn đói, bệnh dịch và chiến tranh dần được kiểm soát, loài người sẽ hướng tới những mục tiêu mới đầy tham vọng: sự bất tử, hạnh phúc vĩnh cửu và thần tính. Harari vẽ nên một viễn cảnh vừa hấp dẫn vừa đáng sợ, nơi dữ liệu trở thành tôn giáo mới (Dataism), trí tuệ nhân tạo vượt qua trí tuệ con người và đại đa số nhân loại có thể trở nên "vô dụng" về mặt kinh tế và chính trị. Một cuốn sách cảnh tỉnh về hướng đi của nền văn minh.', 450, 2017, 180, TRUE),
('21 Lessons for the 21st Century', 9, 3, 7, '9780525512172', 380000, 425000, '21_lessons.jpg', 'Nếu Sapiens nói về quá khứ và Homo Deus nói về tương lai, thì 21 Bài Học Cho Thế Kỷ 21 tập trung vào những vấn đề cấp bách nhất của hiện tại. Harari phân tích sâu sắc về những thách thức toàn cầu như biến đổi khí hậu, sự trỗi dậy của chủ nghĩa dân tộc, tin giả, khủng hoảng tự do dân chủ và sự gián đoạn công nghệ. Ông cung cấp cho người đọc những khung tư duy để hiểu rõ hơn về thế giới đầy biến động, giúp chúng ta giữ vững sự tỉnh táo và tìm ra ý nghĩa cuộc sống giữa nhiễu loạn thông tin. Một cẩm nang sinh tồn trí tuệ cho công dân toàn cầu.', 372, 2018, 160, TRUE),

-- Kinh tế - Quản lý
('Rich Dad Poor Dad', 12, 14, 3, '9781612680194', 320000, 360000, 'rich_dad_poor_dad.jpg', 'Cuốn sách tài chính cá nhân kinh điển đã thay đổi cuộc đời của hàng triệu người trên thế giới. Robert Kiyosaki kể về quá trình trưởng thành của mình bên cạnh hai người cha: người cha ruột có học thức cao nhưng luôn chật vật về tiền bạc (Cha Nghèo), và người cha của bạn thân, tuy chưa học hết lớp 8 nhưng lại trở thành một trong những người giàu nhất Hawaii (Cha Giàu). Thông qua những câu chuyện đối lập, tác giả làm sáng tỏ sự khác biệt trong tư duy về tiền bạc, đầu tư và sự giàu có. Sách dạy bạn cách để tiền làm việc cho mình thay vì làm việc vì tiền.', 336, 2017, 280, TRUE),
('The Lean Startup', 20, 13, 3, '9780307887894', 380000, 425000, 'lean_startup.jpg', 'Một phương pháp tiếp cận khoa học để xây dựng và quản lý các công ty khởi nghiệp thành công trong thời đại bất định. Eric Ries giới thiệu khái niệm "Khởi nghiệp Tinh gọn", tập trung vào việc rút ngắn chu kỳ phát triển sản phẩm, đo lường tiến độ thực tế và học hỏi từ phản hồi của khách hàng càng sớm càng tốt (MVP - Sản phẩm khả dụng tối thiểu). Thay vì lập kế hoạch kinh doanh chi tiết và cứng nhắc, các doanh nhân được khuyến khích thử nghiệm liên tục, thất bại nhanh và điều chỉnh chiến lược linh hoạt (Pivot) để đạt được sự phù hợp giữa sản phẩm và thị trường.', 336, 2011, 150, TRUE),
('Outliers', 10, 15, 3, '9780316017930', 350000, 390000, 'outliers.jpg', '"Những kẻ xuất chúng" là một cuộc khám phá đầy thú vị về những yếu tố thực sự tạo nên thành công vượt trội. Malcolm Gladwell thách thức quan niệm truyền thống rằng tài năng và sự nỗ lực cá nhân là tất cả. Qua hàng loạt ví dụ từ Bill Gates, The Beatles đến các vận động viên khúc côn cầu, ông chứng minh rằng hoàn cảnh xuất thân, văn hóa, thời điểm sinh ra và những cơ hội may mắn đóng vai trò quan trọng không kém. Cuốn sách nổi tiếng với "Quy tắc 10.000 giờ" - ý tưởng rằng để đạt đến trình độ kĩ năng bậc thầy trong bất kỳ lĩnh vực nào cũng cần khoảng 10.000 giờ luyện tập chuyên sâu.', 309, 2008, 170, TRUE),
('Blink', 10, 15, 3, '9780316010665', 340000, 380000, 'blink.jpg', '"Trong chớp mắt" khám phá sức mạnh của tư duy không suy nghĩ - những quyết định chúng ta đưa ra chỉ trong tích tắc. Gladwell đi sâu vào khái niệm "thin-slicing" (lát cắt mỏng), khả năng bộ não tìm ra những mô thức và đưa ra phán đoán chính xác chỉ dựa trên một lượng thông tin rất nhỏ. Cuốn sách phân tích cả hai mặt của vấn đề: khi nào thì trực giác của chúng ta đúng đắn đến kinh ngạc (như chuyên gia giám định nghệ thuật) và khi nào nó dẫn đến những định kiến sai lầm tai hại. Một cái nhìn sâu sắc về cách bộ não hoạt động và cách chúng ta có thể rèn luyện trực giác tốt hơn.', 296, 2007, 145, FALSE),

-- Kỹ năng sống
('How to Win Friends and Influence People', 11, 3, 4, '9780671027032', 295000, 330000, 'how_win_friends.jpg', 'Được xuất bản lần đầu năm 1936, "Đắc Nhân Tâm" vẫn là một trong những cuốn sách bán chạy nhất và có ảnh hưởng nhất mọi thời đại. Dale Carnegie chia sẻ những nguyên tắc vàng trong nghệ thuật ứng xử, giao tiếp và thu phục lòng người. Cuốn sách không chỉ dạy bạn cách gây thiện cảm, thuyết phục người khác theo suy nghĩ của mình mà còn giúp thay đổi người khác mà không gây ra sự chống đối hay oán giận. Những bài học về sự lắng nghe chân thành, sự tôn trọng và thấu hiểu vẫn còn nguyên giá trị trong thế giới hiện đại.', 288, 1998, 320, TRUE),
('Atomic Habits', 13, 5, 4, '9780735211292', 350000, 390000, 'atomic_habits.jpg', 'James Clear đưa ra một khung khổ thực tế và đã được chứng minh để cải thiện bản thân mỗi ngày. "Atomic Habits" (Thói quen nguyên tử) giải thích rằng những thay đổi lớn không đến từ những hành động đột phá nhất thời, mà là kết quả cộng gộp của hàng trăm quyết định nhỏ bé chúng ta thực hiện mỗi ngày. Cuốn sách hướng dẫn cách xây dựng thói quen tốt dễ dàng hơn và phá bỏ thói quen xấu, dựa trên tâm lý học và khoa học thần kinh. Bạn sẽ học được cách thiết kế môi trường sống để thành công trở thành mặc định.', 320, 2018, 290, TRUE),
('The Subtle Art of Not Giving a F*ck', 14, 3, 4, '9780062457714', 320000, 360000, 'subtle_art.jpg', 'Một cuốn sách self-help đi ngược lại với mọi lời khuyên sáo rỗng thường thấy về tư duy tích cực. Mark Manson lập luận rằng chìa khóa để có một cuộc sống tốt đẹp hơn không phải là cố gắng tích cực mọi lúc mọi nơi, mà là học cách quan tâm ít đi đến những điều không quan trọng và chỉ tập trung vào những gì thực sự có ý nghĩa. Với giọng văn hài hước, thẳng thắn và đôi khi thô lỗ, cuốn sách giúp người đọc đối diện với những sự thật phũ phàng của cuộc sống, chấp nhận những giới hạn của bản thân để tìm thấy sự tự do đích thực.', 224, 2016, 240, TRUE),
('Think and Grow Rich', 12, 5, 4, '9781585424337', 280000, 315000, 'think_grow_rich.jpg', 'Kết quả của hơn 20 năm nghiên cứu và phỏng vấn hơn 500 người thành công nhất nước Mỹ (bao gồm Henry Ford, Thomas Edison), Napoleon Hill đã đúc kết ra 13 nguyên tắc để đạt được sự giàu có và thành công. "Nghĩ Giàu Làm Giàu" không chỉ nói về tiền bạc mà còn về tư duy làm chủ cuộc đời. Sách nhấn mạnh sức mạnh của khát khao mãnh liệt, niềm tin, tự ám thị và kiên trì. Đây được coi là ông tổ của dòng sách phát triển bản thân và truyền động lực.', 238, 2005, 200, FALSE),

-- Khoa học - Công nghệ
('Clean Code', 19, 10, 8, '9780132350884', 650000, 720000, 'clean_code.jpg', 'Ngay cả code tệ cũng có thể hoạt động, nhưng nếu code không sạch, nó có thể hủy hoại cả một tổ chức phát triển phần mềm. "Clean Code" của Robert C. Martin (Uncle Bob) là cuốn kinh thánh dành cho mọi lập trình viên muốn nâng cao tay nghề. Cuốn sách chia sẻ các quy tắc, nguyên lý và thực hành tốt nhất để viết mã dễ đọc, dễ bảo trì và ít lỗi. Từ cách đặt tên biến, cấu trúc hàm, xử lý lỗi đến unit testing, cuốn sách cung cấp những ví dụ thực tế (bằng Java) giúp bạn biến code của mình thành một tác phẩm nghệ thuật.', 464, 2008, 120, TRUE),
('The Pragmatic Programmer', 19, 10, 8, '9780135957059', 680000, 750000, 'pragmatic_programmer.jpg', 'Một trong những cuốn sách quan trọng nhất về kỹ thuật phần mềm từng được viết. Andy Hunt và Dave Thomas không chỉ dạy về cú pháp ngôn ngữ hay thuật toán, mà dạy về tư duy và thái độ của một lập trình viên chuyên nghiệp ("Pragmatic Programmer"). Cuốn sách chứa đựng những lời khuyên vượt thời gian về mọi khía cạnh của nghề, từ trách nhiệm cá nhân, phát triển sự nghiệp, đến các kỹ thuật cụ thể để giữ code linh hoạt và dễ thích ứng. Phiên bản kỷ niệm 20 năm đã được cập nhật để phù hợp với bối cảnh công nghệ hiện đại.', 352, 2019, 95, TRUE),
('Design Patterns', 19, 10, 8, '9780201633610', 720000, 800000, 'design_patterns.jpg', 'Cuốn sách kinh điển của nhóm "Gang of Four" (GoF) đã định hình lại cách thiết kế phần mềm hướng đối tượng. Cuốn sách giới thiệu 23 mẫu thiết kế (design patterns) - là những giải pháp đã được kiểm chứng cho các vấn đề thường gặp trong quá trình phát triển phần mềm. Thay vì phải "phát minh lại bánh xe", các lập trình viên có thể áp dụng các mẫu này để tạo ra các hệ thống linh hoạt, dễ tái sử dụng và bảo trì. Mặc dù các ví dụ sử dụng C++ và Smalltalk, các tư tưởng trong sách vẫn là nền tảng kiến thức bắt buộc cho mọi kiến trúc sư phần mềm.', 416, 1994, 80, FALSE),
('Cracking the Coding Interview', 19, 10, 8, '9780984782857', 580000, 640000, 'cracking_coding.jpg', 'Cuốn cẩm nang chuẩn bị phỏng vấn xin việc toàn diện nhất dành cho các kĩ sư phần mềm muốn gia nhập các công ty công nghệ hàng đầu (FAANG). Tác giả Gayle Laakmann McDowell, cựu nhân viên tuyển dụng của Google, cung cấp 189 câu hỏi phỏng vấn lập trình thực tế từ cơ bản đến nâng cao, kèm theo lời giải chi tiết và phân tích độ phức tạp thuật toán. Sách cũng chia sẻ các chiến lược để giải quyết các bài toán chưa từng gặp, cách trình bày tư duy trước bảng trắng và những kỹ năng mềm cần thiết để gây ấn tượng với nhà tuyển dụng.', 687, 2015, 110, TRUE),

-- Thiếu nhi
('The Hunger Games', 15, 6, 9, '9780439023481', 280000, 315000, 'hunger_games.jpg', 'Tại đất nước Panem hoang tàn, trên tàn tích của Bắc Mỹ cũ, mười hai quận bị cai trị tàn bạo bởi Capitol xa hoa. Hàng năm, mỗi quận phải cống nạp một nam và một nữ thiếu niên để tham gia Đấu trường Sinh tử - một chương trình truyền hình thực tế nơi 24 đứa trẻ phải chiến đấu đến chết cho đến khi chỉ còn một người sống sót. Katniss Everdeen, 16 tuổi, tình nguyện thay thế em gái mình bước vào đấu trường. Cô phải dựa vào bản năng săn bắn sắc bén và sự dẫn dắt của người cố vấn say xỉn để tồn tại giữa tình yêu, sự phản bội và chính trị.', 374, 2008, 240, TRUE),
('Catching Fire', 15, 6, 9, '9780439023498', 290000, 325000, 'catching_fire.jpg', 'Sau khi chiến thắng Đấu trường Sinh tử lần thứ 74, Katniss Everdeen và Peeta Mellark trở về nhà, hy vọng vào một cuộc sống bình yên. Nhưng chiến thắng của họ lại châm ngòi cho một cuộc nổi dậy âm ỉ khắp các quận của Panem. Tổng thống Snow đe dọa Katniss, buộc cô phải dập tắt ngọn lửa hy vọng mà cô đã vô tình thắp lên. Vào dịp Quarter Quell kỉ niệm 75 năm, một thông báo chấn động được đưa ra: Những vật tế cho đấu trường năm nay sẽ được chọn từ những người chiến thắng còn sống sót. Katniss nhận ra mình phải quay lại nơi địa ngục đó một lần nữa.', 391, 2009, 220, TRUE),
('The Lightning Thief', 16, 12, 9, '9780786838653', 260000, 295000, 'lightning_thief.jpg', 'Percy Jackson là một cậu bé 12 tuổi rắc rối, mắc chứng khó đọc và tăng động giảm chú ý. Cuộc sống của cậu đảo lộn khi phát hiện ra mình là một Á thần (Demigod) - con trai của thần biển Poseidon trong thần thoại Hy Lạp. Cậu được đưa đến Trại Con Lai để được bảo vệ và huấn luyện. Nhưng rắc rối thực sự bắt đầu khi tia sét quyền năng của thần Zeus bị đánh cắp và Percy trở thành nghi phạm chính. Cùng với hai người bạn, cậu phải thực hiện một hành trình xuyên nước Mỹ để tìm lại tia sét, minh oan cho bản thân và ngăn chặn một cuộc chiến tranh giữa các vị thần.', 377, 2005, 280, TRUE),
('The Sea of Monsters', 16, 12, 9, '9780786856294', 265000, 300000, 'sea_monsters.jpg', 'Năm học lớp 7 của Percy Jackson trôi qua một cách yên ắng đến kỳ lạ, cho đến khi những giấc mơ ác mộng báo hiệu nguy hiểm ập đến. Trại Con Lai, nơi trú ẩn an toàn duy nhất của các á thần, đang bị tấn công. Cây thông Thalia bảo vệ biên giới trại bị đầu độc và đang chết dần. Để cứu trại, Percy và những người bạn phải dấn thân vào vùng Biển Quái Vật (được người thường biết đến là Tam giác Bermuda) đầy rẫy hiểm nguy để tìm kiếm Bộ Lông Cừu Vàng huyền thoại. Trong hành trình này, Percy cũng khám phá ra bí mật gây sốc về gia đình mình.', 279, 2006, 250, TRUE),
('Diary of a Wimpy Kid', 17, 6, 9, '9780810993136', 220000, 250000, 'wimpy_kid_1.jpg', 'Là một đứa trẻ thì thật là phiền phức, và không ai hiểu điều đó rõ hơn Greg Heffley. Bị mắc kẹt ở trường trung học cơ sở, nơi những đứa trẻ yếu ớt phải chia sẻ hành lang với những đứa to con, xấu tính và đã bắt đầu cạo râu. Greg ghi lại những trải nghiệm "đau thương" này trong cuốn nhật ký của mình (đừng gọi nó là nhật ký!), với những hình vẽ minh họa hài hước và những suy nghĩ thật thà đến mức bật cười. Cuốn sách là cái nhìn chân thực và dí dỏm về cuộc sống học đường, tình bạn và những nỗ lực (thường là thất bại) để trở nên nổi tiếng.', 217, 2007, 300, TRUE),
('Dog Man', 18, 6, 9, '9780545581608', 210000, 240000, 'dog_man_1.jpg', 'Từ tác giả của Captain Underpants! Khi sĩ quan cảnh sát Knight và chú chó cảnh sát Greg bị thương nặng trong một vụ nổ bom do tên mèo ác nhân Petey gây ra, một cuộc phẫu thuật cứu mạng điên rồ đã thay đổi lịch sử. Bác sĩ đã khâu đầu của chú chó vào cơ thể của cảnh sát, và Dog Man - Người Chó ra đời! Với cái đầu của chó và cơ thể của người, Dog Man là chiến sĩ chống tội phạm vĩ đại nhất... miễn là anh ta không bị phân tâm bởi những chú sóc hay quả bóng tennis. Một bộ truyện tranh vui nhộn, đầy màu sắc và hành động dành cho mọi lứa tuổi.', 240, 2016, 270, TRUE),

-- Truyện tranh - Manga
('One Piece Vol.1', NULL, 11, 10, '9781569319017', 180000, 200000, 'one_piece_1.jpg', 'Mở đầu cho bộ manga bán chạy nhất mọi thời đại! Monkey D. Luffy, một cậu bé mang trong mình ước mơ cháy bỏng trở thành Vua Hải Tặc, đã vô tình ăn phải Trái Ác Quỷ Gomu Gomu no Mi, khiến cơ thể cậu có khả năng co giãn như cao su nhưng đổi lại cậu sẽ không bao giờ biết bơi. Không nản lòng, Luffy giong buồm ra khơi từ Làng Cối Xay Gió, bắt đầu cuộc hành trình vĩ đại tìm kiếm kho báu huyền thoại "One Piece" mà Vua Hải Tặc đời trước để lại. Trên đường đi, cậu tập hợp những đồng đội kỳ quặc nhưng đáng tin cậy để thành lập băng Mũ Rơm.', 216, 2003, 320, TRUE),
('Naruto Vol.1', NULL, 11, 10, '9781569319000', 180000, 200000, 'naruto_1.jpg', 'Mười hai năm trước, làng Lá bị tấn công bởi Cửu Vĩ Hồ Ly đáng sợ. Để bảo vệ dân làng, Hokage Đệ Tứ đã hy sinh thân mình phong ấn con quái vật vào cơ thể một đứa trẻ sơ sinh: Naruto Uzumaki. Lớn lên không cha mẹ và bị dân làng xa lánh, Naruto trở thành một cậu bé nghịch ngợm luôn tìm cách gây chú ý. Nhưng sâu thẳm bên trong, cậu mang một ước mơ vĩ đại: trở thành Hokage - người lãnh đạo làng và được mọi người công nhận. Đây là câu chuyện về sự trưởng thành, nỗ lực không ngừng nghỉ và con đường trở thành một ninja vĩ đại của Naruto.', 192, 2003, 300, TRUE),
('Attack on Titan Vol.1', NULL, 11, 10, '9781612620244', 195000, 220000, 'aot_1.jpg', 'Hàng trăm năm trước, nhân loại gần như bị diệt vong bởi những sinh vật khổng lồ ăn thịt người gọi là Titan. Những người sống sót đã rút về cố thủ sau ba bức tường thành khổng lồ kiên cố, mua lấy sự bình yên trong hơn một thế kỷ. Nhưng sự bình yên giả tạo đó tan vỡ khi một Titan Đại Hình xuất hiện và phá vỡ bức tường ngoài cùng, reo rắc nỗi kinh hoàng tột độ. Eren Yeager, cậu bé chứng kiến mẹ mình bị ăn thịt ngay trước mắt, đã thề sẽ tiêu diệt tất cả Titan trên thế giới này. Một bộ manga đen tối, tàn khốc nhưng đầy lôi cuốn.', 194, 2012, 260, TRUE),
('Death Note Vol.1', NULL, 11, 10, '9781421501680', 185000, 210000, 'death_note_1.jpg', 'Light Yagami là một học sinh trung học thiên tài nhưng chán nản với thế giới đầy rẫy tội ác và bất công. Cuộc đời cậu thay đổi khi nhặt được "Death Note" (Cuốn sổ Thiên mệnh) do một Thần chết đánh rơi. Bất kỳ ai bị ghi tên vào cuốn sổ này đều sẽ chết. Nắm trong tay quyền lực của chúa trời, Light quyết định thanh lọc thế giới bằng cách giết chết những tên tội phạm, tự xưng là "Kira" (Vị thần của thế giới mới). Nhưng hành động của cậu đã thu hút sự chú ý của L - thám tử lập dị tài ba nhất thế giới. Cuộc đấu trí căng thẳng giữa hai bộ óc thiên tài bắt đầu.', 200, 2005, 280, TRUE),
('My Hero Academia Vol.1', NULL, 11, 10, '9781421582696', 180000, 200000, 'mha_1.jpg', 'Trong một thế giới mà 80% dân số sinh ra với siêu năng lực (gọi là "Quirk"), nghề "Anh hùng" chuyên nghiệp trở thành hiện thực. Izuku Midoriya là một trong số ít những người sinh ra "vô năng", nhưng cậu vẫn luôn mơ ước được trở thành anh hùng như thần tượng All Might của mình. Bất chấp sự chế giễu của bạn bè, tinh thần dũng cảm cứu người của cậu đã gây ấn tượng với All Might. Vị anh hùng số 1 thế giới quyết định chọn Izuku làm người kế thừa sức mạnh của mình. Cậu bé bắt đầu hành trình gian khổ tại Học viện U.A danh tiếng để chứng minh rằng ai cũng có thể trở thành anh hùng.', 192, 2015, 290, TRUE);

-- =============================================
-- SEED DATA - KHÁCH HÀNG
-- =============================================
-- Password: password (hashed)
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
-- NOTE: Stored Procedure disabled for Cloud Import compatibility

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
-- SEED DATA - REVIEWS
-- =============================================
DELIMITER $$
CREATE PROCEDURE sp_seed_reviews(IN p_count INT)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE v_cust INT;
    DECLARE v_book INT;
    DECLARE v_rating INT;
    DECLARE v_title VARCHAR(255);
    DECLARE v_content TEXT;
    DECLARE v_status VARCHAR(20);
    
    WHILE i < p_count DO
        -- Pick random customer and book
        SELECT id_khachhang INTO v_cust FROM khachhang ORDER BY RAND() LIMIT 1;
        SELECT id_sach INTO v_book FROM sach ORDER BY RAND() LIMIT 1;
        
        -- Generate content
        SET v_rating = FLOOR(1 + RAND() * 5);
        
        CASE v_rating
            WHEN 1 THEN SET v_title = 'Thất vọng';
            WHEN 2 THEN SET v_title = 'Chưa hài lòng';
            WHEN 3 THEN SET v_title = 'Tạm ổn';
            WHEN 4 THEN SET v_title = 'Sách hay';
            ELSE SET v_title = 'Tuyệt vời, nên đọc!';
        END CASE;
        
        SET v_content = CONCAT('Nội dung đánh giá mẫu cho sách #', v_book, '. Đánh giá này được tạo tự động.');
        
        -- 90% approved, 10% pending
        IF RAND() < 0.9 THEN
            SET v_status = 'approved';
        ELSE
            SET v_status = 'pending';
        END IF;
        
        -- Insert ignoring duplicates (same user on same book)
        INSERT IGNORE INTO danhgia (id_sach, id_khachhang, so_sao, tieu_de, noi_dung, trang_thai)
        VALUES (v_book, v_cust, v_rating, v_title, v_content, v_status);
        
        -- Only increment if successful insert (row_count > 0)
        IF ROW_COUNT() > 0 THEN
            SET i = i + 1;
        END IF;
        
        -- Breaker to prevent infinite loop if highly populated
        IF i > p_count * 5 THEN -- Allow more attempts
             SET i = p_count;
        END IF;
    END WHILE;
END$$
DELIMITER ;

CALL sp_seed_reviews(100);
DROP PROCEDURE sp_seed_reviews;

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
--
UPDATE khachhang SET email_verified_at = NOW() WHERE email_verified_at IS NULL;
