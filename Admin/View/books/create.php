<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm sách mới</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin sách</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?page=book_create" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="ten_sach">Tên sách <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ten_sach" name="ten_sach" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="isbn">ISBN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="isbn" name="isbn" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="ma_tac_gia">Tác giả <span class="text-danger">*</span></label>
                                <select class="form-control" id="ma_tac_gia" name="ma_tac_gia" required>
                                    <option value="">-- Chọn tác giả --</option>
                                    <?php foreach ($authors as $author): ?>
                                        <option value="<?php echo $author['ma_tac_gia']; ?>">
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
                                        <option value="<?php echo $pub['ma_nha_xuat_ban']; ?>">
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
                                        <option value="<?php echo $cat['ma_danh_muc']; ?>">
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
                                    <input type="number" class="form-control" id="gia" name="gia" min="0" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="gia_goc">Giá gốc (để hiển thị giảm giá)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="gia_goc" name="gia_goc" min="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="so_luong_ton">Số lượng tồn <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="so_luong_ton" name="so_luong_ton" min="0" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="so_trang">Số trang</label>
                                <input type="number" class="form-control" id="so_trang" name="so_trang" min="1">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="nam_xuat_ban">Năm xuất bản</label>
                                <input type="number" class="form-control" id="nam_xuat_ban" name="nam_xuat_ban" min="1900" max="<?php echo date('Y'); ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="ngon_ngu">Ngôn ngữ</label>
                                <input type="text" class="form-control" id="ngon_ngu" name="ngon_ngu" value="Tiếng Việt">
                            </div>
                             <div class="form-group col-md-3">
                                <label for="tinh_trang">Trạng thái</label>
                                <select class="form-control" id="tinh_trang" name="tinh_trang">
                                    <option value="Còn hàng">Còn hàng</option>
                                    <option value="Hết hàng">Hết hàng</option>
                                    <option value="Ngừng kinh doanh">Ngừng kinh doanh</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả sách</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tu_khoa">Từ khóa (cách nhau bởi dấu phẩy)</label>
                            <input type="text" class="form-control" id="tu_khoa" name="tu_khoa" placeholder="VD: kinh tế, khởi nghiệp, marketing">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="anh_bia">Ảnh bìa</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="anh_bia" name="anh_bia" accept="image/*" onchange="previewImage(this)">
                                    <label class="custom-file-label" for="anh_bia">Chọn file ảnh...</label>
                                </div>
                                <small class="form-text text-muted">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 5MB.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <img id="preview" src="#" alt="Preview" style="display: none; max-height: 200px; max-width: 100%; border: 1px solid #ddd; padding: 5px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="noi_bat" name="noi_bat" value="1">
                                <label class="custom-control-label" for="noi_bat">Sách nổi bật (Hiển thị trên trang chủ)</label>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu sách
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
