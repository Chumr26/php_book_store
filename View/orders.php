<?php
$pageTitle = "Đơn hàng của tôi";
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-box"></i> Đơn hàng của tôi</h2>
    
    <?php if (isset($orders) && !empty($orders)): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thanh toán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($order['ma_hoadon'] ?? $order['order_number'] ?? ''); ?></strong>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['ngay_dat_hang'] ?? $order['order_date'] ?? 'now')); ?></td>
                    <td class="text-danger font-weight-bold">
                        <?php echo number_format($order['tong_tien'] ?? $order['total_amount'] ?? 0, 0, ',', '.'); ?>đ
                    </td>
                    <td>
                        <?php
                        $statusLabels = [
                            'pending' => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'shipping' => 'Đang giao hàng',
                            'completed' => 'Đã giao',
                            'cancelled' => 'Đã hủy'
                        ];
                        $statusCode = $order['trang_thai'] ?? $order['status'] ?? '';
                        $statusText = $statusLabels[$statusCode] ?? $statusCode;
                        $statusClass = [
                            'pending' => 'warning',
                            'confirmed' => 'info',
                            'shipping' => 'primary',
                            'completed' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $class = $statusClass[$statusCode] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?php echo $class; ?>">
                            <?php echo htmlspecialchars($statusText); ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $payLabels = [
                            'unpaid' => 'Chờ thanh toán',
                            'paid' => 'Đã thanh toán'
                        ];
                        $payCode = $order['trang_thai_thanh_toan'] ?? $order['payment_status'] ?? '';
                        $payText = $payLabels[$payCode] ?? $payCode;
                        $paymentClass = [
                            'unpaid' => 'warning',
                            'paid' => 'success'
                        ];
                        $pClass = $paymentClass[$payCode] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?php echo $pClass; ?>">
                            <?php echo htmlspecialchars($payText); ?>
                        </span>
                    </td>
                    <td>
                        <a href="?page=order_detail&id=<?php echo (int)($order['id_hoadon'] ?? $order['id_order'] ?? 0); ?>" 
                           class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Chi tiết
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-5x text-muted mb-3"></i>
        <h4>Bạn chưa có đơn hàng nào</h4>
        <p class="text-muted">Hãy bắt đầu mua sắm ngay!</p>
        <a href="?page=books" class="btn btn-primary">
            <i class="fas fa-shopping-cart"></i> Mua sắm ngay
        </a>
    </div>
    <?php endif; ?>
</div>
