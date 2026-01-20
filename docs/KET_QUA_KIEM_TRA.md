# 📊 Báo Cáo Kết Quả Kiểm Tra Yêu Cầu Dự Án

Dưới đây là báo cáo chi tiết về việc đối chiếu các tính năng của dự án **BookStore** với tài liệu yêu cầu (`docs/requirements.md`).

---

## 1. Front-end (Giao diện Khách hàng)

| Tính năng yêu cầu | Trạng thái | Bằng chứng kiểm tra (Files/Logic) |
| :--- | :---: | :--- |
| **Xem danh sách sản phẩm có phân trang** | ✅ | `View/books.php` (sử dụng `Pagination` class), hiển thị dạng lưới/danh sách. |
| **Hiển thị danh mục sản phẩm với icon** | ✅ | `View/sidebar.php` (List group với icon FontAwesome). |
| **Tìm kiếm sản phẩm theo tên** | ✅ | `View/header.php` (Form tìm kiếm), `View/books.php` (Hiển thị kết quả). |
| **Lọc sản phẩm theo danh mục** | ✅ | `View/sidebar.php` (Link điều hướng `?page=books&category=...`). |
| **Xem chi tiết sản phẩm** | ✅ | `View/book_detail.php`, `Controller/BookController.php`. |
| **Thêm sản phẩm vào giỏ hàng** | ✅ | `Controller/CartController.php` (hàm `add`), `View/book_detail.php`. |
| **Cập nhật số lượng trong giỏ hàng** | ✅ | `View/cart.php` (AJAX/Form update), `Controller/CartController.php`. |
| **Xóa sản phẩm khỏi giỏ hàng** | ✅ | `View/cart.php`, `Controller/CartController.php` (hàm `remove`). |
| **Đăng ký tài khoản** | ✅ | `Controller/RegistrationController.php`, `View/register.php`. |
| **Đăng nhập/Đăng xuất** | ✅ | `Controller/LoginController.php`, `View/login.php`. |
| **Quên mật khẩu (gửi email reset)** | ✅ | `Controller/ForgetController.php` (sử dụng PHPMailer), `View/forgot_password.php`. |
| **Xem thông tin tài khoản** | ✅ | `View/profile.php`, `Controller/ProfileController.php`. |
| **Cập nhật thông tin cá nhân** | ✅ | `View/profile.php` (Form `profile`), `Controller/ProfileController.php`. |
| **Đổi mật khẩu** | ✅ | `View/profile.php` (Form `password`), `Controller/ProfileController.php`. |
| **Xem đơn hàng của tôi** | ✅ | `View/orders.php`, `Controller/OrderController.php`. |
| **Xem chi tiết từng đơn hàng** | ✅ | `View/order_detail.php`. |
| **Đặt hàng** | ✅ | `View/checkout.php`, `Controller/OrderController.php` (Xử lý COD/VNPAY/PayOS). |

## 2. Admin Panel (Giao diện Quản trị)

| Tính năng yêu cầu | Trạng thái | Bằng chứng kiểm tra (Files/Logic) |
| :--- | :---: | :--- |
| **Dashboard thống kê** | ✅ | `Admin/View/dashboard.php` (Thống kê Doanh thu, Đơn hàng, Khách mới). |
| **Quản lý sản phẩm (CRUD)** | ✅ | `Admin/View/books/index.php`, `add.php`, `edit.php`. |
| **Upload hình ảnh sản phẩm** | ✅ | `Admin/Controller/BookController.php` (Xử lý upload file). |
| **Quản lý loại sản phẩm (CRUD)** | ✅ | `Admin/View/categories/index.php`. |
| **Quản lý đơn hàng** | ✅ | `Admin/View/orders/index.php`. |
| **Cập nhật trạng thái đơn hàng** | ✅ | `Admin/View/orders/edit.php` (Chuyển trạng thái pending -> completed...). |
| **Xem chi tiết đơn hàng** | ✅ | `Admin/View/orders/detail.php`. |
| **Quản lý khách hàng** | ✅ | `Admin/View/customers/index.php`. |
| **Thống kê sản phẩm bán chạy** | ✅ | `Admin/View/dashboard.php` (Bảng "Sách bán chạy" & Biểu đồ). |
| **Thống kê doanh thu theo tháng** | ✅ | `Admin/View/dashboard.php` (Biểu đồ vùng có bộ lọc thời gian). |

## 3. Công nghệ & Kiến trúc

*   **Ngôn ngữ**: PHP Thuần (Pure PHP) tương thích 5.6 - 8.x.
*   **Mô hình**: MVC (Tách biệt rõ ràng Model - View - Controller trong cấu trúc thư mục).
*   **Database**: MySQL/MariaDB (File `db/bookstore.sql` đầy đủ schema).
*   **Thư viện**: Tích hợp sẵn `PHPMailer`, `PayOS` qua Composer.
