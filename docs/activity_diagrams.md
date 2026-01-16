# Biểu đồ hoạt động dự án (Project Activity Diagrams)

Tài liệu này chứa các biểu đồ hoạt động cho các quy trình chính trong dự án "Bookstore".
Bạn có thể hiển thị các biểu đồ này bằng [PlantUML](https://plantuml.com/) hoặc nhập vào Draw.io (Arrange -> Insert -> Advanced -> PlantUML).

## 1. Quy trình Đăng ký Khách hàng
Dựa trên `Controller/RegistrationController.php`.

```plantuml
@startuml
title Quy trình Đăng ký Khách hàng
|#LightBlue|Khách hàng|
start
:Điền thông tin đăng ký;
:Gửi form đăng ký;

|#LightYellow|Hệ thống|
:Kiểm tra Token CSRF;
if (Token hợp lệ?) then (Không)
  :Báo lỗi (Flash Error);
  :Chuyển về trang Đăng ký;
  stop
endif

:Kiểm tra dữ liệu đầu vào
(Tên, Email, Mật khẩu...);

if (Có lỗi nhập liệu?) then (Có)
  :Báo lỗi (Flash Error);
  :Chuyển về form với dữ liệu cũ;
  stop
endif

|#LightGreen|CSDL|
:Kiểm tra Email đã tồn tại chưa;

|#LightYellow|Hệ thống|
if (Email đã tồn tại?) then (Có)
  :Báo lỗi "Email đã được sử dụng";
  :Chuyển về trang Đăng ký;
  stop
endif

:Mã hóa mật khẩu;
:Chuẩn bị dữ liệu khách hàng;

|#LightGreen|CSDL|
:Lưu khách hàng mới;
:Lưu Token xác thực Email;

|#Orange|Email Server|
:Gửi Email xác thực;

|#LightYellow|Hệ thống|
if (Gửi Email thành công?) then (Có)
  :Báo thành công;
else (Không)
  :Báo cảnh báo (Warning);
endif

:Chuyển hướng về trang Đăng nhập;
stop
@enduml
```

## 2. Quy trình Thanh toán (COD)
Dựa trên `Controller/OrderController.php`.

```plantuml
@startuml
title Quy trình Thanh toán Đơn hàng (COD)
|#LightBlue|Khách hàng|
start
:Nhấn nút "Thanh toán";

|#LightYellow|Hệ thống|
if (Đã đăng nhập?) then (Chưa)
  :Chuyển về trang Đăng nhập;
  stop
endif

|#LightGreen|CSDL|
:Lấy sản phẩm trong giỏ hàng;
:Kiểm tra tồn kho sách;

|#LightYellow|Hệ thống|
if (Giỏ trống hoặc Hết hàng?) then (Có)
  :Báo lỗi;
  :Chuyển về Giỏ hàng;
  stop
endif

:Tính tổng tiền & Giảm giá;
:Hiển thị form Thanh toán;

|#LightBlue|Khách hàng|
:Nhập thông tin giao hàng;
:Chọn phương thức thanh toán (COD);
:Xác nhận đặt hàng;

|#LightYellow|Hệ thống|
:Kiểm tra Token CSRF;
:Kiểm tra thông tin giao hàng;

if (Thông tin lỗi?) then (Có)
  :Báo lỗi;
  :Chuyển về trang Thanh toán;
  stop
endif

|#LightGreen|CSDL|
:Kiểm tra lại tồn kho lần cuối;
if (Đủ hàng?) then (Không)
  :Báo lỗi "Hết hàng";
  :Chuyển về Giỏ hàng;
  stop
endif

:Tạo đơn hàng mới;
:Trừ số lượng tồn kho;
:Xóa giỏ hàng của khách;

|#Orange|Email Server|
:Gửi Email xác nhận đơn hàng;

|#LightYellow|Hệ thống|
:Chuyển về trang "Đặt hàng thành công";
stop
@enduml
```

## 3. Quy trình Quản lý Sách (Admin)
Dựa trên `Admin/Controller/AdminBookController.php`.

```plantuml
@startuml
title Quy trình Thêm Sách Mới (Admin)
|#Pink|Admin|
start
:Truy cập trang Thêm sách;
:Nhập thông tin sách;
:Chọn ảnh bìa;
:Lưu sách;

|#LightYellow|Hệ thống|
if (Admin đã đăng nhập?) then (Chưa)
  :Chuyển về trang Đăng nhập Admin;
  stop
endif

:Kiểm tra Token CSRF;
:Kiểm tra dữ liệu đầu vào
(Tên sách, ISBN, Giá, Tồn kho...);

if (Dữ liệu lỗi?) then (Có)
  :Báo lỗi;
  :Quay lại form nhập;
  stop
endif

:Xử lý upload ảnh bìa;
if (Upload thành công?) then (Không)
  :Báo lỗi upload;
  stop
endif

|#LightGreen|CSDL|
:Lưu thông tin sách vào CSDL;

|#LightYellow|Hệ thống|
if (Lưu thành công?) then (Có)
  :Báo thành công;
  :Chuyển về danh sách sách;
else (Không)
  :Báo lỗi hệ thống;
  :Quay lại form nhập;
endif

stop
@enduml
```

## 4. Quy trình Xử lý Đơn hàng (Admin)
Dựa trên `Admin/Controller/AdminOrderController.php`.

```plantuml
@startuml
title Quy trình Cập nhật Trạng thái Đơn hàng
|#Pink|Admin|
start
:Xem chi tiết đơn hàng;
:Chọn trạng thái mới
(VD: Chờ xác nhận -> Đã xác nhận);
:Nhấn Cập nhật;

|#LightYellow|Hệ thống|
if (Admin đã đăng nhập?) then (Chưa)
  :Chuyển về trang Đăng nhập Admin;
  stop
endif

:Kiểm tra Token CSRF;

|#LightGreen|CSDL|
:Lấy trạng thái hiện tại của đơn;

|#LightYellow|Hệ thống|
:Kiểm tra quy tắc chuyển trạng thái;
note right
  Quy tắc hợp lệ:
  Chờ xác nhận -> [Đã xác nhận, Đã hủy]
  Đã xác nhận -> [Đang giao hàng, Đã hủy]
  Đang giao hàng -> [Đã giao, Đã hủy]
end note

if (Chuyển đổi hợp lệ?) then (Không)
  :Báo lỗi "Trạng thái không hợp lệ";
  :Chuyển về chi tiết đơn hàng;
  stop
endif

|#LightGreen|CSDL|
:Cập nhật trạng thái trong CSDL;

|#LightYellow|Hệ thống|
if (Cập nhật thành công?) then (Có)
  :Báo thành công;
  :Chuyển về chi tiết đơn hàng;
else (Không)
  :Báo lỗi;
endif

stop
@enduml
```
