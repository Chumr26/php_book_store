<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building"></i> Quản lý nhà xuất bản
        </h1>
        <div>
            <button id="bulk-delete-btn" class="btn btn-danger btn-icon-split shadow-sm mr-2 d-none">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Xóa (<span id="selected-count">0</span>)</span>
            </button>
            <a href="index.php?page=publisher_create" class="btn btn-primary btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Thêm NXB</span>
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <form id="bulk-action-form" action="index.php?page=publisher_bulk_delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <table class="table table-custom table-hover mb-0 publishers-table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 40px" class="text-center pl-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="check-all">
                                        <label class="custom-control-label" for="check-all"></label>
                                    </div>
                                </th>
                                <th class="col-publisher-name">Tên nhà xuất bản</th>
                                <th style="width: 400px;">Địa chỉ</th>
                                <th>Điện thoại</th>
                                <th style="width: 120px">SL sách</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($publishers)): ?>
                                <?php foreach ($publishers as $pub): ?>
                                    <tr class="clickable-row" data-href="index.php?page=publisher_edit&id=<?php echo $pub['ma_nxb']; ?>">
                                        <td class="text-center align-middle pl-4 no-click">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ids[]" value="<?php echo $pub['ma_nxb']; ?>"
                                                    class="custom-control-input pub-check" id="check-<?php echo $pub['ma_nxb']; ?>">
                                                <label class="custom-control-label" for="check-<?php echo $pub['ma_nxb']; ?>"></label>
                                            </div>
                                        </td>
                                        <td class="align-middle col-publisher-name">
                                            <div class="publisher-name font-weight-bold text-primary">
                                                <?php echo htmlspecialchars($pub['ten_nxb'] ?? $pub['publisher_name'] ?? ''); ?>
                                            </div>
                                            <?php if (!empty($pub['website'])): ?>
                                                <small class="text-muted"><i class="fas fa-globe mr-1"></i><?php echo htmlspecialchars($pub['website']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php echo htmlspecialchars($pub['dia_chi'] ?? $pub['address'] ?? ''); ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php echo htmlspecialchars($pub['dien_thoai'] ?? $pub['phone'] ?? ''); ?>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-pill-custom badge-info">
                                                <i class="fas fa-book mr-1"></i>
                                                <?php echo $pub['book_count'] ?? 0; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-gray-500 mb-2"><i class="fas fa-building fa-3x"></i></div>
                                        <p>Chưa có nhà xuất bản nào</p>
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
        var checkboxes = document.querySelectorAll('.pub-check');
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
                var allChecked = document.querySelectorAll('.pub-check:checked').length === checkboxes.length;
                if (checkAll) checkAll.checked = allChecked;
                toggleBulkBtn();
            });
        });

        function toggleBulkBtn() {
            var count = document.querySelectorAll('.pub-check:checked').length;
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
                var count = document.querySelectorAll('.pub-check:checked').length;
                if (count > 0) {
                    if (typeof window.showConfirmModal === 'function') {
                        window.showConfirmModal(`Bạn có chắc chắn muốn xóa ${count} nhà xuất bản đã chọn?`, function() {
                            document.getElementById('bulk-action-form').submit();
                        });
                    }
                }
            });
        }
    });
</script>