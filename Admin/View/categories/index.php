<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-folder-open"></i> Quản lý danh mục
        </h1>
        <div>
            <button id="bulk-delete-btn" class="btn btn-danger btn-icon-split shadow-sm mr-2 d-none">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Xóa (<span id="selected-count">0</span>)</span>
            </button>
            <a href="index.php?page=category_create" class="btn btn-primary btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Thêm danh mục</span>
            </a>
        </div>
    </div>

    <!-- Modern Filters Bar -->
    <div class="filter-bar mb-4">
        <form method="GET" action="index.php" class="row align-items-center">
            <input type="hidden" name="page" value="categories">

            <div class="col-md-8 mb-3 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-left">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control form-control-custom border-left-0"
                        id="search" name="search"
                        placeholder="Tìm kiếm danh mục..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
            </div>

            <div class="col-md-4 d-flex">
                <button type="submit" class="btn btn-primary mr-2 flex-grow-1">
                    Tìm kiếm
                </button>
                <a href="index.php?page=categories" class="btn btn-light border">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <form id="bulk-action-form" action="index.php?page=category_bulk_delete" method="POST">
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
                                <th style="width: 100px">Thứ tự</th>
                                <th style="width: 30%">Tên danh mục</th>
                                <th>Mô tả</th>
                                <th style="width: 120px">Số lượng sách</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr class="clickable-row" data-href="index.php?page=category_edit&id=<?php echo $cat['ma_danh_muc']; ?>">
                                        <td class="text-center align-middle pl-4 no-click">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ids[]" value="<?php echo $cat['ma_danh_muc']; ?>"
                                                    class="custom-control-input category-check" id="check-<?php echo $cat['ma_danh_muc']; ?>">
                                                <label class="custom-control-label" for="check-<?php echo $cat['ma_danh_muc']; ?>"></label>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-light border"><?php echo $cat['thu_tu']; ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="book-title">
                                                <i class="fas fa-folder text-primary mr-2"></i>
                                                <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-muted"><?php echo htmlspecialchars($cat['mo_ta']); ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-pill-custom badge-info">
                                                <i class="fas fa-book mr-1"></i>
                                                <?php echo $cat['book_count']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-gray-500 mb-2"><i class="fas fa-folder-open fa-3x"></i></div>
                                        <p>Chưa có danh mục nào</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa danh mục</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa danh mục <strong id="delete-cat-name"></strong>?<br>
                Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="index.php?page=category_delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="category_id" id="delete-cat-id">
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
        var checkboxes = document.querySelectorAll('.category-check');
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
                var allChecked = document.querySelectorAll('.category-check:checked').length === checkboxes.length;
                if (checkAll) checkAll.checked = allChecked;
                toggleBulkBtn();
            });
        });

        function toggleBulkBtn() {
            var count = document.querySelectorAll('.category-check:checked').length;
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
                var count = document.querySelectorAll('.category-check:checked').length;
                if (count > 0) {
                    if (confirm(`Bạn có chắc chắn muốn xóa ${count} danh mục đã chọn?`)) {
                        document.getElementById('bulk-action-form').submit();
                    }
                }
            });
        }
    });
</script>