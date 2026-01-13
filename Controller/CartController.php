<?php
/**
 * CartController - Quản lý giỏ hàng
 * 
 * Xử lý các thao tác liên quan đến giỏ hàng:
 * - Hiển thị giỏ hàng
 * - Thêm sách vào giỏ
 * - Cập nhật số lượng
 * - Xóa sản phẩm
 * - Xóa toàn bộ giỏ hàng
 * - Tính toán tổng tiền
 * 
 * Chiến lược lưu trữ:
 * - Khách vãng lai: Session storage (mất khi đóng trình duyệt)
 * - Khách đã đăng nhập: Database storage (bảng gio_hang, lưu trữ lâu dài)
 * - Khi đăng nhập: Merge session cart vào database cart
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/ShoppingCart.php';
require_once __DIR__ . '/../Model/Books.php';
require_once __DIR__ . '/helpers/SessionHelper.php';
require_once __DIR__ . '/helpers/Validator.php';

class CartController extends BaseController {
    private $cartModel;
    private $bookModel;
    
    /**
     * Constructor
     * 
     * @param mysqli $db_connection Kết nối database
     */
    public function __construct($db_connection) {
        parent::__construct($db_connection);
        $this->cartModel = new ShoppingCart($db_connection);
        $this->bookModel = new Books($db_connection);
        
        // Khởi động session
        SessionHelper::start();
    }
    
    /**
     * Hiển thị trang giỏ hàng
     * 
     * @return array Dữ liệu giỏ hàng để truyền cho view
     */
    public function showCart() {
        try {
            $cartItems = $this->getCartItems();
            $summary = $this->calculateCartSummary($cartItems);
            
            return [
                'cartItems' => $cartItems,
                'cartSummary' => $summary,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
            
        } catch (Exception $e) {
            error_log("Error in showCart: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Không thể tải giỏ hàng. Vui lòng thử lại.');
            return [
                'cartItems' => [],
                'cartSummary' => [
                    'subtotal' => 0,
                    'shipping' => 0,
                    'tax' => 0,
                    'total' => 0
                ]
            ];
        }
    }
    
    /**
     * Thêm sách vào giỏ hàng
     * 
     * @return void
     */
    public function addToCart() {
        try {
            // Kiểm tra phương thức request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Xác thực CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
            
            // Validate input
            $validator = new Validator();
            $bookId = $_POST['book_id'] ?? '';
            $quantity = $_POST['quantity'] ?? 1;
            
            $validator->required('book_id', $bookId, 'Vui lòng chọn sách');
            $validator->integer('book_id', $bookId, 'ID sách không hợp lệ');
            $validator->integer('quantity', $quantity, 'Số lượng không hợp lệ');
            $validator->min('quantity', $quantity, 1, 'Số lượng phải lớn hơn 0');
            
            if ($validator->hasErrors()) {
                throw new Exception($validator->getFirstError());
            }
            
            $bookId = (int)$bookId;
            $quantity = (int)$quantity;
            
            // Kiểm tra sách có tồn tại không
            $book = $this->bookModel->getBookById($bookId);
            if (!$book) {
                throw new Exception('Sách không tồn tại');
            }
            
            // Kiểm tra tình trạng sách
            if ($book['tinh_trang'] !== 'Còn hàng') {
                throw new Exception('Sách hiện không có sẵn');
            }
            
            // Kiểm tra số lượng tồn kho
            if ($book['so_luong_ton'] < $quantity) {
                throw new Exception("Chỉ còn {$book['so_luong_ton']} sản phẩm trong kho");
            }
            
            // Kiểm tra số lượng hiện tại trong giỏ
            $currentQty = $this->getItemQuantity($bookId);
            $newQuantity = $currentQty + $quantity;
            
            if ($newQuantity > $book['so_luong_ton']) {
                throw new Exception("Tổng số lượng vượt quá tồn kho ({$book['so_luong_ton']} sản phẩm)");
            }
            
            // Thêm vào giỏ hàng
            if (SessionHelper::isLoggedIn()) {
                // Người dùng đã đăng nhập - lưu vào database
                $customerId = SessionHelper::get('customer_id');
                
                if ($currentQty > 0) {
                    // Cập nhật số lượng
                    $this->cartModel->updateQuantity($customerId, $bookId, $newQuantity);
                } else {
                    // Thêm mới
                    $this->cartModel->addItem($customerId, $bookId, $quantity);
                }
            } else {
                // Khách vãng lai - lưu vào session
                $this->addToSessionCart($bookId, $quantity);
            }
            
            // Trả về kết quả
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã thêm vào giỏ hàng',
                    'data' => [
                        'item_count' => $this->getCartItemCount()
                    ]
                ]);
                exit;
            } else {
                SessionHelper::setFlash('success', 'Đã thêm sách vào giỏ hàng');
                header('Location: index.php?page=cart');
                exit;
            }
            
        } catch (Exception $e) {
            error_log("Error in addToCart: " . $e->getMessage());
            
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            } else {
                SessionHelper::setFlash('error', $e->getMessage());
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
                exit;
            }
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     * 
     * @return void
     */
    public function updateQuantity() {
        try {
            // Kiểm tra phương thức request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Xác thực CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
            
            // Validate input
            $validator = new Validator();
            $bookId = $_POST['book_id'] ?? '';
            $quantity = $_POST['quantity'] ?? 0;
            
            $validator->required('book_id', $bookId, 'Vui lòng chọn sách');
            $validator->integer('book_id', $bookId, 'ID sách không hợp lệ');
            $validator->integer('quantity', $quantity, 'Số lượng không hợp lệ');
            $validator->min('quantity', $quantity, 1, 'Số lượng phải lớn hơn 0');
            
            if ($validator->hasErrors()) {
                throw new Exception($validator->getFirstError());
            }
            
            $bookId = (int)$bookId;
            $quantity = (int)$quantity;
            
            // Kiểm tra tồn kho
            $book = $this->bookModel->getBookById($bookId);
            if (!$book) {
                throw new Exception('Sách không tồn tại');
            }
            
            if ($quantity > $book['so_luong_ton']) {
                throw new Exception("Chỉ còn {$book['so_luong_ton']} sản phẩm trong kho");
            }
            
            // Cập nhật số lượng
            if (SessionHelper::isLoggedIn()) {
                $customerId = SessionHelper::get('customer_id');
                $this->cartModel->updateQuantity($customerId, $bookId, $quantity);
            } else {
                $this->updateSessionCartQuantity($bookId, $quantity);
            }
            
            // Tính toán lại tổng tiền
            $cartItems = $this->getCartItems();
            $summary = $this->calculateCartSummary($cartItems);
            
            // Tìm thành tiền của sản phẩm vừa cập nhật
            $itemTotal = 0;
            foreach ($cartItems as $item) {
                if ($item['ma_sach'] == $bookId) {
                    $itemTotal = $item['gia'] * $item['so_luong'];
                    break;
                }
            }

            // Trả về kết quả
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã cập nhật số lượng',
                    'data' => [
                        'item_total' => $itemTotal,
                        'summary' => $summary
                    ]
                ]);
                exit;
            } else {
                SessionHelper::setFlash('success', 'Đã cập nhật số lượng');
                header('Location: index.php?page=cart');
                exit;
            }
            
        } catch (Exception $e) {
            error_log("Error in updateQuantity: " . $e->getMessage());
            
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            } else {
                SessionHelper::setFlash('error', $e->getMessage());
                header('Location: index.php?page=cart');
                exit;
            }
        }
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     * 
     * @return void
     */
    public function removeItem() {
        try {
            // Kiểm tra phương thức request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Xác thực CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
            
            // Validate input
            $validator = new Validator();
            $bookId = $_POST['book_id'] ?? '';
            
            $validator->required('book_id', $bookId, 'Vui lòng chọn sách');
            $validator->integer('book_id', $bookId, 'ID sách không hợp lệ');
            
            if ($validator->hasErrors()) {
                throw new Exception($validator->getFirstError());
            }
            
            $bookId = (int)$bookId;
            
            // Xóa sản phẩm
            if (SessionHelper::isLoggedIn()) {
                $customerId = SessionHelper::get('customer_id');
                $this->cartModel->removeItem($customerId, $bookId);
            } else {
                $this->removeFromSessionCart($bookId);
            }
            
            // Trả về kết quả
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                    'cart_count' => $this->getCartItemCount()
                ]);
                exit;
            } else {
                SessionHelper::setFlash('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
                header('Location: index.php?page=cart');
                exit;
            }
            
        } catch (Exception $e) {
            error_log("Error in removeItem: " . $e->getMessage());
            
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            } else {
                SessionHelper::setFlash('error', $e->getMessage());
                header('Location: index.php?page=cart');
                exit;
            }
        }
    }
    
    /**
     * Xóa toàn bộ giỏ hàng
     * 
     * @return void
     */
    public function clearCart() {
        try {
            // Kiểm tra phương thức request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Xác thực CSRF token
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
            
            // Xóa giỏ hàng
            if (SessionHelper::isLoggedIn()) {
                $customerId = SessionHelper::get('customer_id');
                $this->cartModel->clearCart($customerId);
            } else {
                $this->clearSessionCart();
            }
            
            // Check if AJAX request
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ giỏ hàng'
                ]);
                exit;
            } else {
                SessionHelper::setFlash('success', 'Đã xóa toàn bộ giỏ hàng');
                header('Location: index.php?page=cart');
                exit;
            }
            
        } catch (Exception $e) {
            error_log("Error in clearCart: " . $e->getMessage());
            
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Không thể xóa giỏ hàng. Vui lòng thử lại.'
                ]);
                exit;
            } else {
                SessionHelper::setFlash('error', 'Không thể xóa giỏ hàng. Vui lòng thử lại.');
                header('Location: index.php?page=cart');
                exit;
            }
        }
    }
    
    /**
     * Lấy tóm tắt giỏ hàng (AJAX endpoint)
     * 
     * @return void
     */
    public function getCartSummary() {
        try {
            $cartItems = $this->getCartItems();
            $summary = $this->calculateCartSummary($cartItems);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'item_count' => $this->getCartItemCount()
                ]
            ]);
            exit;
            
        } catch (Exception $e) {
            error_log("Error in getCartSummary: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Không thể tải thông tin giỏ hàng',
                'data' => [
                    'item_count' => 0
                ]
            ]);
            exit;
        }
    }
    
    /**
     * Merge giỏ hàng session vào database khi đăng nhập
     * 
     * @param int $customerId ID khách hàng
     * @return bool
     */
    public function mergeSessionCartToDatabase($customerId) {
        try {
            $sessionCart = SessionHelper::get('cart', []);
            
            if (empty($sessionCart)) {
                return true;
            }
            
            foreach ($sessionCart as $bookId => $quantity) {
                $bookId = (int)$bookId;
                $quantity = (int)$quantity;

                if ($bookId <= 0 || $quantity <= 0) {
                    continue;
                }

                // Kiểm tra tồn kho/tình trạng sách
                $book = $this->bookModel->getBookById($bookId);
                if (!$book) {
                    continue;
                }

                $stock = (int)($book['so_luong_ton'] ?? 0);
                if (($book['tinh_trang'] ?? '') !== 'Còn hàng' || $stock <= 0) {
                    continue;
                }

                // Clamp số lượng merge theo tồn kho
                $quantityToMerge = min($quantity, $stock);
                if ($quantityToMerge <= 0) {
                    continue;
                }

                // Kiểm tra xem sản phẩm đã có trong giỏ database chưa
                $existingItem = $this->cartModel->getItem($customerId, $bookId);
                
                if ($existingItem) {
                    $currentQuantity = (int)($existingItem['so_luong'] ?? 0);
                    $newQuantity = min($currentQuantity + $quantityToMerge, $stock);
                    $this->cartModel->updateQuantity($customerId, $bookId, $newQuantity);
                } else {
                    $this->cartModel->addItem($customerId, $bookId, $quantityToMerge);
                }
            }
            
            // Xóa session cart sau khi merge
            $this->clearSessionCart();
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error in mergeSessionCartToDatabase: " . $e->getMessage());
            return false;
        }
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
    /**
     * Lấy danh sách sản phẩm trong giỏ hàng
     * 
     * @return array
     */
    private function getCartItems() {
        if (SessionHelper::isLoggedIn()) {
            // Lấy từ database
            $customerId = SessionHelper::get('customer_id');
            return $this->cartModel->getCartItems($customerId);
        } else {
            // Lấy từ session
            return $this->getSessionCartItems();
        }
    }
    
    /**
     * Tính toán tóm tắt giỏ hàng (subtotal, shipping, tax, total)
     * 
     * @param array $items Danh sách sản phẩm
     * @return array
     */
    private function calculateCartSummary($items) {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $subtotal += $item['gia'] * $item['so_luong'];
        }
        
        // Phí vận chuyển
        $shipping = 0;
        if ($subtotal > 0 && $subtotal < 200000) {
            $shipping = 30000; // 30k cho đơn dưới 200k
        } elseif ($subtotal >= 200000) {
            $shipping = 0; // Miễn phí ship cho đơn từ 200k
        }
        
        // Thuế VAT (10%)
        $tax = ($subtotal + $shipping) * 0.1;
        
        // Tổng cộng
        $total = $subtotal + $shipping + $tax;
        
        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'item_count' => count($items)
        ];
    }
    
    /**
     * Đếm số lượng sản phẩm trong giỏ
     * 
     * @return int
     */
    private function getCartItemCount() {
        if (SessionHelper::isLoggedIn()) {
            $customerId = SessionHelper::get('customer_id');
            return $this->cartModel->getItemCount($customerId);
        } else {
            $sessionCart = SessionHelper::get('cart', []);
            return count($sessionCart);
        }
    }
    
    /**
     * Lấy số lượng của một sản phẩm trong giỏ
     * 
     * @param int $bookId
     * @return int
     */
    private function getItemQuantity($bookId) {
        if (SessionHelper::isLoggedIn()) {
            $customerId = SessionHelper::get('customer_id');
            $item = $this->cartModel->getItem($customerId, $bookId);
            return $item ? $item['so_luong'] : 0;
        } else {
            $sessionCart = SessionHelper::get('cart', []);
            return $sessionCart[$bookId] ?? 0;
        }
    }
    
    // ========== SESSION CART METHODS (Guest Users) ==========
    
    /**
     * Thêm sản phẩm vào session cart
     * 
     * @param int $bookId
     * @param int $quantity
     * @return void
     */
    private function addToSessionCart($bookId, $quantity) {
        $cart = SessionHelper::get('cart', []);
        
        if (isset($cart[$bookId])) {
            $cart[$bookId] += $quantity;
        } else {
            $cart[$bookId] = $quantity;
        }
        
        SessionHelper::set('cart', $cart);
    }
    
    /**
     * Cập nhật số lượng trong session cart
     * 
     * @param int $bookId
     * @param int $quantity
     * @return void
     */
    private function updateSessionCartQuantity($bookId, $quantity) {
        $cart = SessionHelper::get('cart', []);
        
        if (isset($cart[$bookId])) {
            $cart[$bookId] = $quantity;
            SessionHelper::set('cart', $cart);
        }
    }
    
    /**
     * Xóa sản phẩm khỏi session cart
     * 
     * @param int $bookId
     * @return void
     */
    private function removeFromSessionCart($bookId) {
        $cart = SessionHelper::get('cart', []);
        
        if (isset($cart[$bookId])) {
            unset($cart[$bookId]);
            SessionHelper::set('cart', $cart);
        }
    }
    
    /**
     * Xóa toàn bộ session cart
     * 
     * @return void
     */
    private function clearSessionCart() {
        SessionHelper::remove('cart');
    }
    
    /**
     * Lấy danh sách sản phẩm từ session cart với thông tin đầy đủ
     * 
     * @return array
     */
    private function getSessionCartItems() {
        $cart = SessionHelper::get('cart', []);
        $items = [];
        
        foreach ($cart as $bookId => $quantity) {
            $book = $this->bookModel->getBookById($bookId);
            
            if ($book) {
                $items[] = [
                    'ma_sach' => $book['ma_sach'],
                    'ten_sach' => $book['ten_sach'],
                    'anh_bia' => $book['anh_bia'],
                    'gia' => $book['gia'],
                    'so_luong' => $quantity,
                    'so_luong_ton' => $book['so_luong_ton'],
                    'tinh_trang' => $book['tinh_trang'],
                    'ten_tac_gia' => $book['ten_tac_gia'] ?? '',
                    'isbn' => $book['isbn'] ?? ''
                ];
            }
        }
        
        return $items;
    }
}
