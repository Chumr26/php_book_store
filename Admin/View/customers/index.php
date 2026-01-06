<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý khách hàng</h1>
        <div>
            <button id="btn-delete-selected" class="btn btn-sm btn-danger shadow-sm mr-2" disabled>
                <i class="fas fa-trash fa-sm text-white-50"></i> Xóa đã chọn
            </button>
            <a href="index.php?page=customers_export&<?php echo http_build_query($filters); ?>" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-file-export fa-sm text-white-50"></i> Xuất CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc tìm kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="form-inline">
                <input type="hidden" name="page" value="customers">

                <div class="form-group mb-2 mr-3">
                    <label for="search" class="sr-only">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="Tên, Email, SĐT..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>

                <div class="form-group mb-2 mr-3">
                    <select class="form-control" name="status">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php foreach ($statuses as $key => $label): ?>
                            <option value="<?php echo $key; ?>"
                                <?php echo $filters['status'] == $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-2 mr-3">
                    <select class="form-control" name="sort_by">
                        <option value="ngay_tao" <?php echo $filters['sort_by'] == 'ngay_tao' ? 'selected' : ''; ?>>Ngày tham gia</option>
                        <option value="ho_ten" <?php echo $filters['sort_by'] == 'ho_ten' ? 'selected' : ''; ?>>Tên</option>
                        <option value="email" <?php echo $filters['sort_by'] == 'email' ? 'selected' : ''; ?>>Email</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <a href="index.php?page=customers" class="btn btn-secondary mb-2 ml-2">Đặt lại</a>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="bulk-delete-form" action="index.php?page=customer_bulk_delete" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 40px" class="text-center">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>ID</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Đơn hàng</th>
                                <th>Chi tiêu</th>
                                <th>Trạng thái</th>
                                <th>Ngày tham gia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr class="clickable-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="ids[]" value="<?php echo $customer['ma_khach_hang']; ?>" class="item-checkbox">
                                        </td>
                                        <td><?php echo $customer['ma_khach_hang']; ?></td>
                                        <td class="font-weight-bold text-primary">
                                            <a href="index.php?page=customer_detail&id=<?php echo $customer['ma_khach_hang']; ?>">
                                                <?php echo htmlspecialchars($customer['ho_ten']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['so_dien_thoai']); ?></td>
                                        <td class="text-center"><?php echo number_format($customer['order_count']); ?></td>
                                        <td><?php echo number_format($customer['total_spent']); ?>đ</td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch ($customer['trang_thai']) {
                                                case 'active':
                                                    $statusClass = 'badge-success';
                                                    break;
                                                case 'inactive':
                                                    $statusClass = 'badge-secondary';
                                                    break;
                                                case 'banned':
                                                    $statusClass = 'badge-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-secondary';
                                            }
                                            $statusLabel = $statuses[$customer['trang_thai']] ?? $customer['trang_thai'];
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($customer['ngay_tao'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-4">Không tìm thấy khách hàng nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>

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
                            <a class="page-link" href="index.php?page=customers&p=<?php echo $pagination['current_page'] - 1; ?>&<?php echo $queryString; ?>">Trước</a>
                        </li>

                        <?php
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);

                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="index.php?page=customers&p=1&' . $queryString . '">1</a></li>';
                            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }

                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li class="page-item <?php echo $pagination['current_page'] == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?page=customers&p=<?php echo $i; ?>&<?php echo $queryString; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php
                        if ($end < $pagination['total_pages']) {
                            if ($end < $pagination['total_pages'] - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo '<li class="page-item"><a class="page-link" href="index.php?page=customers&p=' . $pagination['total_pages'] . '&' . $queryString . '">' . $pagination['total_pages'] . '</a></li>';
                        }
                        ?>

                        <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=customers&p=<?php echo $pagination['current_page'] + 1; ?>&<?php echo $queryString; ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const btnDelete = document.getElementById('btn-delete-selected');
        const form = document.getElementById('bulk-delete-form');

        // Select All handler
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateDeleteButton();
            });
        }

        // Individual checkbox handler
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateDeleteButton();
                if (!this.checked) {
                    if (selectAll) selectAll.checked = false;
                }
            });
        });

        // Update delete button state
        function updateDeleteButton() {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            if (btnDelete) {
                btnDelete.disabled = checkedCount === 0;
                btnDelete.innerHTML = `<i class="fas fa-trash fa-sm text-white-50"></i> Xóa đã chọn (${checkedCount})`;
            }
        }

        // Delete button click handler
        if (btnDelete) {
            btnDelete.addEventListener('click', function(e) {
                e.preventDefault();
                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                if (checkedCount > 0) {
                    if (confirm(`Bạn có chắc chắn muốn xóa ${checkedCount} khách hàng đã chọn?`)) {
                        form.submit();
                    }
                }
            });
        }
    });
</script>