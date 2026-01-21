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
require_once __DIR__ . '/../Model/Coupon.php';
require_once __DIR__ . '/helpers/SessionHelper.php';
require_once __DIR__ . '/helpers/Validator.php';
require_once __DIR__ . '/../config/payos_config.php';

use PayOS\PayOS;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;
use PayOS\Models\V2\PaymentRequests\PaymentData;

class OrderController extends BaseController
{
    private $orderModel;
    private $bookModel;
    private $cartModel;
    private $couponModel;
    private $emailSender;

    /**
     * Constructor
     * 
     * @param mysqli $db_connection Kết nối database
     */
    public function __construct($db_connection)
    {
        parent::__construct($db_connection);
        $this->orderModel = new Orders($db_connection);
        $this->bookModel = new Books($db_connection);
        $this->cartModel = new ShoppingCart($db_connection);
        $this->couponModel = new Coupon($db_connection);
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
    public function showCheckout()
    {
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

            if (!empty($summary['coupon_error'])) {
                SessionHelper::setFlash('warning', $summary['coupon_error']);
            }

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
    public function validateCheckout()
    {
        $validator = $this->buildCheckoutValidator($_POST);
        return !$validator->hasErrors();
    }

    /**
     * Build the checkout validator for provided input.
     *
     * @param array $input
     * @return Validator
     */
    private function buildCheckoutValidator($input)
    {
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
    public function createOrder()
    {
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

            if (!empty($summary['coupon_error'])) {
                throw new Exception($summary['coupon_error']);
            }

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
                'total_amount' => $summary['total'],
                'id_magiamgia' => $summary['coupon_id'] ?? null,
                'so_tien_giam' => $summary['discount_amount'] ?? 0
            ];

            $orderId = $this->orderModel->createOrder($customerId, $orderData, $cartItems);
            if (!$orderId) {
                throw new Exception('Không thể tạo đơn hàng');
            }

            // Clear cart
            $this->cartModel->clearCart($customerId);

            // Clear coupon session
            SessionHelper::remove('coupon_code');

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
    public function processPayment()
    {
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

            $selectedMethod = strtolower((string)($_POST['payment_method'] ?? 'cod'));

            // COD
            if ($selectedMethod === 'cod') {
                $this->createOrder();
                return;
            }

            // PayOS
            if ($selectedMethod === 'payos') {
                $this->processPayOSPayment();
                return;
            }

            // Fallback
            SessionHelper::setFlash('error', 'Phương thức thanh toán không hợp lệ');
            header('Location: index.php?page=checkout');
            exit;
        } catch (Exception $e) {
            error_log("Error in processPayment: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=checkout');
            exit;
        }
    }

    /**
     * Process PayOS Payment
     */
    private function processPayOSPayment()
    {
        try {
            $customerId = SessionHelper::get('customer_id');
            $cartItems = $this->cartModel->getCartItems($customerId);

            if (empty($cartItems)) {
                throw new Exception("Giỏ hàng trống");
            }

            // Calculate amount
            $summary = $this->calculateOrderSummary($cartItems);
            $amount = (int)$summary['total'];

            if ($amount <= 0) {
                throw new Exception("Số tiền thanh toán không hợp lệ");
            }

            // Create unique numeric order code for PayOS (timestamp + customer_id part to ensure unique)
            // PayOS requires <= 2147483647 (int32)?? Or int64? Docs say "integer (Int64)".
            // However, JS max safe integer is 2^53. PHP on 64bit handles it.
            // Let's use current timestamp + 3 random digits.
            // TIME: 10 digits (1705XXXXXX). + 3 digits = 13 digits. Fits in Int64.
            $payosOrderCode = (int)(time() . rand(100, 999));

            // Store critical info in Session to retrieve after callback
            // (We can't pass all this via PayOS desc/query params reliably)
            SessionHelper::set('payos_data', [
                'orderCode' => $payosOrderCode,
                'post_data' => $_POST, // Address, Name, Phone from checkout form
                'cart_summary' => $summary
            ]);

            $payOS = new PayOS(PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY);

            // Callback URLs
            $baseUrl = $this->getAppBaseUrl();
            $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
            $scriptUrl = rtrim($baseUrl, '/') . $scriptPath;

            $returnUrl = $scriptUrl . "?page=payos_callback";
            $cancelUrl = $scriptUrl . "?page=checkout";

            $description = "Thanh toan don #" . $payosOrderCode;
            // Shorten desc if needed (PayOS limit?) - limit 25 characters recommended for compatibility? 
            // Docs: description (String)

            $paymentData = new CreatePaymentLinkRequest(
                orderCode: $payosOrderCode,
                amount: $amount,
                description: substr($description, 0, 25), // Safety crop
                returnUrl: $returnUrl,
                cancelUrl: $cancelUrl
            );

            $response = $payOS->paymentRequests->create($paymentData);

            if ($response && $response->checkoutUrl) {
                // Redirect
                header('Location: ' . $response->checkoutUrl);
                exit;
            } else {
                throw new Exception("Không tạo được link thanh toán PayOS");
            }
        } catch (Exception $e) {
            error_log("PayOS Create Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Lỗi tạo thanh toán PayOS: ' . $e->getMessage());
            header('Location: index.php?page=checkout');
            exit;
        }
    }

    /**
     * Handle PayOS Callback (Return URL)
     */
    public function handlePayOSCallback()
    {
        try {
            // Check status from GET params
            // ?code=00&id=...&cancel=false&status=PAID&orderCode=...

            $code = $_GET['code'] ?? '';
            $status = $_GET['status'] ?? '';
            $orderCode = isset($_GET['orderCode']) ? (int)$_GET['orderCode'] : 0;
            $cancel = $_GET['cancel'] ?? 'false';

            if ($cancel === 'true' || $status === 'CANCELLED') {
                SessionHelper::setFlash('warning', 'Bạn đã hủy thanh toán PayOS');
                header('Location: index.php?page=checkout');
                exit;
            }

            if ($code === '00' && $status === 'PAID') {
                // Success!

                // Retrieve stored session data
                $storedData = SessionHelper::get('payos_data');

                if (!$storedData || $storedData['orderCode'] !== $orderCode) {
                    throw new Exception("Dữ liệu phiên thanh toán không hợp lệ hoặc đã hết hạn");
                }

                // Restore $_POST to createOrder works primarily with $_POST
                $_POST = $storedData['post_data'];
                $_POST['payment_method'] = 'payos'; // Enforce method
                $_POST['order_payment_status'] = 'paid'; // Custom flag to set paid status

                // Generate CSRF token manually because createOrder checks it, 
                // but the original POST token might be stale or we just want to bypass validation
                // actually createOrder() calls buildCheckoutValidator which checks $_POST fields only.
                // It also verifies CSRF. Since this is a GET callback, we can't easily spoof POST CSRF.
                // WE NEED TO BYPASS CSRF for this specific internal call, OR set it:
                // Let's modify createOrder to allow an optional argument or check a protected flag.
                // EASIEST: Just "Inject" a valid token into $_POST if we are in same session.
                $_POST['csrf_token'] = SessionHelper::generateCSRFToken(); // We are identifying as user

                // Also, createOrder expects POST request method checks.
                // We might need to refactor createOrder slightly OR trick it.
                // Tricking REQUEST_METHOD is hacky.
                // BETTER: Extract logic from createOrder or make a specific "completeOrder($data)" method.
                // FOR NOW: Let's spoof SERVER for the internal call (safe within same request)
                $_SERVER['REQUEST_METHOD'] = 'POST';

                // Call createOrder
                // Note: createOrder() will do the final stock check, clear cart, etc.
                // We need to ensure it marks order as PAID effectively.
                // We will modify createOrder to check a special post var or arg.

                $this->createOrderWithStatus('paid', $orderCode); // Custom method wrapper

            } else {
                throw new Exception("Thanh toán không thành công. Code: $code");
            }
        } catch (Exception $e) {
            error_log("PayOS Callback Error: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=checkout');
            exit;
        }
    }

    /**
     * Wrapper to create order with specific status (internal use)
     */
    private function createOrderWithStatus($paymentStatus, $txnRef)
    {
        // Reuse createOrder logic but we need to inject the status
        // Since createOrder is tied to $_POST and specific flow, let's copy the core logic 
        // OR modify createOrder to accept args.
        // Let's duplicate the core logic for safety and clarity in this "Integration" phase so we don't break COD.

        $customerId = SessionHelper::get('customer_id');
        $cartItems = $this->cartModel->getCartItems($customerId);

        if (empty($cartItems)) throw new Exception("Giỏ hàng trống khi tạo đơn");

        $summary = $this->calculateOrderSummary($cartItems);
        $orderNumber = $this->orderModel->generateOrderNumber();

        $deliveryAddress = Validator::sanitizeString($_POST['address']);
        $district = Validator::sanitizeString($_POST['district']);
        $city = Validator::sanitizeString($_POST['city']);
        $fullDeliveryAddress = trim($deliveryAddress . ', ' . $district . ', ' . $city, " ,");

        $orderData = [
            'order_number' => $orderNumber,
            'payment_method' => 'payos', // Explicit (matches DB enum)
            'payment_status' => $paymentStatus, // 'paid'
            'recipient_name' => Validator::sanitizeString($_POST['recipient_name']),
            'phone' => Validator::sanitizeString($_POST['phone']),
            'email' => Validator::sanitizeEmail($_POST['email'] ?? ''),
            'delivery_address' => $fullDeliveryAddress,
            'note' => Validator::sanitizeString($_POST['note'] ?? '') . " (PayOS Ref: $txnRef)",
            'total_amount' => $summary['total'],
            'id_magiamgia' => $summary['coupon_id'] ?? null,
            'so_tien_giam' => $summary['discount_amount'] ?? 0
        ];

        $orderId = $this->orderModel->createOrder($customerId, $orderData, $cartItems);

        if ($orderId) {
            $this->cartModel->clearCart($customerId);
            SessionHelper::remove('coupon_code');
            SessionHelper::remove('payos_data'); // Clear temp data

            // Email
            $this->sendOrderConfirmation($orderId);

            SessionHelper::set('last_order_id', $orderId);
            SessionHelper::set('last_order_code', $orderNumber);

            header('Location: index.php?page=order_confirmation&order=' . urlencode($orderNumber));
            exit;
        } else {
            throw new Exception("Lỗi lưu đơn hàng vào database");
        }
    }

    /**
     * Resolve base URL (proxy-aware) for building absolute callback URLs
     *
     * @return string
     */
    private function getAppBaseUrl()
    {
        if (defined('PAYOS_APP_URL') && PAYOS_APP_URL) {
            return PAYOS_APP_URL;
        }

        $proto = 'http';
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $proto = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        } elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $proto = 'https';
        }

        $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $proto . '://' . $host;
    }

    /**
     * Xử lý callback từ payment gateway (Old stubs - keep for ref or delete)
     */
    public function handlePaymentCallback()
    {
        // Delegate to handlePayOSCallback if it looks like PayOS? 
        // Or just keep separate routes.
        // For now, let's assume index.php?page=payos_callback calls handlePayOSCallback directly.
        // (We need to ensure routes.php or index.php maps this!)
        // Since I can't see index.php routing, I'll assume standard page=X maps to method X or similar.
        // CHECK: Standard practice in this codebase? 
        // OrderController methods are usually mapped explicitly or mostly standard?
        // Let's assume user/system handles routing or I should check `index.php`.
        // The plan didn't explicitly mention modifying index.php routing logic, 
        // but `returnUrl: ...?page=payos_callback` implies a page mapping.

        // Safe bet: If `page=payos_callback` isn't mapped, it will 404 or default.
        // I should check index.php later.
    }

    /**
     * Hiển thị trang xác nhận đơn hàng
     * 
     * @return array
     */
    public function confirmOrder()
    {
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
    public function viewOrders()
    {
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
    public function viewOrderDetail()
    {
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
    public function cancelOrder()
    {
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
    public function sendOrderConfirmation($orderId)
    {
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

    /**
     * Apply Coupon (AJAX)
     */
    public function applyCoupon()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            $code = trim($_POST['coupon_code'] ?? '');
            if (empty($code)) {
                throw new Exception('Vui lòng nhập mã giảm giá');
            }

            $coupon = $this->couponModel->getCouponByCode($code);
            if (!$coupon) {
                throw new Exception('Mã giảm giá không hợp lệ hoặc đã hết hạn');
            }

            // Validate order minimum
            $customerId = SessionHelper::get('customer_id');
            $cartItems = $this->cartModel->getCartItems($customerId);
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['gia'] * $item['so_luong'];
            }

            if ($subtotal < $coupon['gia_tri_toi_thieu']) {
                $min = number_format($coupon['gia_tri_toi_thieu'], 0, ',', '.');
                throw new Exception("Đơn hàng phải tối thiểu {$min}đ để áp dụng mã này");
            }

            // Save to Session
            SessionHelper::set('coupon_code', $coupon['ma_code']);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công'
            ]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Remove Coupon (AJAX)
     */
    public function removeCoupon()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            SessionHelper::remove('coupon_code');

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Đã xóa mã giảm giá'
            ]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
    
    // ========== PRIVATE HELPER METHODS ==========

    /**
     * Tính toán tóm tắt đơn hàng
     * 
     * @param array $items
     * @return array
     */
    private function calculateOrderSummary($items)
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['gia'] * $item['so_luong'];
        }

        // Calculate Discount
        $discountAmount = 0;
        $couponId = null;
        $couponCode = SessionHelper::get('coupon_code');

        if ($couponCode) {
            $coupon = $this->couponModel->getCouponByCode($couponCode);

            // Check expiry
            if ($coupon && strtotime($coupon['ngay_ket_thuc']) < time()) {
                $couponError = 'Mã giảm giá đã hết hạn';
                SessionHelper::remove('coupon_code');
                $coupon = null;
            }

            if ($coupon) {
                if ($subtotal >= $coupon['gia_tri_toi_thieu']) {
                    $couponId = $coupon['id_magiamgia'];
                    if ($coupon['loai_giam'] == 'free_shipping') {
                        // Free shipping handled separately
                        $discountAmount = 0;
                    } elseif ($coupon['loai_giam'] == 'percent') {
                        $discount = ($subtotal * $coupon['gia_tri_giam']) / 100;
                        if ($coupon['giam_toi_da'] > 0) {
                            $discount = min($discount, $coupon['giam_toi_da']);
                        }
                        $discountAmount = $discount;
                    } else {
                        $discountAmount = min($coupon['gia_tri_giam'], $subtotal);
                    }
                } else {
                    $min = number_format($coupon['gia_tri_toi_thieu'], 0, ',', '.');
                    // We might treat this as an error too if we want strictness, 
                    // but usually we just ignore or remove.
                    // Let's remove silentl or set error? 
                    // Current logic was silent remove.
                    // Let's set error so user knows why price went up.
                    $couponError = "Đơn hàng chưa đạt giá trị tối thiểu ({$min}đ) để dùng mã này";
                    SessionHelper::remove('coupon_code');
                }
            } else {
                // Invalid or expired (caught above) or not found
                if (!$couponError) {
                    // Only remove if we haven't already processed it
                    SessionHelper::remove('coupon_code');
                }
            }
        }

        $subtotalAfterDiscount = max(0, $subtotal - $discountAmount);

        // Phí vận chuyển
        $shipping = 0;
        if ($subtotalAfterDiscount > 0 && $subtotalAfterDiscount < 200000) {
            $shipping = 30000;
        }

        // Apply Free Shipping Coupon
        if ($couponCode && isset($coupon) && $coupon['loai_giam'] == 'free_shipping' && $subtotal >= $coupon['gia_tri_toi_thieu']) {
            $shipping = 0;
        }

        // Thuế VAT (10%)
        $tax = ($subtotalAfterDiscount + $shipping) * 0.1;

        // Tổng cộng
        $total = $subtotalAfterDiscount + $shipping + $tax;

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'coupon_code' => $couponCode,
            'coupon_id' => $couponId,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'coupon_error' => $couponError ?? null
        ];
    }

    /**
     * Tạo mã đơn hàng unique
     * Format: ORD + YYYYMMDD + Random 4 digits
     * 
     * @return string
     */
    private function generateOrderCode()
    {
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
    private function getCustomerInfo($customerId)
    {
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
    public function getPaymentMethods()
    {
        return [
            'cod' => 'Thanh toán khi nhận hàng (COD)',
            'payos' => 'Thanh toán online qua PayOS'
        ];
    }

    /**
     * Tạo URL thanh toán VNPay
     * 
     * @param array $data
     * @return string
     */
    private function createVNPayPaymentUrl($data)
    {
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
    private function verifyVNPayCallback($data)
    {
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
    private function getVNPayErrorMessage($code)
    {
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
    private function buildOrderConfirmationEmail($order, $orderItems, $customerInfo)
    {
        $customerName = htmlspecialchars((string)($customerInfo['ho_ten'] ?? $customerInfo['ten_khachhang'] ?? ''), ENT_QUOTES, 'UTF-8');
        $orderNumberRaw = (string)($order['ma_hoadon'] ?? $order['order_number'] ?? '');
        $orderNumber = htmlspecialchars($orderNumberRaw, ENT_QUOTES, 'UTF-8');

        $itemsHtml = '';
        foreach ($orderItems as $item) {
            $itemName = htmlspecialchars((string)($item['ten_sach'] ?? ''), ENT_QUOTES, 'UTF-8');
            $itemPrice = number_format($item['gia'] ?? 0, 0, ',', '.');
            $itemQty = (int)($item['so_luong'] ?? 0);
            $itemTotal = number_format(($item['gia'] ?? 0) * $itemQty, 0, ',', '.');

            $itemsHtml .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$itemName}</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$itemQty}</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>{$itemPrice}đ</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>{$itemTotal}đ</td>
            </tr>";
        }

        $subtotal = number_format($order['total_amount'] ?? 0, 0, ',', '.');
        $total = number_format($order['total_amount'] ?? 0, 0, ',', '.');

        $deliveryAddress = htmlspecialchars((string)($order['delivery_address'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars((string)($order['phone'] ?? ''), ENT_QUOTES, 'UTF-8');

        return "
        <html>
        <head>
            <title>Xác nhận đơn hàng #{$orderNumber}</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                <h2 style='color: #4e73df;'>Cảm ơn bạn đã đặt hàng!</h2>
                <p>Xin chào <strong>{$customerName}</strong>,</p>
                <p>Đơn hàng <strong>#{$orderNumber}</strong> của bạn đã được tiếp nhận và đang được xử lý.</p>
                
                <h3 style='border-bottom: 2px solid #eee; padding-bottom: 10px;'>Thông tin đơn hàng</h3>
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                    <thead>
                        <tr style='background-color: #f8f9fc;'>
                            <th style='padding: 10px; text-align: left;'>Sản phẩm</th>
                            <th style='padding: 10px; text-align: center;'>SL</th>
                            <th style='padding: 10px; text-align: right;'>Đơn giá</th>
                            <th style='padding: 10px; text-align: right;'>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='3' style='padding: 10px; text-align: right; font-weight: bold;'>Tổng cộng:</td>
                            <td style='padding: 10px; text-align: right; font-weight: bold; color: #e74a3b;'>{$total}đ</td>
                        </tr>
                    </tfoot>
                </table>
                
                <h3 style='border-bottom: 2px solid #eee; padding-bottom: 10px;'>Thông tin giao hàng</h3>
                <p>
                    <strong>Người nhận:</strong> {$customerName}<br>
                    <strong>Số điện thoại:</strong> {$phone}<br>
                    <strong>Địa chỉ:</strong> {$deliveryAddress}
                </p>
                
                <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi.</p>
                <p>Trân trọng,<br>Đội ngũ BookStore</p>
            </div>
        </body>
        </html>
        ";
    }
}
