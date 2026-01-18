<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý sách</h1>
        <div>
            <button id="bulk-delete-btn" class="btn btn-danger btn-icon-split shadow-sm mr-2 d-none">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Xóa (<span id="selected-count">0</span>)</span>
            </button>
            <a href="index.php?page=book_create" class="btn btn-primary btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Thêm sách mới</span>
            </a>
        </div>
    </div>

    <!-- Modern Filters Bar -->
    <div class="filter-bar mb-4">
        <form method="GET" action="index.php" class="row align-items-center">
            <input type="hidden" name="page" value="books">

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-left">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control form-control-custom border-left-0"
                        id="search" name="search"
                        placeholder="Tìm kiếm sách..."
                        value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>
            </div>

            <div class="col-md-2 mb-3 mb-md-0">
                <div class="dropdown">
                    <input type="hidden" name="category" id="category_input" value="<?php echo htmlspecialchars($filters['category']); ?>">
                    <button class="btn admin-dropdown-toggle" type="button" id="categoryDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-value">
                            <?php
                            $catName = 'Danh mục (Tất cả)';
                            if ($filters['category'] != 0) {
                                foreach ($categories as $cat) {
                                    if ($cat['ma_danh_muc'] == $filters['category']) {
                                        $catName = $cat['ten_danh_muc'];
                                        break;
                                    }
                                }
                            }
                            echo htmlspecialchars($catName);
                            ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <div class="admin-dropdown-menu-scrollable">
                            <a class="dropdown-item" href="javascript:void(0)" data-value="0" onclick="selectOption('category', '0', 'Danh mục (Tất cả)')">
                                Danh mục (Tất cả)
                            </a>
                            <?php foreach ($categories as $cat): ?>
                                <a class="dropdown-item" href="javascript:void(0)"
                                    data-value="<?php echo $cat['ma_danh_muc']; ?>"
                                    onclick="selectOption('category', '<?php echo $cat['ma_danh_muc']; ?>', '<?php echo htmlspecialchars($cat['ten_danh_muc']); ?>')">
                                    <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3 mb-md-0">
                <div class="dropdown">
                    <input type="hidden" name="status" id="status_input" value="<?php echo htmlspecialchars($filters['status']); ?>">
                    <button class="btn admin-dropdown-toggle" type="button" id="statusDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-value">
                            <?php echo $filters['status'] ? htmlspecialchars($filters['status']) : 'Trạng thái (Tất cả)'; ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="statusDropdown">
                        <a class="dropdown-item" href="javascript:void(0)" data-value="" onclick="selectOption('status', '', 'Trạng thái (Tất cả)')">
                            Trạng thái (Tất cả)
                        </a>
                        <?php foreach ($statuses as $st): ?>
                            <a class="dropdown-item" href="javascript:void(0)"
                                data-value="<?php echo $st; ?>"
                                onclick="selectOption('status', '<?php echo $st; ?>', '<?php echo htmlspecialchars($st); ?>')">
                                <?php echo htmlspecialchars($st); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3 mb-md-0">
                <div class="dropdown">
                    <input type="hidden" name="sort_by" id="sort_input" value="<?php echo htmlspecialchars($filters['sort_by']); ?>">
                    <button class="btn admin-dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-value">
                            <?php
                            $sortLabel = 'Mới nhất';
                            $sorts = [
                                'ngay_tao' => 'Mới nhất',
                                'ten_sach' => 'Tên A-Z',
                                'gia' => 'Giá bán',
                                'so_luong_ton' => 'Tồn kho',
                                'luot_ban' => 'Bán chạy'
                            ];
                            if (isset($sorts[$filters['sort_by']])) {
                                $sortLabel = $sorts[$filters['sort_by']];
                            }
                            echo htmlspecialchars($sortLabel);
                            ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="sortDropdown">
                        <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('sort', 'ngay_tao', 'Mới nhất')">Mới nhất</a>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('sort', 'ten_sach', 'Tên A-Z')">Tên A-Z</a>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('sort', 'gia', 'Giá bán')">Giá bán</a>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('sort', 'so_luong_ton', 'Tồn kho')">Tồn kho</a>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('sort', 'luot_ban', 'Bán chạy')">Bán chạy</a>
                    </div>
                </div>
            </div>

            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary mr-2 flex-grow-1">
                    Lọc
                </button>
                <a href="index.php?page=books" class="btn btn-light border">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <form id="bulk-action-form" action="index.php?page=book_bulk_delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">



                    <table class="table table-custom table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center pl-4 col-checkbox">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="check-all">
                                        <label class="custom-control-label" for="check-all"></label>
                                    </div>
                                </th>
                                <th class="col-title">Tên sách</th>
                                <th>Danh mục</th>
                                <th>Giá bán</th>
                                <th>Tồn kho</th>
                                <th>Trạng thái</th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php if (!empty($books)): ?>
                                <?php foreach ($books as $book): ?>
                                    <tr class="clickable-row" data-href="index.php?page=book_edit&id=<?php echo $book['ma_sach']; ?>">
                                        <td class="text-center align-middle pl-4 no-click">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="book_ids[]" value="<?php echo $book['ma_sach']; ?>"
                                                    class="custom-control-input book-check" id="check-<?php echo $book['ma_sach']; ?>">
                                                <label class="custom-control-label" for="check-<?php echo $book['ma_sach']; ?>"></label>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="book-title"><?php echo htmlspecialchars($book['ten_sach']); ?></div>
                                            <div class="book-isbn"><i class="fas fa-barcode mr-1"></i><?php echo htmlspecialchars($book['isbn']); ?></div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-light border">
                                                <?php echo htmlspecialchars($book['ten_danh_muc'] ?? 'Chưa phân loại'); ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <?php if ($book['gia_goc'] > $book['gia']): ?>
                                                <div class="price-tag discount"><?php echo number_format($book['gia']); ?>đ</div>
                                                <div class="original-price"><?php echo number_format($book['gia_goc']); ?>đ</div>
                                            <?php else: ?>
                                                <div class="price-tag text-dark"><?php echo number_format($book['gia']); ?>đ</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php
                                            $stockClass = $book['so_luong_ton'] < 10 ? 'stock-low' : 'stock-good';
                                            $stockIcon = $book['so_luong_ton'] < 10 ? 'fa-exclamation-circle' : 'fa-check-circle';
                                            ?>
                                            <span class="stock-badge <?php echo $stockClass; ?>">
                                                <i class="fas <?php echo $stockIcon; ?> mr-1"></i>
                                                <?php echo number_format($book['so_luong_ton']); ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <?php
                                            $statusClass = '';
                                            switch ($book['tinh_trang']) {
                                                case 'Còn hàng':
                                                    $statusClass = 'badge-success';
                                                    break;
                                                case 'Hết hàng':
                                                    $statusClass = 'badge-warning';
                                                    break;
                                                case 'Ngừng kinh doanh':
                                                    $statusClass = 'badge-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-secondary';
                                            }
                                            ?>
                                            <span class="badge badge-pill-custom <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($book['tinh_trang']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-gray-500 mb-2"><i class="fas fa-book-open fa-3x"></i></div>
                                        <p>Không tìm thấy sách nào phù hợp</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>

            <!-- Pagination Footer -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="card-footer bg-white py-3 border-0">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link border-0" href="index.php?page=books&p=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($filters['search']); ?>&category=<?php echo $filters['category']; ?>&status=<?php echo urlencode($filters['status']); ?>&sort_by=<?php echo $filters['sort_by']; ?>&order=<?php echo $filters['order']; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            <?php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);

                            if ($start > 1) {
                                echo '<li class="page-item"><a class="page-link border-0" href="index.php?page=books&p=1">1</a></li>';
                                if ($start > 2) echo '<li class="page-item disabled"><span class="page-link border-0">...</span></li>';
                            }

                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?php echo $pagination['current_page'] == $i ? 'active' : ''; ?>">
                                    <a class="page-link border-0 rounded-circle mx-1" href="index.php?page=books&p=<?php echo $i; ?>&search=<?php echo urlencode($filters['search']); ?>&category=<?php echo $filters['category']; ?>&status=<?php echo urlencode($filters['status']); ?>&sort_by=<?php echo $filters['sort_by']; ?>&order=<?php echo $filters['order']; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php
                            if ($end < $pagination['total_pages']) {
                                if ($end < $pagination['total_pages'] - 1) echo '<li class="page-item disabled"><span class="page-link border-0">...</span></li>';
                                echo '<li class="page-item"><a class="page-link border-0" href="index.php?page=books&p=' . $pagination['total_pages'] . '">' . $pagination['total_pages'] . '</a></li>';
                            }
                            ?>

                            <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link border-0" href="index.php?page=books&p=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($filters['search']); ?>&category=<?php echo $filters['category']; ?>&status=<?php echo urlencode($filters['status']); ?>&sort_by=<?php echo $filters['sort_by']; ?>&order=<?php echo $filters['order']; ?>">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa sách</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa sách <strong id="delete-book-name"></strong>?<br>
                Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="index.php?page=book_delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="book_id" id="delete-book-id">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

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
        var checkboxes = document.querySelectorAll('.book-check');
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
                var allChecked = document.querySelectorAll('.book-check:checked').length === checkboxes.length;
                if (checkAll) checkAll.checked = allChecked;
                toggleBulkBtn();
            });
        });

        function toggleBulkBtn() {
            var count = document.querySelectorAll('.book-check:checked').length;
            if (bulkBtn) {
                if (count > 0) {
                    bulkBtn.classList.remove('d-none');
                    if (selectedCountSpan) selectedCountSpan.textContent = count;
                } else {
                    bulkBtn.classList.add('d-none');
                }
            }
        }
    });

    // Custom Dropdown Handling
    function selectOption(name, value, label) {
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