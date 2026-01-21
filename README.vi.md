# 📚 Dự án Thương Mại Điện Tử BookStore

> Một website bán sách trực tuyến hoàn chỉnh, sẵn sàng cho môi trường thực tế, được xây dựng bằng **PHP Thuần (MVC)**. Dự án bao gồm giao diện khách hàng đầy đủ tính năng và trang quản trị (admin) mạnh mẽ.

## 📖 Mục Lục

*   [Tổng quan dự án](#-tổng-quan-dự-án)
*   [Tính năng chính](#-tính-năng-chính)
    *   [Giao diện khách hàng](#giao-diện-khách-hàng)
    *   [Trang quản trị (Admin)](#trang-quản-trị-admin)
*   [Công nghệ sử dụng](#-công-nghệ-sử-dụng)
*   [Cấu trúc dự án](#-cấu-trúc-dự-án)
*   [Cài đặt & Thiết lập](#-cài-đặt--thiết-lập)
*   [Cấu hình](#-cấu-hình)
*   [Tài khoản mặc định](#-tài-khoản-mặc-định)
*   [Khắc phục sự cố](#-khắc-phục-sự-cố)

---

## 📋 Tổng quan dự án

Dự án này là giải pháp thương mại điện tử hoàn chỉnh cho một cửa hàng sách trực tuyến. Nó minh họa cách xây dựng một ứng dụng web có khả năng mở rộng, dễ bảo trì bằng cách sử dụng mô hình kiến trúc **Model-View-Controller (MVC)** mà không phụ thuộc vào các framework nặng nề như Laravel hay Symfony. Đây là tài liệu tham khảo tuyệt vời để hiểu các nguyên tắc cốt lõi của phát triển web, PHP session, tương tác cơ sở dữ liệu và bảo mật.

**Điểm nổi bật:**
*   **Phân tách rõ ràng:** Tách biệt giữa logic xử lý (Controllers), dữ liệu (Models) và giao diện hiển thị (Views).
*   **Chức năng thực tế:** Bao gồm quản lý giỏ hàng, xử lý đơn hàng, gửi email thông báo và tích hợp cổng thanh toán.
*   **Bản địa hóa:** Giao diện người dùng (UI) được thiết kế bằng **tiếng Việt** cho đối tượng người dùng cụ thể, trong khi mã nguồn và cấu trúc cơ sở dữ liệu sử dụng **tiếng Anh** theo chuẩn quốc tế.

---

## 🚀 Tính năng chính

### Giao diện khách hàng
Front-end được thiết kế để cung cấp trải nghiệm mua sắm liền mạch:
*   **📚 Duyệt sách thông minh:**
    *   Chế độ xem Lưới và Danh sách.
    *   Bộ lọc nâng cao theo Khoảng giá, Danh mục, Tác giả, Nhà xuất bản và Đánh giá.
    *   Sắp xếp động (Giá, Mới nhất, Bán chạy).
*   **🔍 Tìm kiếm tức thì:** Tìm kiếm sách và tác giả theo thời gian thực.
*   **🛒 Giỏ hàng:**
    *   Cập nhật AJAX (thêm, xóa, sửa số lượng) mà không cần tải lại trang.
    *   Tính tổng tiền theo thời gian thực.
*   **💳 Thanh toán bảo mật:**
    *   Đa dạng phương thức: **COD** (Thanh toán khi nhận hàng), **Chuyển khoản**, hoặc **Thanh toán Online** (qua PayOS).
    *   Hệ thống mã giảm giá (Coupon).
*   **👤 Hồ sơ người dùng:**
    *   Theo dõi lịch sử đơn hàng với trạng thái chi tiết (Đang xử lý -> Đang giao -> Hoàn thành).
    *   Quản lý thông tin cá nhân (Avatar, Địa chỉ, SĐT).
    *   Đổi mật khẩu bảo mật.
    *   **Quên mật khẩu** qua mã OTP/Link gửi về email.
*   **⭐ Đánh giá & Bình luận:** Khách hàng có thể đánh giá và viết nhận xét cho sách đã mua.

### Trang quản trị (Admin)
Bảng điều khiển toàn diện dành cho chủ cửa hàng:
*   **📊 Dashboard thống kê:** Biểu đồ trực quan về Doanh thu, Trạng thái đơn hàng và Sản phẩm bán chạy.
*   **📦 Quản lý kho hàng:**
    *   **Sách:** Thay đổi đầy đủ (Thêm, Xem, Sửa, Xóa) với tính năng upload ảnh.
    *   **Danh mục/Tác giả/NXB:** Quản lý phân loại và dữ liệu meta.
*   **🧾 Quản lý đơn hàng:**
    *   Xem chi tiết đơn hàng và thông tin khách hàng.
    *   Cập nhật trạng thái đơn hàng (Xử lý từ Chờ xác nhận đến Hoàn thành).
*   **👥 Quản lý khách hàng:** Xem và quản lý người dùng đã đăng ký.
*   **📢 Marketing:** Quản lý banner quảng cáo và mã giảm giá.
*   **📈 Báo cáo:** Xuất báo cáo hiệu quả kinh doanh.

---

## 💻 Công nghệ sử dụng

*   **Ngôn ngữ Backend:** PHP 5.6+ (Tương thích tốt với PHP 7.x và 8.x).
*   **Cơ sở dữ liệu:** MySQL / MariaDB sử dụng Extension `MySQLi`.
*   **Thư viện Frontend:** Bootstrap 4, jQuery, FontAwesome 5.
*   **Kiến trúc:** Mô hình MVC tùy chỉnh.
*   **Thư viện bên ngoài (qua Composer):**
    *   `phpmailer/phpmailer`: Gửi email SMTP tin cậy.
    *   `payos/payos`: Tích hợp cổng thanh toán QR Việt Nam.

---

## 📂 Cấu trúc dự án

```text
BookStore/
├── Admin/                  # 🔒 Module Quản trị
│   ├── Controller/         # Logic Admin (Xác thực, Thống kê, CRUD)
│   ├── Model/              # Truy vấn CSDL Admin
│   └── View/               # Giao diện Admin (Dashboard, Biểu mẫu)
├── config/                 # ⚙️ Cấu hình chung (SMTP, Key)
├── Content/                # 🎨 Tài nguyên tĩnh
│   ├── CSS/                # File CSS
│   └── images/             # Ảnh đã upload (sách, banner)
├── Controller/             # 🧠 Logic nghiệp vụ Front-end
├── db/                     # 💾 Script SQL
│   ├── bookstore.sql       # Schema & Dữ liệu mẫu
│   └── DATABASE_ERD.md     # Sơ đồ CSDL
├── docs/                   # 📄 Tài liệu dự án
├── Model/                  # 🗄️ Lớp truy cập dữ liệu (giống ORM)
├── View/                   # 🖼️ Giao diện Front-end (HTML/PHP)
├── composer.json           # 📦 Quản lý thư viện
└── index.php               # 🚦 Điểm khởi chạy (Front Controller)
```

---

## 📦 Cài đặt & Thiết lập

### Yêu cầu tiên quyết
1.  **Web Server**: XAMPP, WAMP, hoặc MAMP đã được cài đặt.
2.  **Phiên bản PHP**: Khuyến nghị 7.4 trở lên (tối thiểu 5.6).
3.  **Composer**: Đã cài đặt toàn cục.

### Bước 1: Clone dự án
Di chuyển vào thư mục gốc của web server (ví dụ: `htdocs` hoặc `www`).
```bash
cd C:/xampp/htdocs
git clone https://github.com/yourusername/book_store.git
```

### Bước 2: Cài đặt thư viện
Cài đặt các gói PHP cần thiết được định nghĩa trong `composer.json`.
```bash
cd book_store
composer install
```

### Bước 3: Cài đặt Cơ sở dữ liệu
1.  Khởi động **Apache** và **MySQL** trong XAMPP.
2.  Mở [phpMyAdmin](http://localhost/phpmyadmin).
3.  Tạo cơ sở dữ liệu mới tên là `bookstore`.
4.  Chọn database `bookstore` và nhấn **Nhập (Import)**.
5.  Chọn file `db/bookstore.sql` và nhấn **Thực hiện (Go)**.
    *   *Lưu ý: Script này sẽ tạo toàn bộ bảng và thêm dữ liệu mẫu.*

### Bước 4: Triển khai (Tùy chọn)
Để đưa dự án lên mạng (deploy), chúng tôi khuyên dùng **Render** (Web Service) và **Aiven for MySQL** (Cơ sở dữ liệu).

**1. Cơ sở dữ liệu (Aiven for MySQL):**
*   Tạo dịch vụ MySQL trên [Aiven](https://aiven.io/).
*   Kết nối bằng MySQL client và import file `db/bookstore.sql`.

**2. Web Service (Render):**
*   Kết nối kho lưu trữ GitHub của bạn với [Render](https://render.com/).
*   Chọn **Docker** làm runtime.
*   Thêm các Biến môi trường (Environment Variables) sau trong Dashboard của Render:
    *   `DB_HOST`: Host MySQL Aiven của bạn (ví dụ: `mysql-xxxx.aivencloud.com`)
    *   `DB_USER`: User MySQL Aiven của bạn
    *   `DB_PASS`: Mật khẩu MySQL Aiven của bạn
    *   `DB_NAME`: `bookstore`
    *   `DB_PORT`: Port MySQL Aiven (thường là `3306`)
    *   `DB_SSL`: `true`
    *   `DB_SSL_CA_PATH`: Đường dẫn file CA (tùy chọn)
    *   `DB_SSL_CA`: Nội dung chứng chỉ CA (tùy chọn nếu không có file)
    *   `BASE_URL`: URL Render của bạn (ví dụ: `https://your-app.onrender.com/`)

---

## ⚙️ Cấu hình

### Kết nối Cơ sở dữ liệu
Nếu bạn cài đặt mật khẩu cho tài khoản root MySQL, hãy cập nhật file kết nối:

*   **Front-end:** Sửa file `Model/connect.php`
*   **Admin:** Sửa file `Admin/Model/connect.php`

```php
$servername = "localhost";
$username = "root";
$password = "MAT_KHAU_CUA_BAN"; // Cập nhật mật khẩu tại đây
$dbname = "bookstore";
```

### Cấu hình Email (SMTP)
Để kích hoạt tính năng "Quên mật khẩu":
1.  Sao chép file cấu hình mẫu:
    ```bash
    cp config/email.local.php.example config/email.local.php
    ```
2.  Chỉnh sửa `config/email.local.php` với thông tin của bạn:
    ```php
    return [
        'host' => 'smtp.gmail.com',
        'username' => 'email_cua_ban@gmail.com',
        'password' => 'mat_khau_ung_dung', // Dùng Mật khẩu ứng dụng, KHÔNG phải mật khẩu đăng nhập
        'port' => 587
    ];
    ```

---

## 🔑 Tài khoản mặc định

Hệ thống đã có sẵn người dùng để kiểm thử từ file `db/bookstore.sql`:

| Vai trò | Email / Username | Mật khẩu |
| :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin` | `admin123` |
| **Khách hàng** | `nguyenvana@gmail.com` | `password` |

*Lưu ý: Tất cả mật khẩu trong database đã được mã hóa bằng `password_hash()` (Bcrypt).*
