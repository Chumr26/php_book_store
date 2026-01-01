<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý đơn hàng</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc tìm kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="form-inline">
                <input type="hidden" name="page" value="orders">
                
                <div class="form-group mb-2 mr-3">
                    <label for="search" class="sr-only">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Mã ĐH, Tên KH, Email..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>

                <div class="form-group mb-2 mr-3">
                    <select class="form-control" name="status">
                        <option value="">-- Trạng thái đơn hàng --</option>
                        <?php foreach ($order_statuses as $key => $value): ?>
                            <option value="<?php echo $key; ?>" 
                                <?php echo $filters['status'] == $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($value); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-2 mr-3">
                    <label for="from_date" class="mr-2">Từ ngày:</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" 
                           value="<?php echo htmlspecialchars($filters['from_date']); ?>">
                </div>

                <div class="form-group mb-2 mr-3">
                    <label for="to_date" class="mr-2">Đến ngày:</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" 
                           value="<?php echo htmlspecialchars($filters['to_date']); ?>">
                </div>

                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <a href="index.php?page=orders" class="btn btn-secondary mb-2 ml-2">Đặt lại</a>
                <a href="index.php?page=orders_export&<?php echo http_build_query($filters); ?>" class="btn btn-success mb-2 ml-2">
                    <i class="fas fa-file-export"></i> Xuất CSV
                </a>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Ngày đặt</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái đơn hàng</th>
                            <th>Thanh toán</th>
                            <th style="width: 100px">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <a href="index.php?page=order_detail&id=<?php echo $order['id_hoadon']; ?>" class="font-weight-bold">
                                        #<?php echo $order['id_hoadon']; ?>
                                    </a>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['ngay_dat'])); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($order['ho_ten']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                </td>
                                <td class="font-weight-bold text-primary"><?php echo number_format($order['tong_thanh_toan']); ?>đ</td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    switch($order['trang_thai_don_hang']) {
                                        case 'Chờ xác nhận': $statusClass = 'warning'; break;
                                        case 'Đã xác nhận': $statusClass = 'info'; break;
                                        case 'Đang xử lý': $statusClass = 'primary'; break;
                                        case 'Đang giao hàng': $statusClass = 'info'; break;
                                        case 'Đã giao': $statusClass = 'success'; break;
                                        case 'Đã hủy': $statusClass = 'danger'; break;
                                        case 'Hoàn trả': $statusClass = 'dark'; break;
                                    }
                                    ?>
                                    <span class="badge badge-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['trang_thai_don_hang']); ?></span>
                                </td>
                                <td>
                                    <?php
                                    $paymentClass = 'secondary';
                                    switch($order['trang_thai_thanh_toan']) {
                                        case 'Chờ thanh toán': $paymentClass = 'warning'; break;
                                        case 'Đã thanh toán': $paymentClass = 'success'; break;
                                        case 'Thanh toán thất bại': $paymentClass = 'danger'; break;
                                        case 'Hoàn tiền': $paymentClass = 'dark'; break;
                                    }
                                    ?>
                                    <span class="badge badge-<?php echo $paymentClass; ?>"><?php echo htmlspecialchars($order['trang_thai_thanh_toan']); ?></span>
                                </td>
                                <td>
                                    <a href="index.php?page=order_detail&id=<?php echo $order['id_hoadon']; ?>" class="btn btn-info btn-sm" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Không tìm thấy đơn hàng nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php 
                        $queryParams = $filters;
                        unset($queryParams['page']); // Handled in href
                        $queryString = http_build_query($queryParams);
                        ?>
                        
                        <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=orders&p=<?php echo $pagination['current_page'] - 1; ?>&<?php echo $queryString; ?>">Trước</a>
                        </li>
                        
                        <?php 
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="index.php?page=orders&p=1&' . $queryString . '">1</a></li>';
                            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        
                        for ($i = $start; $i <= $end; $i++): 
                        ?>
                        <li class="page-item <?php echo $pagination['current_page'] == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="index.php?page=orders&p=<?php echo $i; ?>&<?php echo $queryString; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php 
                        if ($end < $pagination['total_pages']) {
                            if ($end < $pagination['total_pages'] - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo '<li class="page-item"><a class="page-link" href="index.php?page=orders&p=' . $pagination['total_pages'] . '&' . $queryString . '">' . $pagination['total_pages'] . '</a></li>';
                        }
                        ?>

                        <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=orders&p=<?php echo $pagination['current_page'] + 1; ?>&<?php echo $queryString; ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
