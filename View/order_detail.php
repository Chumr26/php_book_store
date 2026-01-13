<?php
$pageTitle = "Chi tiết đơn hàng";

$statusCode = $order['status'] ?? $order['trang_thai'] ?? '';
$paymentCode = $order['payment_status'] ?? $order['trang_thai_thanh_toan'] ?? '';

$statusLabels = [
    'pending' => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping' => 'Đang giao hàng',
    'completed' => 'Đã giao',
    'cancelled' => 'Đã hủy'
];

$paymentLabels = [
    'unpaid' => 'Chờ thanh toán',
    'paid' => 'Đã thanh toán'
];
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="fas fa-receipt"></i> Chi tiết đơn hàng</h2>
        <a class="btn btn-outline-secondary" href="?page=orders"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Thông tin đơn hàng</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div><strong>Mã đơn:</strong> <?php echo htmlspecialchars($order['order_number'] ?? $order['ma_hoadon'] ?? ''); ?></div>
                    <div><strong>Ngày đặt:</strong> <?php echo !empty($order['order_date']) ? date('d/m/Y H:i', strtotime($order['order_date'])) : (!empty($order['ngay_dat_hang']) ? date('d/m/Y H:i', strtotime($order['ngay_dat_hang'])) : '-'); ?></div>
                </div>
                <div class="col-md-6">
                    <div><strong>Trạng thái:</strong> <?php echo htmlspecialchars($statusLabels[$statusCode] ?? $statusCode); ?></div>
                    <div><strong>Thanh toán:</strong> <?php echo htmlspecialchars($paymentLabels[$paymentCode] ?? $paymentCode); ?></div>
                </div>
            </div>

            <hr>

            <div><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['recipient_name'] ?? $order['ten_nguoi_nhan'] ?? ''); ?></div>
            <div><strong>SĐT:</strong> <?php echo htmlspecialchars($order['phone'] ?? $order['sdt_giao'] ?? ''); ?></div>
            <div><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['delivery_address'] ?? $order['dia_chi_giao'] ?? ''); ?></div>

            <div class="mt-2"><strong>Tổng tiền:</strong> <span class="text-danger font-weight-bold"><?php echo number_format($order['total_amount'] ?? $order['tong_tien'] ?? 0, 0, ',', '.'); ?>đ</span></div>

            <?php if (($statusCode === 'pending') && !empty($csrf_token)): ?>
                <form method="POST" action="?page=cancel_order" class="mt-3" id="cancelOrderForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="order_id" value="<?php echo (int)($order['id_order'] ?? 0); ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle"></i> Hủy đơn hàng
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <strong>Sản phẩm</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Sách</th>
                            <th class="text-center">SL</th>
                            <th class="text-right">Đơn giá</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($order_items ?? []) as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['ten_sach'] ?? $item['title'] ?? ''); ?></td>
                                <td class="text-center"><?php echo (int)($item['quantity'] ?? $item['so_luong'] ?? 0); ?></td>
                                <td class="text-right"><?php echo number_format($item['price'] ?? $item['gia'] ?? 0, 0, ',', '.'); ?>đ</td>
                                <td class="text-right"><?php echo number_format($item['thanh_tien'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 0)), 0, ',', '.'); ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($order_items)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Không có sản phẩm</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var $form = $('#cancelOrderForm');
    if ($form.length) {
        $form.on('submit', function(e) {
            e.preventDefault();
            if (typeof window.showConfirmModal === 'function') {
                window.showConfirmModal('Xác nhận hủy đơn hàng?', function() {
                    document.getElementById('cancelOrderForm').submit();
                });
            }
        });
    }
});
</script>
