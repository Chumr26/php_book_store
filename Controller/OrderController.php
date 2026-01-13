<?php
/**
 * OrderController - Quản lý đơn hàng
 * 
 * Xử lý các thao tác liên quan đến đơn hàng:
 * - Hiển thị trang checkout
 * - Xác thực thông tin giao hàng
 * - Tạo đơn hàng mới
 * - Xử lý thanh toán online
 * - Xác nhận đơn hàng
 * - Xem lịch sử đơn hàng
 * - Xem chi tiết đơn hàng
 * - Hủy đơn hàng
 * - Gửi email xác nhận
 * 
 * Luồng thanh toán:
 * 1. Khách vãng lai phải đăng nhập trước khi checkout
 * 2. Validate thông tin giao hàng
 * 3. Chọn phương thức thanh toán (Online Payment Gateway)
 * 4. Redirect đến cổng thanh toán
 * 5. Callback từ payment gateway
 * 6. Xác nhận thanh toán thành công -> Tạo đơn hàng
 * 7. Giảm tồn kho
 * 8. Gửi email xác nhận
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Orders.php';
require_once __DIR__ . '/../Model/Books.php';
require_once __DIR__ . '/../Model/ShoppingCart.php';
require_once __DIR__ . '/../Model/EmailSender.php';
require_once __DIR__ . '/helpers/SessionHelper.php';
require_once __DIR__ . '/helpers/Validator.php';

class OrderController extends BaseController {
    private $orderModel;
    private $bookModel;
    private $cartModel;
    private $emailSender;
    
    /**
     * Constructor
     * 
     * @param mysqli $db_connection Kết nối database
     */
    public function __construct($db_connection) {
        parent::__construct($db_connection);
        $this->orderModel = new Orders($db_connection);
        $this->bookModel = new Books($db_connection);
        $this->cartModel = new ShoppingCart($db_connection);
        try {
            $this->emailSender = new EmailSender();
        } catch (Throwable $e) {
            $this->emailSender = null;
            error_log('OrderController: EmailSender not available: ' . $e->getMessage());
        }
        
        // Khởi động session
        SessionHelper::start();
    }
    
    /**
     * Hiển thị trang checkout
     * Yêu cầu đăng nhập
     * 
     * @return array Dữ liệu checkout để truyền cho view
     */
    public function showCheckout() {
        try {
            // Kiểm tra đăng nhập
            if (!SessionHelper::isLoggedIn()) {
                SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để tiếp tục thanh toán');
                header('Location: index.php?page=login&redirect=checkout');
                exit;
            }
            
            $customerId = SessionHelper::get('customer_id');
            
            // Lấy thông tin giỏ hàng
            $cartItems = $this->cartModel->getCartItems($customerId);
            
            if (empty($cartItems)) {
                SessionHelper::setFlash('warning', 'Giỏ hàng của bạn đang trống');
                header('Location: index.php?page=cart');
                exit;
            }
            
            // Validate tồn kho trước khi checkout
            foreach ($cartItems as $item) {
                $book = $this->bookModel->getBookById($item['ma_sach']);
                
                if (!$book || $book['tinh_trang'] !== 'Còn hàng') {
                    SessionHelper::setFlash('error', "Sách '{$item['ten_sach']}' hiện không có sẵn");
                    header('Location: index.php?page=cart');
                    exit;
                }
                
                if ($item['so_luong'] > $book['so_luong_ton']) {
                    SessionHelper::setFlash('error', "Sách '{$item['ten_sach']}' chỉ còn {$book['so_luong_ton']} sản phẩm");
                    header('Location: index.php?page=cart');
                    exit;
                }
            }
            
            // Tính toán tổng tiền
            $summary = $this->calculateOrderSummary($cartItems);
            
            // Lấy thông tin khách hàng để điền sẵn form
            $customerInfo = $this->getCustomerInfo($customerId);
            
            return [
                // View/checkout.php expects these names
                'cartItems' => $cartItems,
                'cartSummary' => $summary,
                'customer_info' => $customerInfo,
                'csrf_token' => SessionHelper::generateCSRFToken(),
                'payment_methods' => $this->getPaymentMethods()
            ];
            
        } catch (Exception $e) {
            error_log("Error in showCheckout: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Không thể tải trang thanh toán. Vui lòng thử lại.');
            header('Location: index.php?page=cart');
            exit;
        }
    }
    
    /**
     * Xác thực thông tin checkout
     * 
     * @return bool
     */
    public function validateCheckout() {
        $validator = $this->buildCheckoutValidator($_POST);
        return !$validator->hasErrors();
    }

    /**
     * Build the checkout validator for provided input.
     *
     * @param array $input
     * @return Validator
     */
    private function buildCheckoutValidator($input) {
        $validator = new Validator();

        // Thông tin giao hàng
        $validator->required('recipient_name', $input['recipient_name'] ?? '', 'Vui lòng nhập tên người nhận');
        $validator->minLength('recipient_name', $input['recipient_name'] ?? '', 3, 'Tên người nhận phải có ít nhất 3 ký tự');

        $validator->required('phone', $input['phone'] ?? '', 'Vui lòng nhập số điện thoại');
        $validator->phone('phone', $input['phone'] ?? '', 'Số điện thoại không hợp lệ');

        $validator->required('email', $input['email'] ?? '', 'Vui lòng nhập email để nhận xác nhận đơn hàng');
        $validator->email('email', $input['email'] ?? '', 'Email không hợp lệ');

        $validator->required('address', $input['address'] ?? '', 'Vui lòng nhập địa chỉ giao hàng');
        $validator->minLength('address', $input['address'] ?? '', 10, 'Địa chỉ phải có ít nhất 10 ký tự');

        $validator->required('city', $input['city'] ?? '', 'Vui lòng chọn tỉnh/thành phố');
        $validator->required('district', $input['district'] ?? '', 'Vui lòng chọn quận/huyện');

        // Phương thức thanh toán
        $validator->required('payment_method', $input['payment_method'] ?? '', 'Vui lòng chọn phương thức thanh toán');
        $paymentMethods = array_keys($this->getPaymentMethods());
        $validator->inArray('payment_method', $input['payment_method'] ?? '', $paymentMethods, 'Phương thức thanh toán không hợp lệ');

        return $validator;
    }
    
    /**
     * Tạo đơn hàng mới
     * Được gọi sau khi thanh toán thành công
     * 
     * @return void
     */
    public function createOrder() {
        try {
            // Kiểm tra đăng nhập
            if (!SessionHelper::isLoggedIn()) {
                throw new Exception('Vui lòng đăng nhập');
            }
            
            // Kiểm tra phương thức request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Xác thực CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
            
            // Validate thông tin checkout
            $validator = $this->buildCheckoutValidator($_POST);
            if ($validator->hasErrors()) {
                throw new Exception($validator->getFirstError() ?? 'Thông tin thanh toán không hợp lệ');
            }
            
            $customerId = SessionHelper::get('customer_id');
            
            // Lấy giỏ hàng
            $cartItems = $this->cartModel->getCartItems($customerId);
            
            if (empty($cartItems)) {
                throw new Exception('Giỏ hàng trống');
            }
            
            // Validate tồn kho lần cuối trước khi tạo đơn
            foreach ($cartItems as $item) {
                $book = $this->bookModel->getBookById($item['ma_sach']);
                
                if (!$book || $book['so_luong_ton'] < $item['so_luong']) {
                    throw new Exception("Sách '{$item['ten_sach']}' không đủ số lượng trong kho");
                }
            }
            
            // COD only for now (future PayOS/online can hook into processPayment + callback)
            $selectedMethod = strtolower((string)($_POST['payment_method'] ?? 'cod'));
            if ($selectedMethod !== 'cod') {
                throw new Exception('Phương thức thanh toán này chưa được hỗ trợ. Vui lòng chọn COD.');
            }

            // Tính toán tổng tiền
            $summary = $this->calculateOrderSummary($cartItems);

            // Generate order number using Orders model format
            $orderNumber = $this->orderModel->generateOrderNumber();

            $deliveryAddress = Validator::sanitizeString($_POST['address']);
            $district = Validator::sanitizeString($_POST['district']);
            $city = Validator::sanitizeString($_POST['city']);
            $fullDeliveryAddress = trim($deliveryAddress . ', ' . $district . ', ' . $city, " ,");

            // Create order via model (handles items + stock + transaction)
            $orderData = [
                'order_number' => $orderNumber,
                'payment_method' => 'COD',
                'payment_status' => 'unpaid',
                'recipient_name' => Validator::sanitizeString($_POST['recipient_name']),
                'phone' => Validator::sanitizeString($_POST['phone']),
                'email' => Validator::sanitizeEmail($_POST['email'] ?? ''),
                'delivery_address' => $fullDeliveryAddress,
                'note' => Validator::sanitizeString($_POST['note'] ?? ''),
                'total_amount' => $summary['total']
            ];

            $orderId = $this->orderModel->createOrder($customerId, $orderData, $cartItems);
            if (!$orderId) {
                throw new Exception('Không thể tạo đơn hàng');
            }

            // Clear cart
            $this->cartModel->clearCart($customerId);

            // Best-effort email (do not block order success)
            $emailSent = $this->sendOrderConfirmation($orderId);
            if (!$emailSent) {
                SessionHelper::setFlash('warning', 'Đặt hàng thành công nhưng không gửi được email xác nhận. Vui lòng cấu hình Resend SMTP trong config/email.local.php.');
            }

            SessionHelper::set('last_order_id', $orderId);
            SessionHelper::set('last_order_code', $orderNumber);

            header('Location: index.php?page=order_confirmation&order=' . urlencode($orderNumber));
            exit;
            
        } catch (Exception $e) {
            error_log("Error in createOrder: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=checkout');
            exit;
        }
    }
    
    /**
     * Xử lý thanh toán online
     * Redirect đến payment gateway
     * 
     * @return void
     */
    public function processPayment() {
        try {
            // Kiểm tra đăng nhập
            if (!SessionHelper::isLoggedIn()) {
                throw new Exception('Vui lòng đăng nhập');
            }
            
            // Xác thực CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
            
            // Validate checkout
            $validator = $this->buildCheckoutValidator($_POST);
            if ($validator->hasErrors()) {
                throw new Exception($validator->getFirstError() ?? 'Thông tin thanh toán không hợp lệ');
            }
            
            // COD only: create the order immediately
            $selectedMethod = strtolower((string)($_POST['payment_method'] ?? 'cod'));
            if ($selectedMethod !== 'cod') {
                SessionHelper::setFlash('error', 'Hiện chỉ hỗ trợ COD. Cổng thanh toán online sẽ được bổ sung sau (PayOS).');
                header('Location: index.php?page=checkout');
                exit;
            }

            $this->createOrder();
            
        } catch (Exception $e) {
            error_log("Error in processPayment: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=checkout');
            exit;
        }
    }
    
    /**
     * Xử lý callback từ payment gateway
     * 
     * @return void
     */
    public function handlePaymentCallback() {
        try {
            // TODO: Verify payment signature từ gateway
            // Ví dụ với VNPay
            $vnpayData = $_GET;
            
            if (!$this->verifyVNPayCallback($vnpayData)) {
                throw new Exception('Chữ ký thanh toán không hợp lệ');
            }
            
            // Kiểm tra kết quả thanh toán
            $responseCode = $vnpayData['vnp_ResponseCode'] ?? '';
            
            if ($responseCode === '00') {
                // Thanh toán thành công
                // Lấy thông tin checkout từ session
                $checkoutData = SessionHelper::get('checkout_data');
                
                if (!$checkoutData) {
                    throw new Exception('Thông tin thanh toán không tồn tại');
                }
                
                // Set lại POST data để createOrder có thể sử dụng
                $_POST = array_merge($checkoutData, [
                    'csrf_token' => SessionHelper::generateCSRFToken()
                ]);
                
                // Tạo đơn hàng
                $this->createOrder();
                
            } else {
                // Thanh toán thất bại
                $errorMessage = $this->getVNPayErrorMessage($responseCode);
                SessionHelper::setFlash('error', 'Thanh toán thất bại: ' . $errorMessage);
                header('Location: index.php?page=checkout');
                exit;
            }
            
        } catch (Exception $e) {
            error_log("Error in handlePaymentCallback: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra trong quá trình thanh toán');
            header('Location: index.php?page=checkout');
            exit;
        }
    }
    
    /**
     * Hiển thị trang xác nhận đơn hàng
     * 
     * @return array
     */
    public function confirmOrder() {
        try {
            if (!SessionHelper::isLoggedIn()) {
                SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để xem đơn hàng');
                header('Location: index.php?page=login&redirect=orders');
                exit;
            }

            $orderCode = $_GET['order'] ?? '';
            
            if (empty($orderCode)) {
                throw new Exception('Mã đơn hàng không hợp lệ');
            }
            
            // Lấy thông tin đơn hàng (ma_hoadon)
            $order = $this->orderModel->getOrderByNumber($orderCode);
            
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            // Kiểm tra quyền xem đơn hàng
            $customerId = SessionHelper::get('customer_id');
            if ((int)($order['id_khachhang'] ?? 0) !== (int)$customerId) {
                throw new Exception('Bạn không có quyền xem đơn hàng này');
            }
            
            // Lấy chi tiết đơn hàng
            $orderItems = $this->orderModel->getOrderItems($order['id_hoadon']);
            
            return [
                'order' => $order,
                'order_items' => $orderItems
            ];
            
        } catch (Exception $e) {
            error_log("Error in confirmOrder: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php');
            exit;
        }
    }
    
    /**
     * Hiển thị lịch sử đơn hàng của khách hàng
     * 
     * @return array
     */
    public function viewOrders() {
        try {
            // Kiểm tra đăng nhập
            if (!SessionHelper::isLoggedIn()) {
                SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để xem đơn hàng');
                header('Location: index.php?page=login&redirect=orders');
                exit;
            }
            
            $customerId = SessionHelper::get('customer_id');
            
            // Lấy danh sách đơn hàng
            $orders = $this->orderModel->getOrdersByCustomer($customerId);
            
            return [
                'orders' => $orders
            ];
            
        } catch (Exception $e) {
            error_log("Error in viewOrders: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Không thể tải danh sách đơn hàng');
            return [
                'orders' => []
            ];
        }
    }
    
    /**
     * Xem chi tiết đơn hàng
     * 
     * @return array
     */
    public function viewOrderDetail() {
        try {
            // Kiểm tra đăng nhập
            if (!SessionHelper::isLoggedIn()) {
                SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để xem đơn hàng');
                header('Location: index.php?page=login');
                exit;
            }
            
            $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($orderId <= 0) {
                throw new Exception('Mã đơn hàng không hợp lệ');
            }
            
            $customerId = SessionHelper::get('customer_id');
            
            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            // Kiểm tra quyền xem
            if ((int)($order['ma_khach_hang'] ?? 0) !== (int)$customerId) {
                throw new Exception('Bạn không có quyền xem đơn hàng này');
            }
            
            // Lấy chi tiết đơn hàng
            $orderItems = $this->orderModel->getOrderItems($orderId);
            
            return [
                'order' => $order,
                'order_items' => $orderItems,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
            
        } catch (Exception $e) {
            error_log("Error in viewOrderDetail: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=orders');
            exit;
        }
    }
    
    /**
     * Hủy đơn hàng
     * Chỉ cho phép hủy khi đơn hàng ở trạng thái "Chờ xác nhận"
     * 
     * @return void
     */
    public function cancelOrder() {
        try {
            // Kiểm tra đăng nhập
            if (!SessionHelper::isLoggedIn()) {
                throw new Exception('Vui lòng đăng nhập');
            }
            
            // Kiểm tra phương thức request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Xác thực CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
            
            $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
            $customerId = SessionHelper::get('customer_id');
            
            if ($orderId <= 0) {
                throw new Exception('Mã đơn hàng không hợp lệ');
            }
            
            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            // Kiểm tra quyền hủy
            if ((int)($order['ma_khach_hang'] ?? 0) !== (int)$customerId) {
                throw new Exception('Bạn không có quyền hủy đơn hàng này');
            }
            
            // Chỉ cho phép hủy đơn ở trạng thái pending
            if (($order['status'] ?? $order['trang_thai_don_hang'] ?? '') !== 'pending') {
                throw new Exception('Không thể hủy đơn hàng ở trạng thái hiện tại');
            }

            if (!$this->orderModel->cancelOrder($orderId)) {
                throw new Exception('Không thể hủy đơn hàng. Vui lòng thử lại.');
            }

            SessionHelper::setFlash('success', 'Đã hủy đơn hàng thành công');
            header('Location: index.php?page=order_detail&id=' . $orderId);
            exit;
            
        } catch (Exception $e) {
            error_log("Error in cancelOrder: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=orders');
            exit;
        }
    }
    
    /**
     * Gửi email xác nhận đơn hàng
     * 
     * @param int $orderId
     * @return bool
     */
    public function sendOrderConfirmation($orderId) {
        try {
            if (!$this->emailSender) {
                throw new Exception('EmailSender is not configured. Please configure config/email.local.php.');
            }

            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            // Lấy thông tin khách hàng
            $customerId = $order['id_khachhang'] ?? $order['ma_khach_hang'] ?? null;
            if (empty($customerId)) {
                throw new Exception('Không xác định được khách hàng của đơn hàng');
            }
            $customerInfo = $this->getCustomerInfo((int)$customerId);
            
            // Lấy chi tiết đơn hàng
            $orderItems = $this->orderModel->getOrderItems($orderId);

            // Chọn email nhận: ưu tiên email giao hàng (nhập ở checkout), fallback sang email tài khoản
            $recipientEmail = trim((string)($order['email_giao'] ?? $order['email'] ?? ($customerInfo['email'] ?? '')));
            if (empty($recipientEmail)) {
                throw new Exception('Không có email để gửi xác nhận đơn hàng');
            }

            // Chọn tên nhận: ưu tiên người nhận, fallback tên tài khoản
            $recipientName = (string)($order['recipient_name'] ?? $order['ten_nguoi_nhan'] ?? ($customerInfo['ho_ten'] ?? $customerInfo['ten_khachhang'] ?? ''));
            $recipientName = trim($recipientName);
            
            // Tạo nội dung email
            $orderNumber = $order['ma_hoadon'] ?? $order['order_number'] ?? $order['ma_don_hang'] ?? '';
            $subject = "Xác nhận đơn hàng #{$orderNumber}";
            $body = $this->buildOrderConfirmationEmail($order, $orderItems, $customerInfo);
            
            // Gửi email
            return $this->emailSender->sendEmail(
                $recipientEmail,
                $recipientName,
                $subject,
                $body
            );
            
        } catch (Exception $e) {
            error_log("Error in sendOrderConfirmation: " . $e->getMessage());
            return false;
        }
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
    /**
     * Tính toán tóm tắt đơn hàng
     * 
     * @param array $items
     * @return array
     */
    private function calculateOrderSummary($items) {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $subtotal += $item['gia'] * $item['so_luong'];
        }
        
        // Phí vận chuyển
        $shipping = 0;
        if ($subtotal > 0 && $subtotal < 200000) {
            $shipping = 30000;
        }
        
        // Thuế VAT (10%)
        $tax = ($subtotal + $shipping) * 0.1;
        
        // Tổng cộng
        $total = $subtotal + $shipping + $tax;
        
        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total
        ];
    }
    
    /**
     * Tạo mã đơn hàng unique
     * Format: ORD + YYYYMMDD + Random 4 digits
     * 
     * @return string
     */
    private function generateOrderCode() {
        $date = date('Ymd');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return "ORD{$date}{$random}";
    }
    
    /**
     * Lấy thông tin khách hàng
     * 
     * @param int $customerId
     * @return array|null
     */
    private function getCustomerInfo($customerId) {
        $query = "SELECT *, ten_khachhang as ho_ten FROM khachhang WHERE id_khachhang = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Lấy danh sách phương thức thanh toán
     * 
     * @return array
     */
    private function getPaymentMethods() {
        return [
            // COD only for now; keep method key stable for future providers like PayOS
            'cod' => 'Thanh toán khi nhận hàng (COD)'
        ];
    }
    
    /**
     * Tạo URL thanh toán VNPay
     * 
     * @param array $data
     * @return string
     */
    private function createVNPayPaymentUrl($data) {
        // TODO: Implement VNPay integration
        // Đây là ví dụ cơ bản, cần config thực tế
        
        $vnpayConfig = [
            'vnp_TmnCode' => 'YOUR_TMN_CODE', // Mã website tại VNPay
            'vnp_HashSecret' => 'YOUR_HASH_SECRET', // Chuỗi bí mật
            'vnp_Url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'vnp_ReturnUrl' => $data['return_url']
        ];
        
        $vnpayData = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $vnpayConfig['vnp_TmnCode'],
            'vnp_Amount' => $data['amount'] * 100, // VNPay yêu cầu amount * 100
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $_SERVER['REMOTE_ADDR'],
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $data['order_desc'],
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $vnpayConfig['vnp_ReturnUrl'],
            'vnp_TxnRef' => $data['order_code']
        ];
        
        // Sắp xếp dữ liệu theo key
        ksort($vnpayData);
        
        // Tạo query string
        $query = http_build_query($vnpayData);
        
        // Tạo secure hash
        $secureHash = hash_hmac('sha512', $query, $vnpayConfig['vnp_HashSecret']);
        
        // Tạo URL thanh toán
        $paymentUrl = $vnpayConfig['vnp_Url'] . '?' . $query . '&vnp_SecureHash=' . $secureHash;
        
        return $paymentUrl;
    }
    
    /**
     * Xác thực callback từ VNPay
     * 
     * @param array $data
     * @return bool
     */
    private function verifyVNPayCallback($data) {
        // TODO: Implement VNPay signature verification
        $vnpHashSecret = 'YOUR_HASH_SECRET';
        
        $secureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash']);
        unset($data['vnp_SecureHashType']);
        
        ksort($data);
        $query = http_build_query($data);
        $calculatedHash = hash_hmac('sha512', $query, $vnpHashSecret);
        
        return $secureHash === $calculatedHash;
    }
    
    /**
     * Lấy thông báo lỗi VNPay
     * 
     * @param string $code
     * @return string
     */
    private function getVNPayErrorMessage($code) {
        $messages = [
            '07' => 'Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường)',
            '09' => 'Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking',
            '10' => 'Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Đã hết hạn chờ thanh toán',
            '12' => 'Thẻ/Tài khoản của khách hàng bị khóa',
            '13' => 'Quý khách nhập sai mật khẩu xác thực giao dịch (OTP)',
            '24' => 'Khách hàng hủy giao dịch',
            '51' => 'Tài khoản của quý khách không đủ số dư để thực hiện giao dịch',
            '65' => 'Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng thanh toán đang bảo trì',
            '79' => 'KH nhập sai mật khẩu thanh toán quá số lần quy định'
        ];
        
        return $messages[$code] ?? 'Giao dịch thất bại';
    }
    
    /**
     * Xây dựng nội dung email xác nhận đơn hàng
     * 
     * @param array $order
     * @param array $orderItems
     * @param array $customerInfo
     * @return string
     */
    private function buildOrderConfirmationEmail($order, $orderItems, $customerInfo) {
                $customerName = htmlspecialchars((string)($customerInfo['ho_ten'] ?? $customerInfo['ten_khachhang'] ?? ''), ENT_QUOTES, 'UTF-8');
                $orderNumberRaw = (string)($order['ma_hoadon'] ?? $order['order_number'] ?? '');
                $orderNumber = htmlspecialchars($orderNumberRaw, ENT_QUOTES, 'UTF-8');

                $orderDateRaw = (string)($order['ngay_dat_hang'] ?? $order['order_date'] ?? '');
                $orderDate = $orderDateRaw ? date('d/m/Y H:i', strtotime($orderDateRaw)) : '';
                $orderDate = htmlspecialchars($orderDate, ENT_QUOTES, 'UTF-8');

                $statusCode = (string)($order['trang_thai'] ?? $order['status'] ?? '');
                $statusLabels = [
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'shipping' => 'Đang giao hàng',
                        'completed' => 'Đã giao',
                        'cancelled' => 'Đã hủy'
                ];
                $status = htmlspecialchars($statusLabels[$statusCode] ?? $statusCode, ENT_QUOTES, 'UTF-8');

                $paymentCode = (string)($order['trang_thai_thanh_toan'] ?? $order['payment_status'] ?? '');
                $paymentLabels = [
                        'unpaid' => 'Chờ thanh toán',
                        'paid' => 'Đã thanh toán'
                ];
                $paymentStatus = htmlspecialchars($paymentLabels[$paymentCode] ?? $paymentCode, ENT_QUOTES, 'UTF-8');

                $paymentMethod = htmlspecialchars((string)($order['phuong_thuc_thanh_toan'] ?? $order['payment_method'] ?? ''), ENT_QUOTES, 'UTF-8');

                $recipientName = htmlspecialchars((string)($order['ten_nguoi_nhan'] ?? $order['recipient_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                $recipientPhone = htmlspecialchars((string)($order['sdt_giao'] ?? $order['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
                $deliveryAddress = htmlspecialchars((string)($order['dia_chi_giao'] ?? $order['delivery_address'] ?? ''), ENT_QUOTES, 'UTF-8');

                $total = (float)($order['tong_tien'] ?? $order['total_amount'] ?? 0);
                $totalFormatted = number_format($total, 0, ',', '.') . ' đ';

                $itemsRows = '';
                foreach (($orderItems ?? []) as $item) {
                        $title = htmlspecialchars((string)($item['ten_sach'] ?? $item['title'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $qty = (int)($item['so_luong'] ?? $item['quantity'] ?? 0);
                        $price = (float)($item['gia'] ?? $item['price'] ?? 0);
                        $lineTotal = (float)($item['thanh_tien'] ?? ($qty * $price));

                        $itemsRows .= "
                                <tr>
                                        <td style=\"padding:12px; border-bottom:1px solid #E5E7EB;\">{$title}</td>
                                        <td style=\"padding:12px; border-bottom:1px solid #E5E7EB; text-align:center;\">{$qty}</td>
                                        <td style=\"padding:12px; border-bottom:1px solid #E5E7EB; text-align:right; white-space:nowrap;\">" . number_format($price, 0, ',', '.') . " đ</td>
                                        <td style=\"padding:12px; border-bottom:1px solid #E5E7EB; text-align:right; white-space:nowrap;\">" . number_format($lineTotal, 0, ',', '.') . " đ</td>
                                </tr>
                        ";
                }

                if ($itemsRows === '') {
                        $itemsRows = "
                                <tr>
                                        <td colspan=\"4\" style=\"padding:12px; border-bottom:1px solid #E5E7EB; color:#6B7280; text-align:center;\">Không có sản phẩm</td>
                                </tr>
                        ";
                }

                // Use only inline CSS for best email client compatibility
                return "
<!doctype html>
<html lang=\"vi\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>Xác nhận đơn hàng</title>
</head>
<body style=\"margin:0; padding:0; background:#F3F4F6;\">
    <div style=\"display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;\">
        BookStore xác nhận đơn hàng #{$orderNumber}
    </div>

    <table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background:#F3F4F6; padding:24px 0;\">
        <tr>
            <td align=\"center\">
                <table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\" style=\"width:600px; max-width:600px; background:#FFFFFF; border-radius:12px; overflow:hidden;\">
                    <tr>
                        <td style=\"padding:20px 24px; background:#111827;\">
                            <div style=\"font-family:Arial, sans-serif; color:#FFFFFF; font-size:18px; font-weight:bold;\">BookStore</div>
                            <div style=\"font-family:Arial, sans-serif; color:#D1D5DB; font-size:13px; margin-top:4px;\">Xác nhận đơn hàng</div>
                        </td>
                    </tr>

                    <tr>
                        <td style=\"padding:24px; font-family:Arial, sans-serif; color:#111827;\">
                            <div style=\"font-size:16px; line-height:24px;\">Xin chào <strong>{$customerName}</strong>,</div>
                            <div style=\"margin-top:8px; font-size:14px; line-height:22px; color:#374151;\">Cảm ơn bạn đã đặt hàng tại BookStore. Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý sớm nhất.</div>

                            <div style=\"margin-top:20px; padding:16px; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:10px;\">
                                <div style=\"font-size:14px; font-weight:bold; margin-bottom:10px;\">Thông tin đơn hàng</div>
                                <table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"font-size:14px; color:#111827;\">
                                    <tr>
                                        <td style=\"padding:4px 0; color:#6B7280; width:42%;\">Mã đơn hàng</td>
                                        <td style=\"padding:4px 0; font-weight:bold;\">#{$orderNumber}</td>
                                    </tr>
                                    <tr>
                                        <td style=\"padding:4px 0; color:#6B7280;\">Ngày đặt</td>
                                        <td style=\"padding:4px 0;\">{$orderDate}</td>
                                    </tr>
                                    <tr>
                                        <td style=\"padding:4px 0; color:#6B7280;\">Trạng thái</td>
                                        <td style=\"padding:4px 0;\">{$status}</td>
                                    </tr>
                                    <tr>
                                        <td style=\"padding:4px 0; color:#6B7280;\">Thanh toán</td>
                                        <td style=\"padding:4px 0;\">{$paymentStatus}</td>
                                    </tr>
                                    <tr>
                                        <td style=\"padding:4px 0; color:#6B7280;\">Phương thức</td>
                                        <td style=\"padding:4px 0;\">{$paymentMethod}</td>
                                    </tr>
                                </table>
                            </div>

                            <div style=\"margin-top:16px; padding:16px; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:10px;\">
                                <div style=\"font-size:14px; font-weight:bold; margin-bottom:10px;\">Thông tin giao hàng</div>
                                <table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"font-size:14px; color:#111827;\">
                                    <tr>
                                        <td style=\"padding:4px 0; color:#6B7280; width:42%;\">Người nhận</td>
                                        <td style=\"padding:4px 0;\">{$recipientName}</td>
                                    </tr>
                                    <tr>
                                        <td style=\"padding:4px 0; color:#6B7280;\">Số điện thoại</td>
                                        <td style=\"padding:4px 0;\">{$recipientPhone}</td>
                                    </tr>
                                    <tr>
                                        <td style=\"padding:4px 0; color:#6B7280; vertical-align:top;\">Địa chỉ</td>
                                        <td style=\"padding:4px 0;\">{$deliveryAddress}</td>
                                    </tr>
                                </table>
                            </div>

                            <div style=\"margin-top:22px; font-size:14px; font-weight:bold;\">Chi tiết sản phẩm</div>
                            <table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"margin-top:10px; border:1px solid #E5E7EB; border-radius:10px; border-collapse:separate; border-spacing:0; overflow:hidden;\">
                                <tr style=\"background:#F9FAFB;\">
                                    <th align=\"left\" style=\"padding:12px; font-family:Arial, sans-serif; font-size:12px; text-transform:uppercase; letter-spacing:.02em; color:#6B7280; border-bottom:1px solid #E5E7EB;\">Sản phẩm</th>
                                    <th align=\"center\" style=\"padding:12px; font-family:Arial, sans-serif; font-size:12px; text-transform:uppercase; letter-spacing:.02em; color:#6B7280; border-bottom:1px solid #E5E7EB;\">SL</th>
                                    <th align=\"right\" style=\"padding:12px; font-family:Arial, sans-serif; font-size:12px; text-transform:uppercase; letter-spacing:.02em; color:#6B7280; border-bottom:1px solid #E5E7EB;\">Đơn giá</th>
                                    <th align=\"right\" style=\"padding:12px; font-family:Arial, sans-serif; font-size:12px; text-transform:uppercase; letter-spacing:.02em; color:#6B7280; border-bottom:1px solid #E5E7EB;\">Thành tiền</th>
                                </tr>
                                {$itemsRows}
                            </table>

                            <table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"margin-top:14px;\">
                                <tr>
                                    <td style=\"font-family:Arial, sans-serif; font-size:14px; color:#6B7280;\">Tổng cộng</td>
                                    <td align=\"right\" style=\"font-family:Arial, sans-serif; font-size:18px; font-weight:bold; color:#111827; white-space:nowrap;\">{$totalFormatted}</td>
                                </tr>
                                <tr>
                                    <td colspan=\"2\" style=\"padding-top:6px; font-family:Arial, sans-serif; font-size:12px; color:#6B7280;\">(Tổng tiền có thể đã bao gồm phí vận chuyển/VAT theo cấu hình hệ thống.)</td>
                                </tr>
                            </table>

                            <div style=\"margin-top:18px; font-family:Arial, sans-serif; font-size:13px; line-height:20px; color:#374151;\">
                                Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng.
                            </div>

                            <div style=\"margin-top:18px; font-family:Arial, sans-serif; font-size:13px; line-height:20px; color:#6B7280;\">
                                Trân trọng,<br>
                                <strong style=\"color:#111827;\">BookStore Team</strong>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style=\"padding:16px 24px; background:#F9FAFB; border-top:1px solid #E5E7EB;\">
                            <div style=\"font-family:Arial, sans-serif; font-size:12px; color:#6B7280; line-height:18px;\">
                                Email này được gửi tự động để xác nhận đơn hàng. Nếu bạn không thực hiện giao dịch này, vui lòng liên hệ hỗ trợ.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
";
    }
    
    /**
     * Lấy base URL của website
     * 
     * @return string
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        return $protocol . "://" . $host . $scriptName;
    }
}
