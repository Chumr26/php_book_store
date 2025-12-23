<?php
$pageTitle = "Giỏ hàng";

require_once __DIR__ . '/helpers/cover.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>
    
    <?php if (isset($cartItems) && !empty($cartItems)): ?>
    <div class="row">
        <!-- Cart Items -->
        <div class="col-md-8">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th width="15%">Đơn giá</th>
                            <th width="15%">Số lượng</th>
                            <th width="15%">Thành tiền</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                        <tr data-cart-item-id="<?php echo $item['ma_sach']; ?>">
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php $coverUrl = book_cover_url($item['isbn'] ?? null, 'small'); ?>
                                    <img src="<?php echo htmlspecialchars($coverUrl); ?>" 
                                 loading="lazy" decoding="async"
                                         style="width: 70px; height: 100px; object-fit: cover;" class="mr-3">
                                    <div>
                                        <h6><?php echo htmlspecialchars($item['ten_sach']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['ten_tac_gia']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo number_format($item['gia'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <input type="number" 
                                       class="form-control quantity-input" 
                                       value="<?php echo $item['so_luong']; ?>" 
                                       min="1" 
                                       max="<?php echo $item['so_luong_ton']; ?>"
                                       data-book-id="<?php echo $item['ma_sach']; ?>">
                            </td>
                            <td class="item-total"><?php echo number_format($item['gia'] * $item['so_luong'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <button class="btn btn-sm btn-danger remove-item" data-book-id="<?php echo $item['ma_sach']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <a href="?page=books" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
                <button type="button" class="btn btn-outline-danger" id="clearCart">
                    <i class="fas fa-trash-alt"></i> Xóa giỏ hàng
                </button>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tổng đơn hàng</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span id="subtotal"><?php echo number_format($cartSummary['subtotal'] ?? 0, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span id="shipping"><?php echo number_format($cartSummary['shipping'] ?? 0, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Thuế VAT (10%):</span>
                        <span id="tax"><?php echo number_format($cartSummary['tax'] ?? 0, 0, ',', '.'); ?>đ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Tổng cộng:</strong>
                        <strong class="text-danger" id="total"><?php echo number_format($cartSummary['total'] ?? 0, 0, ',', '.'); ?>đ</strong>
                    </div>
                    <a href="?page=checkout" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-credit-card"></i> Thanh toán
                    </a>
                    <small class="text-muted d-block mt-2 text-center">
                        Miễn phí vận chuyển cho đơn hàng từ 200.000đ
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
        <h4>Giỏ hàng của bạn đang trống</h4>
        <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
        <a href="?page=books" class="btn btn-primary">
            <i class="fas fa-book"></i> Khám phá sách
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Update quantity
    $('.quantity-input').change(function() {
        const bookId = $(this).data('book-id');
        const quantity = $(this).val();
        
        $.post('?page=update_cart', {
            book_id: bookId,
            quantity: quantity,
            csrf_token: '<?php echo $_SESSION["csrf_token"] ?? ""; ?>'
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Có lỗi xảy ra');
            }
        }, 'json');
    });
    
    // Remove item
    $('.remove-item').click(function() {
        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            const bookId = $(this).data('book-id');
            $.post('?page=remove_from_cart', {
                book_id: bookId,
                csrf_token: '<?php echo $_SESSION["csrf_token"] ?? ""; ?>'
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Có lỗi xảy ra');
                }
            }, 'json');
        }
    });
    
    // Clear cart
    $('#clearCart').click(function() {
        if (confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
            $.post('?page=clear_cart', {
                csrf_token: '<?php echo $_SESSION["csrf_token"] ?? ""; ?>'
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Có lỗi xảy ra');
                }
            }, 'json');
        }
    });
});
</script>
