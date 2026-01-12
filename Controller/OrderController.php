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
        $this->emailSender = new EmailSender();
        
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
        $validator = new Validator();
        
        // Thông tin giao hàng
        $validator->required('recipient_name', $_POST['recipient_name'] ?? '', 'Vui lòng nhập tên người nhận');
        $validator->minLength('recipient_name', $_POST['recipient_name'] ?? '', 3, 'Tên người nhận phải có ít nhất 3 ký tự');
        
        $validator->required('phone', $_POST['phone'] ?? '', 'Vui lòng nhập số điện thoại');
        $validator->phone('phone', $_POST['phone'] ?? '', 'Số điện thoại không hợp lệ');
        
        $validator->required('address', $_POST['address'] ?? '', 'Vui lòng nhập địa chỉ giao hàng');
        $validator->minLength('address', $_POST['address'] ?? '', 10, 'Địa chỉ phải có ít nhất 10 ký tự');
        
        $validator->required('city', $_POST['city'] ?? '', 'Vui lòng chọn tỉnh/thành phố');
        $validator->required('district', $_POST['district'] ?? '', 'Vui lòng chọn quận/huyện');
        
        // Phương thức thanh toán
        $validator->required('payment_method', $_POST['payment_method'] ?? '', 'Vui lòng chọn phương thức thanh toán');
        $paymentMethods = array_keys($this->getPaymentMethods());
        $validator->inArray('payment_method', $_POST['payment_method'] ?? '', $paymentMethods, 'Phương thức thanh toán không hợp lệ');
        
        return !$validator->hasErrors();
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
            if (!$this->validateCheckout()) {
                $validator = new Validator();
                throw new Exception($validator->getFirstError());
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
            $this->sendOrderConfirmation($orderId);

            SessionHelper::set('last_order_id', $orderId);
            SessionHelper::set('last_order_code', $orderNumber);

            SessionHelper::setFlash('success', 'Đặt hàng thành công!');
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
            if (!$this->validateCheckout()) {
                $validator = new Validator();
                throw new Exception($validator->getFirstError());
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
            // Lấy thông tin đơn hàng
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            // Lấy thông tin khách hàng
            $customerInfo = $this->getCustomerInfo($order['ma_khach_hang']);
            
            // Lấy chi tiết đơn hàng
            $orderItems = $this->orderModel->getOrderItems($orderId);
            
            // Tạo nội dung email
            $subject = "Xác nhận đơn hàng #{$order['ma_don_hang']}";
            $body = $this->buildOrderConfirmationEmail($order, $orderItems, $customerInfo);
            
            // Gửi email
            return $this->emailSender->sendEmail(
                $customerInfo['email'],
                $customerInfo['ho_ten'],
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
        $query = "SELECT * FROM khachhang WHERE id_khachhang = ? LIMIT 1";
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
        $html = "
        <h2>Xin chào {$customerInfo['ho_ten']},</h2>
        <p>Cảm ơn bạn đã đặt hàng tại BookStore!</p>
        
        <h3>Thông tin đơn hàng</h3>
        <p><strong>Mã đơn hàng:</strong> {$order['ma_don_hang']}</p>
        <p><strong>Ngày đặt:</strong> {$order['ngay_dat']}</p>
        <p><strong>Trạng thái:</strong> {$order['trang_thai_don_hang']}</p>
        
        <h3>Thông tin giao hàng</h3>
        <p><strong>Người nhận:</strong> {$order['ten_nguoi_nhan']}</p>
        <p><strong>Số điện thoại:</strong> {$order['sdt_nguoi_nhan']}</p>
        <p><strong>Địa chỉ:</strong> {$order['dia_chi_giao_hang']}, {$order['phuong_xa']}, {$order['quan_huyen']}, {$order['thanh_pho']}</p>
        
        <h3>Chi tiết đơn hàng</h3>
        <table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        ";
        
        foreach ($orderItems as $item) {
            $html .= "
            <tr>
                <td>{$item['ten_sach']}</td>
                <td>{$item['so_luong']}</td>
                <td>" . number_format($item['gia']) . " đ</td>
                <td>" . number_format($item['thanh_tien']) . " đ</td>
            </tr>
            ";
        }
        
        $html .= "
            <tr>
                <td colspan='3' align='right'><strong>Tạm tính:</strong></td>
                <td>" . number_format($order['tong_tien']) . " đ</td>
            </tr>
            <tr>
                <td colspan='3' align='right'><strong>Phí vận chuyển:</strong></td>
                <td>" . number_format($order['phi_van_chuyen']) . " đ</td>
            </tr>
            <tr>
                <td colspan='3' align='right'><strong>Thuế VAT:</strong></td>
                <td>" . number_format($order['thue']) . " đ</td>
            </tr>
            <tr>
                <td colspan='3' align='right'><strong>Tổng cộng:</strong></td>
                <td><strong>" . number_format($order['tong_thanh_toan']) . " đ</strong></td>
            </tr>
        </table>
        
        <p>Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng.</p>
        <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi.</p>
        
        <p>Trân trọng,<br>BookStore Team</p>
        ";
        
        return $html;
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
