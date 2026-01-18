<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-pen-nib"></i> Quản lý tác giả
        </h1>
        <div>
            <button id="bulk-delete-btn" class="btn btn-danger btn-icon-split shadow-sm mr-2 d-none">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Xóa (<span id="selected-count">0</span>)</span>
            </button>
            <a href="index.php?page=author_create" class="btn btn-primary btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Thêm tác giả</span>
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <form id="bulk-action-form" action="index.php?page=author_bulk_delete" method="POST">
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
                                <th style="width: 25%">Tên tác giả</th>
                                <th style="width: 20%">Bút danh</th>
                                <th>Quốc tịch</th>
                                <th style="width: 120px">SL sách</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($authors)): ?>
                                <?php foreach ($authors as $auth): ?>
                                    <tr class="clickable-row" data-href="index.php?page=author_edit&id=<?php echo $auth['ma_tac_gia']; ?>">
                                        <td class="text-center align-middle pl-4 no-click">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ids[]" value="<?php echo $auth['ma_tac_gia']; ?>"
                                                    class="custom-control-input author-check" id="check-<?php echo $auth['ma_tac_gia']; ?>">
                                                <label class="custom-control-label" for="check-<?php echo $auth['ma_tac_gia']; ?>"></label>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="author-name font-weight-bold text-primary">
                                                <?php echo htmlspecialchars($auth['ten_tac_gia'] ?? $auth['author_name'] ?? ''); ?>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <?php echo htmlspecialchars($auth['but_danh'] ?? $auth['pen_name'] ?? ''); ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php echo htmlspecialchars($auth['quoc_tich'] ?? $auth['nationality'] ?? ''); ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-pill-custom badge-info">
                                                <i class="fas fa-book mr-1"></i>
                                                <?php echo $auth['book_count'] ?? 0; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-gray-500 mb-2"><i class="fas fa-pen-nib fa-3x"></i></div>
                                        <p>Chưa có tác giả nào</p>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Row Click
        var rows = document.querySelectorAll('.clickable-row');
        rows.forEach(function(row) {
            row.addEventListener('click', function(e) {
                if (e.target.closest('.no-click') || e.target.closest('.custom-control')) return;
                var href = this.getAttribute('data-href');
                if (href) window.location.href = href;
            });
        });

        // Handle Check All
        var checkAll = document.getElementById('check-all');
        var checkboxes = document.querySelectorAll('.author-check');
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
                var allChecked = document.querySelectorAll('.author-check:checked').length === checkboxes.length;
                if (checkAll) checkAll.checked = allChecked;
                toggleBulkBtn();
            });
        });

        function toggleBulkBtn() {
            var count = document.querySelectorAll('.author-check:checked').length;
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
                var count = document.querySelectorAll('.author-check:checked').length;
                if (count > 0) {
                    if (typeof window.showConfirmModal === 'function') {
                        window.showConfirmModal(`Bạn có chắc chắn muốn xóa ${count} tác giả đã chọn?`, function() {
                            document.getElementById('bulk-action-form').submit();
                        });
                    }
                }
            });
        }
    });
</script>