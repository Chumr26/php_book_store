<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý danh mục</h1>
        <a href="index.php?page=category_create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Thêm danh mục
        </a>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách danh mục</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 80px">Thứ tự</th>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th>Số lượng sách</th>
                            <th style="width: 150px">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="text-center"><?php echo $cat['thu_tu']; ?></td>
                                <td class="font-weight-bold text-primary"><?php echo htmlspecialchars($cat['ten_danh_muc']); ?></td>
                                <td><?php echo htmlspecialchars($cat['mo_ta']); ?></td>
                                <td class="text-center">
                                    <span class="badge badge-secondary badge-pill"><?php echo $cat['book_count']; ?></span>
                                </td>
                                <td>
                                    <a href="index.php?page=category_edit&id=<?php echo $cat['ma_danh_muc']; ?>" class="btn btn-info btn-sm" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($cat['book_count'] == 0): ?>
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                            data-id="<?php echo $cat['ma_danh_muc']; ?>" 
                                            data-name="<?php echo htmlspecialchars($cat['ten_danh_muc']); ?>"
                                            title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-secondary btn-sm" disabled title="Không thể xóa danh mục đang có sách">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">Chưa có danh mục nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
    // Handle Delete Modal
    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#delete-cat-id').val(id);
        $('#delete-cat-name').text(name);
        $('#deleteModal').modal('show');
    });
</script>
