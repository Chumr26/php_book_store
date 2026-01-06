<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý danh mục</h1>
        <div>
            <button id="btn-delete-selected" class="btn btn-sm btn-danger shadow-sm mr-2" disabled>
                <i class="fas fa-trash fa-sm text-white-50"></i> Xóa đã chọn
            </button>
            <a href="index.php?page=category_create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Thêm danh mục
            </a>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách danh mục</h6>
        </div>
        <div class="card-body">
            <form id="bulk-delete-form" action="index.php?page=category_bulk_delete" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 40px" class="text-center">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th style="width: 80px">Thứ tự</th>
                                <th>Tên danh mục</th>
                                <th>Mô tả</th>
                                <th>Số lượng sách</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr class="clickable-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="ids[]" value="<?php echo $cat['ma_danh_muc']; ?>" class="item-checkbox">
                                        </td>
                                        <td class="text-center"><?php echo $cat['thu_tu']; ?></td>
                                        <td class="font-weight-bold text-primary">
                                            <a href="index.php?page=category_edit&id=<?php echo $cat['ma_danh_muc']; ?>">
                                                <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($cat['mo_ta']); ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary badge-pill"><?php echo $cat['book_count']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">Chưa có danh mục nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
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
                    if (confirm(`Bạn có chắc chắn muốn xóa ${checkedCount} danh mục đã chọn?`)) {
                        form.submit();
                    }
                }
            });
        }
    });
</script>