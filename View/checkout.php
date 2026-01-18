<?php
$pageTitle = "Thanh toán";

require_once __DIR__ . '/helpers/cover.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-credit-card"></i> Thanh toán đơn hàng</h2>

    <?php if (isset($cartItems) && !empty($cartItems)): ?>
        <form method="POST" action="?page=checkout" id="checkoutForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <div class="row">
                <!-- Delivery Information -->
                <div class="col-md-7">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Thông tin giao hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Họ và tên người nhận <span class="text-danger">*</span></label>
                                <input type="text" name="recipient_name" class="form-control" required
                                    value="<?php echo htmlspecialchars($customer_info['ten_khachhang'] ?? ($_SESSION['customer_name'] ?? '')); ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" name="phone" class="form-control" required
                                            value="<?php echo htmlspecialchars($customer_info['dien_thoai'] ?? ($_SESSION['customer_phone'] ?? '')); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" required
                                            value="<?php echo htmlspecialchars($customer_info['email'] ?? ($_SESSION['customer_email'] ?? '')); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" required
                                    value="<?php echo htmlspecialchars($customer_info['dia_chi'] ?? ''); ?>"
                                    placeholder="Số nhà, tên đường...">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                        <select name="city" class="form-control" required>
                                            <option value="">-- Chọn Tỉnh/TP --</option>
                                            <option value="Hồ Chí Minh">TP. Hồ Chí Minh</option>
                                            <option value="Hà Nội">Hà Nội</option>
                                            <option value="Đà Nẵng">Đà Nẵng</option>
                                            <option value="Cần Thơ">Cần Thơ</option>
                                            <option value="Khác">Tỉnh khác</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Quận/Huyện <span class="text-danger">*</span></label>
                                        <input type="text" name="district" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Ghi chú</label>
                                <textarea name="note" class="form-control" rows="3"
                                    placeholder="Ghi chú thêm (không bắt buộc)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-money-check-alt"></i> Phương thức thanh toán</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // Default icons
                            $icons = [
                                'cod' => 'fas fa-money-bill-wave',
                                'payos' => 'fas fa-credit-card',
                                'momo' => 'fas fa-wallet'
                            ];

                            $first = true;
                            foreach ($payment_methods as $code => $name):
                                $isChecked = $first ? 'checked' : '';
                                $iconClass = $icons[$code] ?? 'fas fa-money-check';
                            ?>
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="payment_<?php echo $code; ?>" name="payment_method" value="<?php echo $code; ?>"
                                        class="custom-control-input" <?php echo $isChecked; ?>>
                                    <label class="custom-control-label" for="payment_<?php echo $code; ?>">
                                        <i class="<?php echo $iconClass; ?> mr-2"></i>
                                        <strong><?php echo htmlspecialchars($name); ?></strong>
                                    </label>
                                </div>
                            <?php
                                $first = false;
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-md-5">
                    <div class="card sticky-top" style="top: 160px;">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-receipt"></i> Đơn hàng (<?php echo count($cartItems); ?> sản phẩm)</h5>
                        </div>
                        <div class="card-body">
                            <!-- Order Items -->
                            <div class="order-items mb-3" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="d-flex mb-3 pb-3 border-bottom">
                                        <?php $coverUrl = book_cover_url($item['isbn'] ?? null, 'small'); ?>
                                        <img src="<?php echo htmlspecialchars($coverUrl); ?>"
                                            loading="lazy" decoding="async"
                                            style="width: 60px; height: 80px; object-fit: cover;" class="mr-3">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1" style="font-size: 14px;">
                                                <?php echo htmlspecialchars($item['ten_sach']); ?>
                                            </h6>
                                            <small class="text-muted">SL: <?php echo $item['so_luong']; ?></small>
                                            <div class="text-danger font-weight-bold">
                                                <?php echo number_format($item['gia'] * $item['so_luong'], 0, ',', '.'); ?>đ
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Coupon Input -->
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="couponCode" placeholder="Nhập mã giảm giá"
                                    value="<?php echo isset($_SESSION['coupon_code']) ? htmlspecialchars($_SESSION['coupon_code']) : ''; ?>"
                                    <?php echo isset($_SESSION['coupon_code']) ? 'disabled' : ''; ?>>
                                <div class="input-group-append">
                                    <?php if (isset($_SESSION['coupon_code'])): ?>
                                        <button class="btn btn-outline-danger" type="button" id="removeCouponBtn">Xóa</button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-primary" type="button" id="applyCouponBtn">Áp dụng</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div id="couponMessage" class="small mb-3"></div>

                            <!-- Order Totals -->
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span><?php echo number_format($cartSummary['subtotal'] ?? 0, 0, ',', '.'); ?>đ</span>
                            </div>
                            <?php if (!empty($cartSummary['discount_amount'])): ?>
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Giảm giá (<?php echo htmlspecialchars($cartSummary['coupon_code'] ?? ''); ?>):</span>
                                    <span>-<?php echo number_format($cartSummary['discount_amount'], 0, ',', '.'); ?>đ</span>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span><?php echo number_format($cartSummary['shipping'] ?? 0, 0, ',', '.'); ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Thuế VAT (10%):</span>
                                <span><?php echo number_format($cartSummary['tax'] ?? 0, 0, ',', '.'); ?>đ</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Tổng cộng:</strong>
                                <strong class="text-danger h5"><?php echo number_format($cartSummary['total'] ?? 0, 0, ',', '.'); ?>đ</strong>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-lg" id="placeOrderBtn">
                                <i class="fas fa-check-circle"></i> Đặt hàng
                            </button>

                            <a href="?page=cart" class="btn btn-outline-secondary btn-block mt-2">
                                <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                            </a>

                            <div class="alert alert-info mt-3 mb-0">
                                <small><i class="fas fa-info-circle"></i> Miễn phí vận chuyển cho đơn hàng từ 200.000đ</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Giỏ hàng của bạn đang trống.
            <a href="?page=books">Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        var $form = $('#checkoutForm');
        var $btn = $('#placeOrderBtn');
        var originalHtml = $btn.html();
        var isSubmitting = false;

        function setLoading(loading) {
            if (loading) {
                $btn.prop('disabled', true);
                $btn.text('Đang đặt hàng...');
            } else {
                $btn.prop('disabled', false);
                $btn.html(originalHtml);
            }
        }

        var checkoutConfirmed = false;

        $form.on('submit', function(e) {
            // Block double-submit.
            if (isSubmitting) {
                e.preventDefault();
                return;
            }

            // If not confirmed yet, validate + show modal.
            if (!checkoutConfirmed) {
                var formEl = $form.get(0);
                if (formEl && typeof formEl.checkValidity === 'function' && !formEl.checkValidity()) {
                    if (typeof formEl.reportValidity === 'function') {
                        formEl.reportValidity();
                    }
                    return;
                }

                e.preventDefault();

                // Use the global confirm modal (defined in View/footer.php)
                if (typeof window.showConfirmModal === 'function') {
                    window.showConfirmModal('Xác nhận đặt hàng?', function() {
                        checkoutConfirmed = true;
                        $form.trigger('submit');
                    });
                    return;
                }

                // If modal helper isn't available for some reason, proceed without blocking.
                checkoutConfirmed = true;
                $form.trigger('submit');
                return;
            }

            // Confirmed: submit and show loading.
            isSubmitting = true;
            setLoading(true);
        });
        // Coupon Handling
        $('#applyCouponBtn').on('click', function() {
            var code = $('#couponCode').val().trim();
            if (!code) {
                $('#couponMessage').html('<span class="text-danger">Vui lòng nhập mã giảm giá</span>');
                return;
            }

            var $this = $(this);
            $this.prop('disabled', true).text('Đang xử lý...');

            $.ajax({
                url: 'index.php?page=apply_coupon',
                method: 'POST',
                data: {
                    coupon_code: code,
                    csrf_token: $('input[name="csrf_token"]').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#couponMessage').html('<span class="text-success">' + response.message + '</span>');
                        location.reload(); // Reload to update totals
                    } else {
                        $('#couponMessage').html('<span class="text-danger">' + response.message + '</span>');
                        $this.prop('disabled', false).text('Áp dụng');
                    }
                },
                error: function() {
                    $('#couponMessage').html('<span class="text-danger">Có lỗi xảy ra. Vui lòng thử lại.</span>');
                    $this.prop('disabled', false).text('Áp dụng');
                }
            });
        });

        $('#removeCouponBtn').on('click', function() {
            var $this = $(this);
            $this.prop('disabled', true).text('Đang xử lý...');

            $.ajax({
                url: 'index.php?page=remove_coupon',
                method: 'POST',
                data: {
                    csrf_token: $('input[name="csrf_token"]').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        $('#couponMessage').html('<span class="text-danger">' + response.message + '</span>');
                        $this.prop('disabled', false).text('Xóa');
                    }
                },
                error: function() {
                    $('#couponMessage').html('<span class="text-danger">Có lỗi xảy ra. Vui lòng thử lại.</span>');
                    $this.prop('disabled', false).text('Xóa');
                }
            });
        });
    });
</script>