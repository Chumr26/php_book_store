<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-modern">
            <li class="breadcrumb-item"><a href="index.php?page=orders"><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn #<?php echo $order['id_hoadon']; ?></li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 page-header-modern">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice"></i>
            Chi tiết đơn hàng #<?php echo $order['id_hoadon']; ?>
        </h1>
        <div>
            <a href="index.php?page=order_print_invoice&id=<?php echo $order['id_hoadon']; ?>" class="btn btn-info btn-icon-split shadow-sm mr-2">
                <span class="icon text-white-50">
                    <i class="fas fa-print"></i>
                </span>
                <span class="text">In hóa đơn</span>
            </a>
            <a href="index.php?page=orders" class="btn btn-secondary btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Quay lại</span>
            </a>
        </div>
    </div>

    <!-- Order Status Summary -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ngày đặt</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <?php echo isset($order['ngay_dat_hang']) && $order['ngay_dat_hang'] ? date('d/m/Y H:i', strtotime($order['ngay_dat_hang'])) : 'N/A'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng tiền</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($order['tong_thanh_toan']) ? number_format($order['tong_thanh_toan']) : '0'; ?>đ
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Trạng thái ĐH</div>
                            <div>
                                <?php
                                $statusClass = 'secondary';
                                $orderStatusCode = isset($order['trang_thai_don_hang']) ? $order['trang_thai_don_hang'] : '';
                                $orderStatusLabelMap = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'shipping' => 'Đang giao hàng',
                                    'completed' => 'Đã giao',
                                    'cancelled' => 'Đã hủy',
                                ];
                                $orderStatusLabel = $orderStatusLabelMap[$orderStatusCode] ?? $orderStatusCode;
                                switch ($orderStatusCode) {
                                    case 'pending':
                                        $statusClass = 'badge-warning';
                                        break;
                                    case 'confirmed':
                                        $statusClass = 'badge-info';
                                        break;
                                    case 'shipping':
                                        $statusClass = 'badge-primary';
                                        break;
                                    case 'completed':
                                        $statusClass = 'badge-success';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'badge-danger';
                                        break;
                                }
                                ?>
                                <span class="badge badge-pill-custom <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($orderStatusLabel); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Thanh toán</div>
                            <div>
                                <?php
                                $paymentClass = 'secondary';
                                $paymentStatusCode = isset($order['trang_thai_thanh_toan']) ? $order['trang_thai_thanh_toan'] : '';
                                $paymentStatusLabelMap = [
                                    'unpaid' => 'Chờ thanh toán',
                                    'paid' => 'Đã thanh toán',
                                ];
                                $paymentStatusLabel = $paymentStatusLabelMap[$paymentStatusCode] ?? $paymentStatusCode;
                                switch ($paymentStatusCode) {
                                    case 'unpaid':
                                        $paymentClass = 'badge-warning';
                                        break;
                                    case 'paid':
                                        $paymentClass = 'badge-success';
                                        break;
                                }
                                ?>
                                <span class="badge badge-pill-custom <?php echo $paymentClass; ?>">
                                    <?php echo htmlspecialchars($paymentStatusLabel); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Product List -->
            <div class="form-section-card">
                <h5><i class="fas fa-box-open mr-2"></i>Danh sách sản phẩm</h5>
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 50%">Sản phẩm</th>
                                <th class="text-center" style="width: 15%">Số lượng</th>
                                <th class="text-right" style="width: 17.5%">Đơn giá</th>
                                <th class="text-right" style="width: 17.5%">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td class="align-middle">
                                        <div class="product-info">
                                            <i class="fas fa-book text-primary mr-2"></i>
                                            <span class="font-weight-600"><?php echo htmlspecialchars($item['ten_sach']); ?></span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-light border">
                                            <i class="fas fa-times mr-1"></i><?php echo $item['so_luong']; ?>
                                        </span>
                                    </td>
                                    <td class="align-middle text-right"><?php echo number_format($item['don_gia']); ?>đ</td>
                                    <td class="align-middle text-right font-weight-bold text-primary">
                                        <?php echo number_format($item['so_luong'] * $item['don_gia']); ?>đ
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Tổng tiền hàng:</td>
                                <td class="text-right font-weight-bold"><?php echo isset($order['tong_tien']) ? number_format($order['tong_tien']) : '0'; ?>đ</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Phí vận chuyển:</td>
                                <td class="text-right">
                                    <span class="text-success">Miễn phí</span>
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="3" class="text-right font-weight-bold text-primary h5 mb-0">Tổng thanh toán:</td>
                                <td class="text-right font-weight-bold text-success h5 mb-0">
                                    <?php echo isset($order['tong_thanh_toan']) ? number_format($order['tong_thanh_toan']) : '0'; ?>đ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Update Status -->
            <div class="form-section-card">
                <h5><i class="fas fa-edit mr-2"></i>Cập nhật trạng thái</h5>
                <form action="index.php?page=order_update_status" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="order_id" value="<?php echo $order['id_hoadon']; ?>">

                    <div class="row align-items-end">
                        <div class="col-md-8 mb-3">
                            <label for="status" class="font-weight-600">Trạng thái đơn hàng</label>
                            <div class="dropdown">
                                <?php
                                $currentStatusCode = $order['trang_thai_don_hang'] ?? '';
                                $currentStatusLabel = $order_statuses[$currentStatusCode] ?? $currentStatusCode;
                                ?>
                                <input type="hidden" name="status" id="status_input" value="<?php echo htmlspecialchars($currentStatusCode); ?>">
                                <button class="btn admin-dropdown-toggle" type="button" id="statusDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="text-value"><?php echo htmlspecialchars($currentStatusLabel); ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="statusDropdown">
                                    <div class="admin-dropdown-menu-scrollable">
                                        <?php foreach ($order_statuses as $key => $value): ?>
                                            <a class="dropdown-item" href="javascript:void(0)"
                                                onclick="selectStatusOption('status', '<?php echo $key; ?>', '<?php echo htmlspecialchars($value); ?>')">
                                                <?php echo htmlspecialchars($value); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save mr-2"></i>Cập nhật
                            </button>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        <small>Lưu ý: Chỉ một số chuyển đổi trạng thái là hợp lệ (Ví dụ: Chờ xác nhận → Đã xác nhận/Hủy).</small>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Customer Info -->
            <div class="form-section-card">
                <h5><i class="fas fa-user mr-2"></i>Thông tin khách hàng</h5>
                <div class="info-group">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user-circle mr-2 text-gray-400"></i>Họ tên
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($customer['ho_ten']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>Email
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($customer['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-phone mr-2 text-gray-400"></i>Số điện thoại
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($customer['so_dien_thoai']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="form-section-card">
                <h5><i class="fas fa-shipping-fast mr-2"></i>Thông tin giao hàng</h5>
                <div class="info-group">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user mr-2 text-gray-400"></i>Người nhận
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($order['ten_nguoi_nhan']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>Địa chỉ
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($order['dia_chi_giao_hang']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-phone-alt mr-2 text-gray-400"></i>Số điện thoại
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($order['sdt_nguoi_nhan']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-sticky-note mr-2 text-gray-400"></i>Ghi chú
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($order['ghi_chu'] ?? 'Không có'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="form-section-card">
                <h5><i class="fas fa-credit-card mr-2"></i>Thông tin thanh toán</h5>
                <div class="info-group">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-money-check-alt mr-2 text-gray-400"></i>Phương thức
                        </div>
                        <div class="info-value">
                            <span class="badge badge-light border">
                                <?php echo htmlspecialchars($order['phuong_thuc_thanh_toan']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-check-circle mr-2 text-gray-400"></i>Trạng thái
                        </div>
                        <div class="info-value">
                            <?php
                            $paymentClass = 'secondary';
                            $paymentStatusCode = $order['trang_thai_thanh_toan'] ?? '';
                            $paymentStatusLabelMap = [
                                'unpaid' => 'Chờ thanh toán',
                                'paid' => 'Đã thanh toán',
                            ];
                            $paymentStatusLabel = $paymentStatusLabelMap[$paymentStatusCode] ?? $paymentStatusCode;
                            switch ($paymentStatusCode) {
                                case 'unpaid':
                                    $paymentClass = 'badge-warning';
                                    break;
                                case 'paid':
                                    $paymentClass = 'badge-success';
                                    break;
                            }
                            ?>
                            <span class="badge badge-pill-custom <?php echo $paymentClass; ?>">
                                <?php echo htmlspecialchars($paymentStatusLabel); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Custom Dropdown Handling for Status Update
    function selectStatusOption(name, value, label) {
        // Update hidden input
        document.getElementById(name + '_input').value = value;
        // Update button text
        var btn = document.getElementById(name + 'Dropdown');
        if (btn) {
            btn.querySelector('.text-value').textContent = label;
            // Close the dropdown using Bootstrap's dropdown method
            $(btn).dropdown('toggle');
        }
    }
</script>

<style>
    /* Order Detail Specific Styling */
    .product-info {
        display: flex;
        align-items: center;
    }

    .info-group {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-item {
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.75rem;
    }

    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #858796;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #3a3b45;
        word-wrap: break-word;
    }

    /* Summary Cards */
    .border-left-primary {
        border-left: .25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: .25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: .25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: .25rem solid #f6c23e !important;
    }
</style>