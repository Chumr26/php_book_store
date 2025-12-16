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
                        <strong><?php echo htmlspecialchars($order['ma_don_hang']); ?></strong>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['ngay_dat_hang'])); ?></td>
                    <td class="text-danger font-weight-bold">
                        <?php echo number_format($order['tong_tien'], 0, ',', '.'); ?>đ
                    </td>
                    <td>
                        <?php
                        $statusClass = [
                            'Chờ xác nhận' => 'warning',
                            'Đã xác nhận' => 'info',
                            'Đang xử lý' => 'primary',
                            'Đang giao hàng' => 'primary',
                            'Đã giao' => 'success',
                            'Đã hủy' => 'danger',
                            'Hoàn trả' => 'secondary'
                        ];
                        $class = $statusClass[$order['trang_thai_don_hang']] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?php echo $class; ?>">
                            <?php echo htmlspecialchars($order['trang_thai_don_hang']); ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $paymentClass = [
                            'Chờ thanh toán' => 'warning',
                            'Đã thanh toán' => 'success',
                            'Thanh toán thất bại' => 'danger',
                            'Hoàn tiền' => 'info'
                        ];
                        $pClass = $paymentClass[$order['trang_thai_thanh_toan']] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?php echo $pClass; ?>">
                            <?php echo htmlspecialchars($order['trang_thai_thanh_toan']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="?page=order_detail&id=<?php echo $order['ma_don_hang']; ?>" 
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
