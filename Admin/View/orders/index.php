<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý đơn hàng</h1>
        <a href="index.php?page=orders_export&<?php echo http_build_query($filters); ?>" class="btn btn-success btn-icon-split shadow-sm">
            <span class="icon text-white-50">
                <i class="fas fa-file-export"></i>
            </span>
            <span class="text">Xuất CSV</span>
        </a>
    </div>

    <!-- Modern Filters Bar -->
    <div class="filter-bar mb-4">
        <form method="GET" action="index.php" class="row align-items-center">
            <input type="hidden" name="page" value="orders">

            <!-- Search Input -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-left">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control form-control-custom border-left-0"
                        id="search" name="search"
                        placeholder="Mã ĐH, Tên KH, Email..."
                        value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>
            </div>

            <!-- Status Dropdown -->
            <div class="col-md-2 mb-3 mb-md-0">
                <div class="dropdown">
                    <input type="hidden" name="status" id="status_input" value="<?php echo htmlspecialchars($filters['status']); ?>">
                    <button class="btn admin-dropdown-toggle" type="button" id="statusDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-value">
                            <?php
                            $statusLabel = 'Trạng thái (Tất cả)';
                            if (!empty($filters['status']) && isset($order_statuses[$filters['status']])) {
                                $statusLabel = $order_statuses[$filters['status']];
                            }
                            echo htmlspecialchars($statusLabel);
                            ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="statusDropdown">
                        <div class="admin-dropdown-menu-scrollable">
                            <a class="dropdown-item" href="javascript:void(0)"
                                onclick="selectOrderOption('status', '', 'Trạng thái (Tất cả)')">
                                Trạng thái (Tất cả)
                            </a>
                            <?php foreach ($order_statuses as $key => $value): ?>
                                <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="selectOrderOption('status', '<?php echo $key; ?>', '<?php echo htmlspecialchars($value); ?>')">
                                    <?php echo htmlspecialchars($value); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- From Date -->
            <div class="col-md-2 mb-3 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </span>
                    </div>
                    <input type="date" class="form-control" id="from_date" name="from_date"
                        value="<?php echo htmlspecialchars($filters['from_date']); ?>"
                        placeholder="Từ ngày">
                </div>
            </div>

            <!-- To Date -->
            <div class="col-md-2 mb-3 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </span>
                    </div>
                    <input type="date" class="form-control" id="to_date" name="to_date"
                        value="<?php echo htmlspecialchars($filters['to_date']); ?>"
                        placeholder="Đến ngày">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary mr-2 flex-grow-1">
                    Lọc
                </button>
                <a href="index.php?page=orders" class="btn btn-light border">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 10%">Mã ĐH</th>
                            <th style="width: 15%">Ngày đặt</th>
                            <th style="width: 25%">Khách hàng</th>
                            <th style="width: 15%">Tổng tiền</th>
                            <th style="width: 17.5%">Trạng thái ĐH</th>
                            <th style="width: 17.5%">Thanh toán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr class="clickable-row" data-href="index.php?page=order_detail&id=<?php echo $order['id_hoadon']; ?>">
                                    <td class="align-middle">
                                        <div class="order-id">
                                            <i class="fas fa-receipt mr-2 text-primary"></i>
                                            <span class="font-weight-bold">#<?php echo $order['id_hoadon']; ?></span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="order-date">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i>
                                            <?php echo date('d/m/Y', strtotime($order['ngay_dat'])); ?>
                                        </div>
                                        <small class="text-muted"><?php echo date('H:i', strtotime($order['ngay_dat'])); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <div class="customer-info">
                                            <div class="customer-name"><?php echo htmlspecialchars($order['ho_ten']); ?></div>
                                            <small class="customer-email">
                                                <i class="fas fa-envelope mr-1"></i>
                                                <?php echo htmlspecialchars($order['email']); ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="price-tag text-success font-weight-bold">
                                            <?php echo number_format($order['tong_thanh_toan']); ?>đ
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <?php
                                        $statusClass = 'secondary';
                                        switch ($order['trang_thai_don_hang']) {
                                            case 'Chờ xác nhận':
                                                $statusClass = 'badge-warning';
                                                break;
                                            case 'Đã xác nhận':
                                                $statusClass = 'badge-info';
                                                break;
                                            case 'Đang xử lý':
                                                $statusClass = 'badge-primary';
                                                break;
                                            case 'Đang giao hàng':
                                                $statusClass = 'badge-info';
                                                break;
                                            case 'Đã giao':
                                                $statusClass = 'badge-success';
                                                break;
                                            case 'Đã hủy':
                                                $statusClass = 'badge-danger';
                                                break;
                                            case 'Hoàn trả':
                                                $statusClass = 'badge-dark';
                                                break;
                                        }
                                        ?>
                                        <span class="badge badge-pill-custom <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($order['trang_thai_don_hang']); ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <?php
                                        $paymentClass = 'secondary';
                                        switch ($order['trang_thai_thanh_toan']) {
                                            case 'Chờ thanh toán':
                                                $paymentClass = 'badge-warning';
                                                break;
                                            case 'Đã thanh toán':
                                                $paymentClass = 'badge-success';
                                                break;
                                            case 'Thanh toán thất bại':
                                                $paymentClass = 'badge-danger';
                                                break;
                                            case 'Hoàn tiền':
                                                $paymentClass = 'badge-dark';
                                                break;
                                        }
                                        ?>
                                        <span class="badge badge-pill-custom <?php echo $paymentClass; ?>">
                                            <?php echo htmlspecialchars($order['trang_thai_thanh_toan']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-gray-500 mb-2"><i class="fas fa-inbox fa-3x"></i></div>
                                    <p>Không tìm thấy đơn hàng nào phù hợp</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="card-footer bg-white py-3 border-0">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php
                            $queryParams = $filters;
                            $queryString = http_build_query($queryParams);
                            ?>

                            <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link border-0" href="index.php?page=orders&p=<?php echo $pagination['current_page'] - 1; ?>&<?php echo $queryString; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            <?php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);

                            if ($start > 1) {
                                echo '<li class="page-item"><a class="page-link border-0" href="index.php?page=orders&p=1&' . $queryString . '">1</a></li>';
                                if ($start > 2) echo '<li class="page-item disabled"><span class="page-link border-0">...</span></li>';
                            }

                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?php echo $pagination['current_page'] == $i ? 'active' : ''; ?>">
                                    <a class="page-link border-0 rounded-circle mx-1" href="index.php?page=orders&p=<?php echo $i; ?>&<?php echo $queryString; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php
                            if ($end < $pagination['total_pages']) {
                                if ($end < $pagination['total_pages'] - 1) echo '<li class="page-item disabled"><span class="page-link border-0">...</span></li>';
                                echo '<li class="page-item"><a class="page-link border-0" href="index.php?page=orders&p=' . $pagination['total_pages'] . '&' . $queryString . '">' . $pagination['total_pages'] . '</a></li>';
                            }
                            ?>

                            <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link border-0" href="index.php?page=orders&p=<?php echo $pagination['current_page'] + 1; ?>&<?php echo $queryString; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Row Click
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

    // Custom Dropdown Handling for Orders
    function selectOrderOption(name, value, label) {
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
    /* Order-specific styling */
    .order-id {
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .order-date {
        font-weight: 500;
        color: #3a3b45;
        margin-bottom: 0.25rem;
    }

    .customer-info .customer-name {
        font-weight: 600;
        color: #3a3b45;
        margin-bottom: 0.25rem;
    }

    .customer-info .customer-email {
        font-size: 0.8rem;
        color: #858796;
        display: block;
    }
</style>