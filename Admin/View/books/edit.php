<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa thông tin sách</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin sách: <?php echo htmlspecialchars($book['ten_sach']); ?></h6>
                </div>
                <div class="card-body">
                    <form action="index.php?page=book_edit" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="ma_sach" value="<?php echo $book['ma_sach']; ?>">
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="ten_sach">Tên sách <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ten_sach" name="ten_sach" 
                                       value="<?php echo htmlspecialchars($book['ten_sach']); ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="isbn">ISBN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="isbn" name="isbn" 
                                       value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="ma_tac_gia">Tác giả <span class="text-danger">*</span></label>
                                <select class="form-control" id="ma_tac_gia" name="ma_tac_gia" required>
                                    <option value="">-- Chọn tác giả --</option>
                                    <?php foreach ($authors as $author): ?>
                                        <option value="<?php echo $author['ma_tac_gia']; ?>"
                                            <?php echo $book['ma_tac_gia'] == $author['ma_tac_gia'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($author['ten_tac_gia']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ma_nha_xuat_ban">Nhà xuất bản <span class="text-danger">*</span></label>
                                <select class="form-control" id="ma_nha_xuat_ban" name="ma_nha_xuat_ban" required>
                                    <option value="">-- Chọn nhà xuất bản --</option>
                                    <?php foreach ($publishers as $pub): ?>
                                        <option value="<?php echo $pub['ma_nha_xuat_ban']; ?>"
                                            <?php echo $book['ma_nha_xuat_ban'] == $pub['ma_nha_xuat_ban'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pub['ten_nha_xuat_ban']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ma_danh_muc">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-control" id="ma_danh_muc" name="ma_danh_muc" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['ma_danh_muc']; ?>"
                                            <?php echo $book['ma_danh_muc'] == $cat['ma_danh_muc'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="gia">Giá bán <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="gia" name="gia" min="0" 
                                           value="<?php echo $book['gia']; ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="gia_goc">Giá gốc (để hiển thị giảm giá)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="gia_goc" name="gia_goc" min="0"
                                           value="<?php echo $book['gia_goc']; ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="so_luong_ton">Số lượng tồn <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="so_luong_ton" name="so_luong_ton" min="0" 
                                       value="<?php echo $book['so_luong_ton']; ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="so_trang">Số trang</label>
                                <input type="number" class="form-control" id="so_trang" name="so_trang" min="1"
                                       value="<?php echo $book['so_trang']; ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="nam_xuat_ban">Năm xuất bản</label>
                                <input type="number" class="form-control" id="nam_xuat_ban" name="nam_xuat_ban" 
                                       min="1900" max="<?php echo date('Y'); ?>" value="<?php echo $book['nam_xuat_ban']; ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="ngon_ngu">Ngôn ngữ</label>
                                <input type="text" class="form-control" id="ngon_ngu" name="ngon_ngu" 
                                       value="<?php echo htmlspecialchars($book['ngon_ngu']); ?>">
                            </div>
                             <div class="form-group col-md-3">
                                <label for="tinh_trang">Trạng thái</label>
                                <select class="form-control" id="tinh_trang" name="tinh_trang">
                                    <option value="Còn hàng" <?php echo $book['tinh_trang'] == 'Còn hàng' ? 'selected' : ''; ?>>Còn hàng</option>
                                    <option value="Hết hàng" <?php echo $book['tinh_trang'] == 'Hết hàng' ? 'selected' : ''; ?>>Hết hàng</option>
                                    <option value="Ngừng kinh doanh" <?php echo $book['tinh_trang'] == 'Ngừng kinh doanh' ? 'selected' : ''; ?>>Ngừng kinh doanh</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả sách</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5"><?php echo htmlspecialchars($book['mo_ta']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tu_khoa">Từ khóa (cách nhau bởi dấu phẩy)</label>
                            <input type="text" class="form-control" id="tu_khoa" name="tu_khoa" 
                                   value="<?php echo htmlspecialchars($book['tu_khoa']); ?>"
                                   placeholder="VD: kinh tế, khởi nghiệp, marketing">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="anh_bia">Ảnh bìa</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="anh_bia" name="anh_bia" accept="image/*" onchange="previewImage(this)">
                                    <label class="custom-file-label" for="anh_bia">Chọn file ảnh mới (nếu muốn đổi)...</label>
                                </div>
                                <small class="form-text text-muted">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 5MB.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <?php if ($book['anh_bia']): ?>
                                    <p>Ảnh hiện tại:</p>
                                    <img src="<?php echo BASE_URL . $book['anh_bia']; ?>" alt="Cover" style="height: 150px; border: 1px solid #ddd; padding: 5px;">
                                <?php endif; ?>
                                <img id="preview" src="#" alt="Preview" style="display: none; max-height: 200px; max-width: 100%; border: 1px solid #ddd; padding: 5px; margin-top: 10px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="noi_bat" name="noi_bat" value="1" 
                                    <?php echo $book['noi_bat'] ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="noi_bat">Sách nổi bật (Hiển thị trên trang chủ)</label>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="index.php?page=books" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy bỏ
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update file input label with selected filename
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    // Image preview
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result);
                $('#preview').show();
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
