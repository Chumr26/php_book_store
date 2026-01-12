<?php
$pageTitle = "Xác nhận đơn hàng";

$orderNumber = $order['ma_hoadon'] ?? '';
$statusCode = $order['trang_thai'] ?? '';
$paymentCode = $order['trang_thai_thanh_toan'] ?? '';

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
    <div class="alert alert-success">
        <h4 class="mb-1"><i class="fas fa-check-circle"></i> Đặt hàng thành công</h4>
        <div>Mã đơn hàng: <strong><?php echo htmlspecialchars($orderNumber); ?></strong></div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Thông tin đơn hàng</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div><strong>Ngày đặt:</strong> <?php echo !empty($order['ngay_dat_hang']) ? date('d/m/Y H:i', strtotime($order['ngay_dat_hang'])) : '-'; ?></div>
                    <div><strong>Tổng tiền:</strong> <span class="text-danger font-weight-bold"><?php echo number_format($order['tong_tien'] ?? 0, 0, ',', '.'); ?>đ</span></div>
                </div>
                <div class="col-md-6">
                    <div><strong>Trạng thái:</strong> <?php echo htmlspecialchars($statusLabels[$statusCode] ?? $statusCode); ?></div>
                    <div><strong>Thanh toán:</strong> <?php echo htmlspecialchars($paymentLabels[$paymentCode] ?? $paymentCode); ?></div>
                </div>
            </div>

            <hr>

            <div><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['ten_nguoi_nhan'] ?? ''); ?></div>
            <div><strong>SĐT:</strong> <?php echo htmlspecialchars($order['sdt_giao'] ?? ''); ?></div>
            <div><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['dia_chi_giao'] ?? ''); ?></div>
            <?php if (!empty($order['ghi_chu'])): ?>
                <div><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['ghi_chu']); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <strong>Chi tiết sản phẩm</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Sách</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-right">Đơn giá</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($order_items ?? []) as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['ten_sach'] ?? ''); ?></td>
                                <td class="text-center"><?php echo (int)($item['so_luong'] ?? $item['quantity'] ?? 0); ?></td>
                                <td class="text-right"><?php echo number_format($item['gia'] ?? $item['price'] ?? 0, 0, ',', '.'); ?>đ</td>
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

    <div class="mt-3 d-flex">
        <a class="btn btn-primary mr-2" href="?page=orders"><i class="fas fa-box"></i> Xem đơn hàng</a>
        <a class="btn btn-outline-secondary" href="?page=books"><i class="fas fa-shopping-cart"></i> Tiếp tục mua sắm</a>
    </div>
</div>
