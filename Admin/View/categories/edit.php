<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa danh mục</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin danh mục</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?page=category_edit" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="ma_danh_muc" value="<?php echo $category['ma_danh_muc']; ?>">
                        
                        <div class="form-group">
                            <label for="ten_danh_muc">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_danh_muc" name="ten_danh_muc" 
                                   value="<?php echo htmlspecialchars($category['ten_danh_muc']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta" rows="3"><?php echo htmlspecialchars($category['mo_ta']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="thu_tu">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" id="thu_tu" name="thu_tu" min="0" 
                                   value="<?php echo $category['thu_tu']; ?>">
                            <small class="form-text text-muted">Số nhỏ hơn sẽ hiển thị trước.</small>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cập nhật
                                </button>
                                <a href="index.php?page=categories" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Hủy bỏ
                                </a>
                            </div>
                             <!-- Delete Button -->
                             <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModalInEdit">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    </form>

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
                </div>
            </div>
        </div>
    </div>
</div>
