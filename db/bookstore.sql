-- =============================================
-- BOOKSTORE DATABASE - MySQL
-- Created: December 9, 2025
-- Description: Complete database structure and seed data for online bookstore
-- =============================================

-- Drop database if exists and create new one
DROP DATABASE IF EXISTS bookstore;
CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookstore;

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
('Nguyễn Nhật Ánh', NULL, 'Nhà văn nổi tiếng Việt Nam với nhiều tác phẩm thiếu nhi và tuổi học trò', '1955-05-07', 'Việt Nam', 'nguyen_nhat_anh.jpg'),
('Nguyễn Ngọc Tư', NULL, 'Nhà văn nữ với nhiều tác phẩm về miền Tây Nam Bộ', '1976-01-01', 'Việt Nam', 'nguyen_ngoc_tu.jpg'),
('Nguyễn Du', NULL, 'Đại thi hào Việt Nam, tác giả truyện Kiều', '1766-01-03', 'Việt Nam', 'nguyen_du.jpg'),
('Paulo Coelho', NULL, 'Nhà văn Brazil nổi tiếng với tác phẩm Nhà giả kim', '1947-08-24', 'Brazil', 'paulo_coelho.jpg'),
('Haruki Murakami', NULL, 'Nhà văn Nhật Bản đương đại nổi tiếng', '1949-01-12', 'Nhật Bản', 'haruki_murakami.jpg'),
('Dale Carnegie', NULL, 'Tác giả nổi tiếng về kỹ năng sống và giao tiếp', '1888-11-24', 'Mỹ', 'dale_carnegie.jpg'),
('Tony Buzan', NULL, 'Chuyên gia về tư duy và trí nhớ', '1942-06-02', 'Anh', 'tony_buzan.jpg'),
('Robert Kiyosaki', NULL, 'Tác giả sách về tài chính và đầu tư', '1947-04-08', 'Mỹ', 'robert_kiyosaki.jpg'),
('J.K. Rowling', NULL, 'Tác giả series Harry Potter', '1965-07-31', 'Anh', 'jk_rowling.jpg'),
('Nam Cao', NULL, 'Nhà văn hiện thực Việt Nam', '1915-10-29', 'Việt Nam', 'nam_cao.jpg');

-- =============================================
-- SEED DATA - NHÀ XUẤT BẢN
-- =============================================
INSERT INTO nhaxuatban (ten_nxb, dia_chi, dien_thoai, email, website) VALUES
('NXB Trẻ', '161B Lý Chính Thắng, Quận 3, TP.HCM', '02839316211', 'info@nxbtre.com.vn', 'https://www.nxbtre.com.vn'),
('NXB Kim Đồng', '55 Quang Trung, Hai Bà Trưng, Hà Nội', '02439434730', 'info@kimdong.com.vn', 'https://kimdong.com.vn'),
('NXB Văn học', '18 Nguyễn Trường Tộ, Ba Đình, Hà Nội', '02438223398', 'vanhocsv@hn.vnn.vn', 'http://nxbvanhoc.com.vn'),
('NXB Lao động', '175 Giảng Võ, Đống Đa, Hà Nội', '02438514238', 'info@nxblaodong.com.vn', 'http://nxblaodong.com.vn'),
('NXB Thế Giới', '7 Nguyễn Thị Minh Khai, Quận 1, TP.HCM', '02838222446', 'info@thegioipublishers.vn', 'https://thegioipublishers.vn'),
('NXB Tổng hợp TP.HCM', '62 Nguyễn Thị Minh Khai, Quận 3, TP.HCM', '02839309498', 'info@nxbhcm.com.vn', 'http://www.nxbhcm.com.vn'),
('NXB Phụ nữ', '39 Hàng Chuối, Hai Bà Trưng, Hà Nội', '02839717979', 'nxbphunu@hn.vnn.vn', 'http://nxbphunu.com.vn'),
('NXB Hội Nhà văn', '65 Nguyễn Du, Hai Bà Trưng, Hà Nội', '02438222135', 'info@nxbhnv.com.vn', 'http://nxbhoinhavan.com.vn'),
('NXB Đại học Quốc gia', '16 Hàng Chuối, Hai Bà Trưng, Hà Nội', '02438694256', 'info@nxbdhqg.vn', 'http://nxbdhqg.vn'),
('First News', '81 Quang Trung, Gò Vấp, TP.HCM', '02838437228', 'info@firstnews.com.vn', 'https://firstnews.com.vn');

-- =============================================
-- SEED DATA - SÁCH
-- =============================================
INSERT INTO sach (ten_sach, id_tacgia, id_nxb, id_theloai, isbn, gia, gia_goc, hinh_anh, mo_ta, so_trang, nam_xuat_ban, so_luong_ton, noi_bat) VALUES
-- Văn học Việt Nam
('Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 1, 1, 1, '9786041027237', 108000, 120000, 'toi_thay_hoa_vang.jpg', 'Truyện kể về tuổi thơ nghèo khó nhưng đẹp đẽ ở miền quê Việt Nam của hai anh em Thiều và Tường cùng với những người bạn.', 368, 2018, 150, TRUE),
('Cho Tôi Xin Một Vé Đi Tuổi Thơ', 1, 1, 1, '9786041027244', 95000, 105000, 've_di_tuoi_tho.jpg', 'Những mẩu chuyện nhỏ về tuổi thơ thân thương và đầy hoài niệm.', 324, 2018, 120, TRUE),
('Mắt Biếc', 1, 1, 1, '9786041027251', 110000, 125000, 'mat_biec.jpg', 'Câu chuyện tình đẹp mà buồn giữa Ngạn và Hà Lan, mối tình thầm lặng mà say đắm.', 296, 2019, 200, TRUE),
('Cánh Đồng Bất Tận', 2, 1, 1, '9786041045293', 120000, 140000, 'canh_dong_bat_tan.jpg', 'Truyện dài về cuộc sống miền Tây Nam Bộ với những con người bình dị.', 368, 2020, 80, FALSE),
('Truyện Kiều', 3, 3, 1, '9786041046587', 85000, 95000, 'truyen_kieu.jpg', 'Tác phẩm kinh điển của văn học Việt Nam, kể về số phận đau khổ của Thuý Kiều.', 244, 2019, 100, TRUE),
('Chí Phèo', 10, 3, 1, '9786041047829', 65000, 75000, 'chi_pheo.jpg', 'Truyện ngắn nổi tiếng về cuộc đời cùng cực của Chí Phèo.', 156, 2018, 90, FALSE),

-- Văn học nước ngoài
('Nhà Giả Kim', 4, 5, 2, '9786041028067', 79000, 89000, 'nha_gia_kim.jpg', 'Câu chuyện về chuyến hành trình tìm kiếm kho báu và ý nghĩa cuộc sống của cậu bé chăn cừu Santiago.', 227, 2020, 250, TRUE),
('Kafka Bên Bờ Biển', 5, 5, 2, '9786041029132', 155000, 175000, 'kafka_ben_bo_bien.jpg', 'Tiểu thuyết siêu thực về cuộc hành trình của cậu bé Kafka chạy trốn lời nguyền định mệnh.', 568, 2019, 100, TRUE),
('Rừng Na Uy', 5, 5, 2, '9786041030234', 139000, 155000, 'rung_na_uy.jpg', 'Câu chuyện tình yêu và nỗi đau của tuổi trẻ trong bối cảnh Nhật Bản những năm 1960.', 464, 2020, 150, TRUE),
('Tôi Là Bêtô', 1, 1, 2, '9786041045385', 92000, 105000, 'toi_la_beto.jpg', 'Chuyện kể từ góc nhìn của chú bò sữa Bêtô, nhân cách hóa để nói về cuộc sống.', 280, 2021, 75, FALSE),

-- Kinh tế - Quản lý
('Dạy Con Làm Giàu (Tập 1)', 8, 4, 3, '9786041031245', 99000, 115000, 'day_con_lam_giau_1.jpg', 'Bài học về tài chính và đầu tư từ "người cha giàu" và "người cha nghèo".', 328, 2020, 180, TRUE),
('Dạy Con Làm Giàu (Tập 2)', 8, 4, 3, '9786041031252', 99000, 115000, 'day_con_lam_giau_2.jpg', 'Tiếp tục những bài học về tư duy tài chính và đầu tư thông minh.', 352, 2020, 160, FALSE),
('Đắc Nhân Tâm', 6, 4, 3, '9786041032156', 86000, 98000, 'dac_nhan_tam.jpg', 'Cuốn sách kinh điển về nghệ thuật giao tiếp và ứng xử.', 320, 2019, 300, TRUE),

-- Kỹ năng sống
('Nghĩ Giàu Và Làm Giàu', 8, 4, 4, '9786041033234', 92000, 105000, 'nghi_giau_lam_giau.jpg', 'Bí quyết thành công từ những người giàu có nhất thế giới.', 368, 2020, 120, FALSE),
('Sức Mạnh Của Tư Duy Tích Cực', 6, 4, 4, '9786041034156', 79000, 89000, 'tu_duy_tich_cuc.jpg', 'Cách thay đổi cuộc sống thông qua tư duy tích cực.', 256, 2019, 95, FALSE),
('Đọc Vị Bất Kỳ Ai', 6, 4, 4, '9786041035234', 95000, 108000, 'doc_vi_bat_ky_ai.jpg', 'Nghệ thuật đọc hiểu ngôn ngữ cơ thể và tâm lý con người.', 304, 2021, 110, TRUE),

-- Khoa học - Công nghệ
('Clean Code', NULL, 9, 8, '9780132350884', 450000, 500000, 'clean_code.jpg', 'Cẩm nang viết code sạch và chuyên nghiệp cho lập trình viên.', 464, 2020, 60, TRUE),
('Design Patterns', NULL, 9, 8, '9780201633612', 520000, 580000, 'design_patterns.jpg', 'Các mẫu thiết kế trong lập trình hướng đối tượng.', 395, 2019, 45, FALSE),

-- Thiếu nhi
('Đắc Nhân Tâm Dành Cho Tuổi Teen', 6, 2, 9, '9786041036123', 78000, 88000, 'dac_nhan_tam_teen.jpg', 'Phiên bản dành cho tuổi teen với ngôn ngữ gần gũi, dễ hiểu.', 256, 2021, 140, TRUE),
('Nhà Khoa Học Nhỏ Tuổi', NULL, 2, 9, '9786041037234', 65000, 75000, 'nha_khoa_hoc_nho_tuoi.jpg', 'Những thí nghiệm khoa học thú vị dành cho trẻ em.', 180, 2020, 100, FALSE),
('Thám Tử Lừng Danh Conan (Tập 1)', NULL, 2, 10, '9786041038156', 25000, 30000, 'conan_tap_1.jpg', 'Manga trinh thám nổi tiếng về cậu bé Conan.', 192, 2021, 200, TRUE);

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
INSERT INTO hoadon (id_khachhang, ma_hoadon, tong_tien, trang_thai, phuong_thuc_thanh_toan, trang_thai_thanh_toan, ten_nguoi_nhan, dia_chi_giao, sdt_giao, email_giao) VALUES
(1, 'HD001', 295000, 'completed', 'COD', 'paid', 'Nguyễn Văn A', '123 Nguyễn Huệ, Q1, TP.HCM', '0901234567', 'nguyenvana@gmail.com'),
(2, 'HD002', 467000, 'shipping', 'transfer', 'paid', 'Trần Thị B', '456 Lê Lợi, Q3, TP.HCM', '0912345678', 'tranthib@gmail.com'),
(3, 'HD003', 203000, 'pending', 'COD', 'unpaid', 'Lê Văn C', '789 Trần Hưng Đạo, Q5, TP.HCM', '0923456789', 'levanc@gmail.com'),
(4, 'HD004', 534000, 'confirmed', 'momo', 'paid', 'Phạm Thị D', '321 Võ Văn Tần, Q3, TP.HCM', '0934567890', 'phamthid@gmail.com'),
(5, 'HD005', 174000, 'completed', 'vnpay', 'paid', 'Hoàng Văn E', '654 Pasteur, Q1, TP.HCM', '0945678901', 'hoangvane@gmail.com');

-- =============================================
-- SEED DATA - CHI TIẾT HÓA ĐƠN
-- =============================================
INSERT INTO chitiet_hoadon (id_hoadon, id_sach, so_luong, gia, thanh_tien) VALUES
-- HD001
(1, 1, 2, 108000, 216000),
(1, 5, 1, 79000, 79000),
-- HD002
(2, 7, 1, 79000, 79000),
(2, 8, 1, 155000, 155000),
(2, 13, 1, 86000, 86000),
(2, 3, 1, 110000, 110000),
(2, 17, 1, 25000, 25000),
-- HD003
(3, 1, 1, 108000, 108000),
(3, 6, 1, 95000, 95000),
-- HD004
(4, 11, 2, 99000, 198000),
(4, 8, 1, 155000, 155000),
(4, 16, 1, 95000, 95000),
(4, 13, 1, 86000, 86000),
-- HD005
(5, 17, 2, 25000, 50000),
(5, 19, 1, 65000, 65000),
(5, 15, 1, 79000, 79000);

-- =============================================
-- SEED DATA - ĐÁNH GIÁ
-- =============================================
INSERT INTO danhgia (id_sach, id_khachhang, so_sao, tieu_de, noi_dung, trang_thai) VALUES
(1, 1, 5, 'Tuyệt vời!', 'Cuốn sách rất hay, gợi nhớ lại tuổi thơ của mình. Viết rất chân thực và cảm động.', 'approved'),
(1, 2, 5, 'Đọc mà khóc', 'Đọc xong mà muốn khóc, nhớ lại bao nhiêu kỷ niệm tuổi thơ.', 'approved'),
(7, 3, 5, 'Kinh điển', 'Nhà giả kim là cuốn sách mà ai cũng nên đọc ít nhất một lần trong đời.', 'approved'),
(8, 4, 4, 'Hay nhưng hơi khó hiểu', 'Kafka bên bờ biển viết hay nhưng có phần hơi khó hiểu, cần đọc kỹ.', 'approved'),
(11, 5, 5, 'Must read!', 'Cuốn sách thay đổi tư duy của tôi về tài chính. Rất bổ ích!', 'approved'),
(13, 1, 5, 'Kinh điển vượt thời gian', 'Đắc nhân tâm là cuốn sách mà mình đọc đi đọc lại nhiều lần.', 'approved'),
(3, 2, 5, 'Tình yêu đẹp nhất', 'Mắt Biếc là câu chuyện tình đẹp và buồn nhất mà mình từng đọc.', 'approved');

-- =============================================
-- SEED DATA - GIỎ HÀNG
-- =============================================
INSERT INTO giohang (id_khachhang, id_sach, so_luong) VALUES
(1, 9, 1),
(1, 14, 2),
(2, 17, 3),
(3, 7, 1),
(3, 11, 1),
(4, 1, 2);

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
(1, 16),
(2, 11),
(3, 1),
(3, 7),
(4, 9),
(5, 13);

-- =============================================
-- SEED DATA - BANNER
-- =============================================
INSERT INTO banner (tieu_de, hinh_anh, link_url, thu_tu, trang_thai) VALUES
('Khuyến mãi năm mới 2025', 'banner_new_year.jpg', '/promotion/newyear2025', 1, 'active'),
('Sách best seller', 'banner_bestseller.jpg', '/books/bestseller', 2, 'active'),
('Giảm giá sách thiếu nhi', 'banner_thieunhi.jpg', '/category/thieunhi', 3, 'active'),
('Mua 2 tặng 1', 'banner_mua2tang1.jpg', '/promotion/buy2get1', 4, 'active');

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
