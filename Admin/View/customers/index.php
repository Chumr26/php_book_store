<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> Quản lý khách hàng
        </h1>
        <div>
            <button id="bulk-delete-btn" class="btn btn-danger btn-icon-split shadow-sm mr-2 d-none">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Xóa (<span id="selected-count">0</span>)</span>
            </button>
            <a href="index.php?page=customers_export&<?php echo http_build_query($filters); ?>" class="btn btn-success btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-file-export"></i>
                </span>
                <span class="text">Xuất CSV</span>
            </a>
        </div>
    </div>

    <!-- Modern Filters Bar -->
    <div class="filter-bar mb-4">
        <form method="GET" action="index.php" class="row align-items-end">
            <input type="hidden" name="page" value="customers">

            <div class="col-md-4 mb-3 mb-md-0">
                <label for="search" class="small font-weight-bold text-gray-700 mb-2">Tìm kiếm</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-left">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control form-control-custom border-left-0"
                        id="search" name="search"
                        placeholder="Tên, Email, SĐT..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
            </div>

            <div class="col-md-2 mb-3 mb-md-0">
                <label for="status" class="small font-weight-bold text-gray-700 mb-2">Trạng thái</label>
                <div class="dropdown">
                    <input type="hidden" name="status" id="status_input" value="<?php echo htmlspecialchars($filters['status']); ?>">
                    <button class="btn admin-dropdown-toggle" type="button" id="statusDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-value">
                            <?php
                            $statusLabel = 'Tất cả';
                            if ($filters['status'] && isset($statuses[$filters['status']])) {
                                $statusLabel = $statuses[$filters['status']];
                            }
                            echo htmlspecialchars($statusLabel);
                            ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="statusDropdown">
                        <div class="admin-dropdown-menu-scrollable">
                            <a class="dropdown-item" href="javascript:void(0)" data-value="" onclick="selectOption('status', '', 'Tất cả')">
                                Tất cả
                            </a>
                            <?php foreach ($statuses as $key => $label): ?>
                                <a class="dropdown-item" href="javascript:void(0)"
                                    data-value="<?php echo $key; ?>"
                                    onclick="selectOption('status', '<?php echo $key; ?>', '<?php echo htmlspecialchars($label); ?>')">
                                    <?php echo htmlspecialchars($label); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3 mb-md-0">
                <label for="sort_by" class="small font-weight-bold text-gray-700 mb-2">Sắp xếp theo</label>
                <div class="dropdown">
                    <input type="hidden" name="sort_by" id="sort_by_input" value="<?php echo htmlspecialchars($filters['sort_by']); ?>">
                    <button class="btn admin-dropdown-toggle" type="button" id="sortByDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-value">
                            <?php
                            $sortLabels = [
                                'ngay_tao' => 'Ngày tham gia',
                                'ho_ten' => 'Tên',
                                'email' => 'Email'
                            ];
                            echo htmlspecialchars($sortLabels[$filters['sort_by']] ?? 'Ngày tham gia');
                            ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="sortByDropdown">
                        <div class="admin-dropdown-menu-scrollable">
                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('sort_by', 'ngay_tao', 'Ngày tham gia')">Ngày tham gia</a>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('sort_by', 'ho_ten', 'Tên')">Tên</a>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('sort_by', 'email', 'Email')">Email</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex mb-3 mb-md-0">
                <button type="submit" class="btn btn-primary mr-2 flex-grow-1">
                    Tìm kiếm
                </button>
                <a href="index.php?page=customers" class="btn btn-light border">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <form id="bulk-action-form" action="index.php?page=customer_bulk_delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <table class="table table-custom table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 40px" class="text-center pl-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="check-all">
                                        <label class="custom-control-label" for="check-all"></label>
                                    </div>
                                </th>
                                <th style="width: 80px">ID</th>
                                <th style="width: 20%">Họ tên</th>
                                <th>Email</th>
                                <th style="width: 130px">Số điện thoại</th>
                                <th style="width: 100px">Đơn hàng</th>
                                <th style="width: 130px">Chi tiêu</th>
                                <th style="width: 120px">Trạng thái</th>
                                <th style="width: 120px">Ngày tham gia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr class="clickable-row" data-href="index.php?page=customer_detail&id=<?php echo $customer['ma_khach_hang']; ?>">
                                        <td class="text-center align-middle pl-4 no-click">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ids[]" value="<?php echo $customer['ma_khach_hang']; ?>"
                                                    class="custom-control-input customer-check" id="check-<?php echo $customer['ma_khach_hang']; ?>">
                                                <label class="custom-control-label" for="check-<?php echo $customer['ma_khach_hang']; ?>"></label>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-light border"><?php echo $customer['ma_khach_hang']; ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="book-title">
                                                <i class="fas fa-user text-primary mr-2"></i>
                                                <?php echo htmlspecialchars($customer['ho_ten']); ?>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-muted"><i class="fas fa-envelope mr-1 text-gray-400"></i><?php echo htmlspecialchars($customer['email']); ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-muted"><i class="fas fa-phone mr-1 text-gray-400"></i><?php echo htmlspecialchars($customer['so_dien_thoai']); ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-pill-custom badge-info">
                                                <i class="fas fa-shopping-bag mr-1"></i>
                                                <?php echo $customer['order_count']; ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="price-tag"><?php echo number_format($customer['total_spent']); ?>đ</span>
                                        </td>
                                        <td class="align-middle text-center">
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
                                            <span class="badge badge-pill-custom <?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span>
                                        </td>
                                        <td class="align-middle"><?php echo date('d/m/Y', strtotime($customer['ngay_tao'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-gray-500 mb-2"><i class="fas fa-users fa-3x"></i></div>
                                        <p>Không tìm thấy khách hàng nào</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="px-4 py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php
                            $queryParams = $filters;
                            unset($queryParams['page']);
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
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    // Standardized Admin Dropdown Handler
    function selectOption(name, value, label) {
        // Update hidden input
        document.getElementById(name + '_input').value = value;
        // Update button text
        var btn = document.getElementById(name + 'Dropdown') || document.getElementById(name.replace('_', '') + 'Dropdown');
        if (!btn) {
            // Try alternative naming conventions
            var possibleIds = [name + 'Dropdown', name.replace('_', '') + 'Dropdown', name.charAt(0).toUpperCase() + name.slice(1) + 'Dropdown'];
            for (var i = 0; i < possibleIds.length; i++) {
                btn = document.getElementById(possibleIds[i]);
                if (btn) break;
            }
        }
        if (btn) {
            var textElement = btn.querySelector('.text-value');
            if (textElement) {
                textElement.textContent = label;
            }
            // Close the dropdown using Bootstrap's dropdown method
            $(btn).dropdown('toggle');
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Row Click
        var rows = document.querySelectorAll('.clickable-row');
        rows.forEach(function(row) {
            row.addEventListener('click', function(e) {
                // Do not trigger if clicked on checkbox, its container, or label
                if (e.target.closest('.no-click') || e.target.closest('.custom-control')) {
                    return;
                }
                var href = this.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            });
        });

        // Handle Check All
        var checkAll = document.getElementById('check-all');
        var checkboxes = document.querySelectorAll('.customer-check');
        var bulkBtn = document.getElementById('bulk-delete-btn');
        var selectedCountSpan = document.getElementById('selected-count');

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                var isChecked = this.checked;
                checkboxes.forEach(function(cb) {
                    cb.checked = isChecked;
                });
                toggleBulkBtn();
            });
        }

        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', function() {
                var allChecked = document.querySelectorAll('.customer-check:checked').length === checkboxes.length;
                if (checkAll) checkAll.checked = allChecked;
                toggleBulkBtn();
            });
        });

        function toggleBulkBtn() {
            var count = document.querySelectorAll('.customer-check:checked').length;
            if (bulkBtn) {
                if (count > 0) {
                    bulkBtn.classList.remove('d-none');
                    if (selectedCountSpan) selectedCountSpan.textContent = count;
                } else {
                    bulkBtn.classList.add('d-none');
                }
            }
        }

        // Bulk delete button handler
        if (bulkBtn) {
            bulkBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var count = document.querySelectorAll('.customer-check:checked').length;
                if (count > 0) {
                    if (confirm(`Bạn có chắc chắn muốn xóa ${count} khách hàng đã chọn?`)) {
                        document.getElementById('bulk-action-form').submit();
                    }
                }
            });
        }
    });
</script>