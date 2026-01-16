<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-ticket-alt"></i> Quản lý mã giảm giá
        </h1>
        <div>
            <a href="index.php?page=admin_coupon_create" class="btn btn-primary btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Thêm mã giảm giá</span>
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 15%">Mã Code</th>
                            <th style="width: 25%">Chương trình</th>
                            <th style="width: 15%">Giảm giá</th>
                            <th style="width: 15%">Số lượng</th>
                            <th style="width: 20%">Thời gian</th>
                            <th style="width: 10%">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($coupons)): ?>
                            <?php foreach ($coupons as $coupon): ?>
                                <tr class="clickable-row" data-href="index.php?page=admin_coupon_edit&id=<?php echo $coupon['id_magiamgia']; ?>">
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-primary"><?php echo htmlspecialchars($coupon['ma_code']); ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <?php echo htmlspecialchars($coupon['ten_chuongtrinh']); ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($coupon['loai_giam'] == 'percent'): ?>
                                            <span class="badge badge-info"><?php echo $coupon['gia_tri_giam']; ?>%</span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><?php echo number_format($coupon['gia_tri_giam'], 0, ',', '.'); ?>đ</span>
                                        <?php endif; ?>
                                        <div class="small text-muted mt-1">
                                            Min: <?php echo number_format($coupon['gia_tri_toi_thieu'], 0, ',', '.'); ?>đ
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress progress-sm mb-1">
                                            <?php
                                            $percent = ($coupon['da_su_dung'] / $coupon['so_luong']) * 100;
                                            $color = $percent > 90 ? 'danger' : ($percent > 50 ? 'warning' : 'success');
                                            ?>
                                            <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $percent; ?>%"
                                                aria-valuenow="<?php echo $coupon['da_su_dung']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $coupon['so_luong']; ?>"></div>
                                        </div>
                                        <small><?php echo $coupon['da_su_dung']; ?> / <?php echo $coupon['so_luong']; ?></small>
                                    </td>
                                    <td class="align-middle small">
                                        <div>BĐ: <?php echo date('H:i d/m/Y', strtotime($coupon['ngay_bat_dau'])); ?></div>
                                        <div>KT: <?php echo date('H:i d/m/Y', strtotime($coupon['ngay_ket_thuc'])); ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($coupon['trang_thai'] == 'active'): ?>
                                            <?php if (strtotime($coupon['ngay_ket_thuc']) < time()): ?>
                                                <span class="badge badge-secondary">Hết hạn</span>
                                            <?php elseif ($coupon['da_su_dung'] >= $coupon['so_luong']): ?>
                                                <span class="badge badge-secondary">Hết lượt</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Hoạt động</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Vô hiệu</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p>Chưa có mã giảm giá nào</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var rows = document.querySelectorAll('.clickable-row');
        rows.forEach(function(row) {
            row.addEventListener('click', function(e) {
                var href = this.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            });
        });
    });
</script>