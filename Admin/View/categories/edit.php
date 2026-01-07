<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-modern">
            <li class="breadcrumb-item"><a href="index.php?page=categories"><i class="fas fa-folder-open"></i> Quản lý danh mục</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sửa danh mục</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 page-header-modern">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> Sửa danh mục
        </h1>
        <a href="index.php?page=categories" class="btn btn-secondary btn-icon-split shadow-sm">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Quay lại danh sách</span>
        </a>
    </div>

    <!-- Main Form -->
    <form action="index.php?page=category_edit" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="ma_danh_muc" value="<?php echo $category['ma_danh_muc']; ?>">

        <!-- Category Information Section -->
        <div class="form-section-card">
            <h5><i class="fas fa-info-circle mr-2"></i>Thông tin danh mục</h5>

            <div class="form-group">
                <label for="ten_danh_muc">Tên danh mục <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-folder text-gray-400"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="ten_danh_muc" name="ten_danh_muc"
                        value="<?php echo htmlspecialchars($category['ten_danh_muc']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-align-left text-gray-400"></i>
                        </span>
                    </div>
                    <textarea class="form-control" id="mo_ta" name="mo_ta" rows="3"
                        placeholder="Nhập mô tả cho danh mục..."><?php echo htmlspecialchars($category['mo_ta']); ?></textarea>
                </div>
                <small class="form-text text-muted">Mô tả sẽ giúp khách hàng hiểu rõ hơn về danh mục này</small>
            </div>

            <div class="form-group">
                <label for="thu_tu">Thứ tự hiển thị</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-sort-numeric-down text-gray-400"></i>
                        </span>
                    </div>
                    <input type="number" class="form-control" id="thu_tu" name="thu_tu" min="0"
                        value="<?php echo $category['thu_tu']; ?>">
                </div>
                <small class="form-text text-muted">Số nhỏ hơn sẽ hiển thị trước</small>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="form-action-bar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button type="submit" class="btn btn-primary btn-icon-split shadow-sm mr-2">
                        <span class="icon text-white-50">
                            <i class="fas fa-save"></i>
                        </span>
                        <span class="text">Cập nhật</span>
                    </button>
                    <a href="index.php?page=categories" class="btn btn-secondary btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fas fa-times"></i>
                        </span>
                        <span class="text">Hủy bỏ</span>
                    </a>
                </div>
                <!-- Delete Button -->
                <button type="button" class="btn btn-danger btn-icon-split" data-toggle="modal" data-target="#deleteModalInEdit">
                    <span class="icon text-white-50">
                        <i class="fas fa-trash"></i>
                    </span>
                    <span class="text">Xóa danh mục</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModalInEdit" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa danh mục</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa danh mục <strong><?php echo htmlspecialchars($category['ten_danh_muc']); ?></strong>?<br>
                Hành động này không thể hoàn tác.
                <br><small class="text-danger">* Lưu ý: Không thể xóa danh mục nếu đang có sách.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="index.php?page=category_delete" method="POST" style="margin:0;">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="category_id" value="<?php echo $category['ma_danh_muc']; ?>">
                    <button type="submit" class="btn btn-danger">Xóa ngay</button>
                </form>
            </div>
        </div>
    </div>
</div>