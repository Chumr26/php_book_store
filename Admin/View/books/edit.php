<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-modern">
            <li class="breadcrumb-item"><a href="index.php?page=books"><i class="fas fa-book"></i> Quản lý sách</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sửa thông tin</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 page-header-modern">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i>
            Sửa thông tin sách
        </h1>
        <a href="index.php?page=books" class="btn btn-secondary btn-icon-split shadow-sm">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Quay lại danh sách</span>
        </a>
    </div>

    <!-- Main Form -->
    <form action="index.php?page=book_edit" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="ma_sach" value="<?php echo $book['ma_sach']; ?>">

        <!-- Basic Information Section -->
        <div class="form-section-card">
            <h5><i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản</h5>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="ten_sach">Tên sách <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-book text-gray-400"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="ten_sach" name="ten_sach"
                            value="<?php echo htmlspecialchars($book['ten_sach']); ?>" required>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="isbn">ISBN <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-barcode text-gray-400"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="isbn" name="isbn"
                            value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="ma_tac_gia">Tác giả <span class="text-danger">*</span></label>
                    <div class="dropdown">
                        <input type="hidden" name="ma_tac_gia" id="ma_tac_gia" value="<?php echo $book['ma_tac_gia'] ?? ''; ?>" required>
                        <button class="btn admin-dropdown-toggle" type="button" id="authorDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-value">
                                <?php
                                $authorName = '-- Chọn tác giả --';
                                if (!empty($book['ma_tac_gia'])) {
                                    foreach ($authors as $author) {
                                        if ($author['ma_tac_gia'] == $book['ma_tac_gia']) {
                                            $authorName = $author['ten_tac_gia'];
                                            break;
                                        }
                                    }
                                }
                                echo htmlspecialchars($authorName);
                                ?>
                            </span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="authorDropdown">
                            <div class="admin-dropdown-menu-scrollable">
                                <a class="dropdown-item" href="javascript:void(0)" data-value="" onclick="selectEditOption('ma_tac_gia', '', '-- Chọn tác giả --')">-- Chọn tác giả --</a>
                                <?php foreach ($authors as $author): ?>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                        data-value="<?php echo $author['ma_tac_gia']; ?>"
                                        onclick="selectEditOption('ma_tac_gia', '<?php echo $author['ma_tac_gia']; ?>', '<?php echo htmlspecialchars($author['ten_tac_gia']); ?>')">
                                        <?php echo htmlspecialchars($author['ten_tac_gia']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="ma_nha_xuat_ban">Nhà xuất bản <span class="text-danger">*</span></label>
                    <div class="dropdown">
                        <input type="hidden" name="ma_nha_xuat_ban" id="ma_nha_xuat_ban" value="<?php echo $book['ma_nha_xuat_ban'] ?? ''; ?>" required>
                        <button class="btn admin-dropdown-toggle" type="button" id="publisherDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-value">
                                <?php
                                $publisherName = '-- Chọn nhà xuất bản --';
                                if (!empty($book['ma_nha_xuat_ban'])) {
                                    foreach ($publishers as $pub) {
                                        if ($pub['ma_nha_xuat_ban'] == $book['ma_nha_xuat_ban']) {
                                            $publisherName = $pub['ten_nha_xuat_ban'];
                                            break;
                                        }
                                    }
                                }
                                echo htmlspecialchars($publisherName);
                                ?>
                            </span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="publisherDropdown">
                            <div class="admin-dropdown-menu-scrollable">
                                <a class="dropdown-item" href="javascript:void(0)" data-value="" onclick="selectEditOption('ma_nha_xuat_ban', '', '-- Chọn nhà xuất bản --')">-- Chọn nhà xuất bản --</a>
                                <?php foreach ($publishers as $pub): ?>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                        data-value="<?php echo $pub['ma_nha_xuat_ban']; ?>"
                                        onclick="selectEditOption('ma_nha_xuat_ban', '<?php echo $pub['ma_nha_xuat_ban']; ?>', '<?php echo htmlspecialchars($pub['ten_nha_xuat_ban']); ?>')">
                                        <?php echo htmlspecialchars($pub['ten_nha_xuat_ban']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="ma_danh_muc">Danh mục <span class="text-danger">*</span></label>
                    <div class="dropdown">
                        <input type="hidden" name="ma_danh_muc" id="ma_danh_muc" value="<?php echo $book['ma_danh_muc']; ?>" required>
                        <button class="btn admin-dropdown-toggle" type="button" id="categoryDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-value">
                                <?php
                                $categoryName = '-- Chọn danh mục --';
                                foreach ($categories as $cat) {
                                    if ($cat['ma_danh_muc'] == $book['ma_danh_muc']) {
                                        $categoryName = $cat['ten_danh_muc'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($categoryName);
                                ?>
                            </span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="categoryDropdown">
                            <div class="admin-dropdown-menu-scrollable">
                                <a class="dropdown-item" href="javascript:void(0)" data-value="" onclick="selectEditOption('ma_danh_muc', '', '-- Chọn danh mục --')">-- Chọn danh mục --</a>
                                <?php foreach ($categories as $cat): ?>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                        data-value="<?php echo $cat['ma_danh_muc']; ?>"
                                        onclick="selectEditOption('ma_danh_muc', '<?php echo $cat['ma_danh_muc']; ?>', '<?php echo htmlspecialchars($cat['ten_danh_muc']); ?>')">
                                        <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing & Inventory Section -->
        <div class="form-section-card">
            <h5><i class="fas fa-dollar-sign mr-2"></i>Giá & Kho hàng</h5>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="gia">Giá bán <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-tag text-gray-400"></i>
                            </span>
                        </div>
                        <input type="number" class="form-control" id="gia" name="gia" min="0"
                            value="<?php echo $book['gia']; ?>" required>
                        <div class="input-group-append">
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="gia_goc">Giá gốc <small class="text-muted">(để hiển thị giảm giá)</small></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-money-bill-wave text-gray-400"></i>
                            </span>
                        </div>
                        <input type="number" class="form-control" id="gia_goc" name="gia_goc" min="0"
                            value="<?php echo $book['gia_goc']; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="so_luong_ton">Số lượng tồn <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-boxes text-gray-400"></i>
                            </span>
                        </div>
                        <input type="number" class="form-control" id="so_luong_ton" name="so_luong_ton" min="0"
                            value="<?php echo $book['so_luong_ton']; ?>" required>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="tinh_trang">Trạng thái</label>
                    <div class="dropdown">
                        <input type="hidden" name="tinh_trang" id="tinh_trang" value="<?php echo $book['tinh_trang']; ?>">
                        <button class="btn admin-dropdown-toggle" type="button" id="statusDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-value"><?php echo htmlspecialchars($book['tinh_trang']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="statusDropdown">
                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectEditOption('tinh_trang', 'Còn hàng', 'Còn hàng')">Còn hàng</a>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectEditOption('tinh_trang', 'Hết hàng', 'Hết hàng')">Hết hàng</a>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectEditOption('tinh_trang', 'Ngừng kinh doanh', 'Ngừng kinh doanh')">Ngừng kinh doanh</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">

            </div>
        </div>

        <!-- Publishing Details Section -->
        <div class="form-section-card">
            <h5><i class="fas fa-file-alt mr-2"></i>Thông tin xuất bản</h5>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="so_trang">Số trang</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-file text-gray-400"></i>
                            </span>
                        </div>
                        <input type="number" class="form-control" id="so_trang" name="so_trang" min="1"
                            value="<?php echo $book['so_trang'] ?? ''; ?>">
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="nam_xuat_ban">Năm xuất bản</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </span>
                        </div>
                        <input type="number" class="form-control" id="nam_xuat_ban" name="nam_xuat_ban"
                            min="1900" max="<?php echo date('Y'); ?>" value="<?php echo $book['nam_xuat_ban'] ?? ''; ?>">
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="ngon_ngu">Ngôn ngữ</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-language text-gray-400"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="ngon_ngu" name="ngon_ngu"
                            value="<?php echo htmlspecialchars($book['ngon_ngu'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="form-section-card">
            <h5><i class="fas fa-align-left mr-2"></i>Nội dung</h5>
            <div class="form-group">
                <label for="mo_ta">Mô tả sách</label>
                <textarea class="form-control" id="mo_ta" name="mo_ta" rows="5"
                    placeholder="Nhập mô tả chi tiết về sách..."><?php echo htmlspecialchars($book['mo_ta'] ?? ''); ?></textarea>
                <small class="form-text text-muted">Mô tả sẽ hiển thị trên trang chi tiết sách</small>
            </div>

            <div class="form-group">
                <label for="tu_khoa">Từ khóa</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-tags text-gray-400"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="tu_khoa" name="tu_khoa"
                        value="<?php echo htmlspecialchars($book['tu_khoa'] ?? ''); ?>"
                        placeholder="VD: kinh tế, khởi nghiệp, marketing">
                </div>
                <small class="form-text text-muted">Các từ khóa cách nhau bởi dấu phẩy</small>
            </div>
        </div>

        <!-- Media Section -->
        <div class="form-section-card">
            <h5><i class="fas fa-image mr-2"></i>Ảnh bìa</h5>

            <?php
            // Include helper for API covers
            require_once BASE_PATH . 'View/helpers/cover.php';

            $imagePath = $book['anh_bia'] ?? '';
            $displayUrl = '';
            $isLocal = false;

            // 1. Check for valid local file
            if ($imagePath) {
                if (file_exists(BASE_PATH . $imagePath) && is_file(BASE_PATH . $imagePath)) {
                    $displayUrl = BASE_URL . $imagePath;
                    $isLocal = true;
                } elseif (file_exists(BASE_PATH . 'Content/images/books/' . $imagePath) && is_file(BASE_PATH . 'Content/images/books/' . $imagePath)) {
                    $displayUrl = BASE_URL . 'Content/images/books/' . $imagePath;
                    $isLocal = true;
                }
            }

            // 2. Fallback to API if no local file found
            if (!$isLocal) {
                $displayUrl = book_cover_url($book['isbn'] ?? '', 'medium');
            }
            ?>

            <div class="row align-items-start">
                <!-- Current Book Cover Preview -->
                <div class="col-md-4">
                    <div class="book-cover-preview-wrapper">
                        <label class="d-block mb-2"><strong>Ảnh hiện tại</strong></label>
                        <div class="book-cover-frame">
                            <img src="<?php echo $displayUrl; ?>" alt="Book Cover" class="img-fluid">
                        </div>

                        <?php if ($isLocal): ?>
                            <div class="mt-2 text-success">
                                <small><i class="fas fa-check-circle"></i> Ảnh đã upload</small>
                            </div>
                        <?php else: ?>
                            <div class="mt-2 text-info">
                                <small><i class="fas fa-cloud"></i> Sử dụng ảnh từ API</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upload Section -->
                <div class="col-md-8">
                    <div class="upload-section">
                        <label for="anh_bia"><strong>Tải lên ảnh mới</strong></label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="anh_bia" name="anh_bia"
                                accept="image/*" onchange="previewImage(this)">
                            <label class="custom-file-label" for="anh_bia">Chọn file ảnh...</label>
                        </div>
                        <small class="form-text text-muted d-block mb-3">
                            <i class="fas fa-info-circle"></i> Định dạng: JPG, PNG, GIF. Kích thước tối đa: 5MB
                        </small>

                        <!-- New Image Preview -->
                        <div id="preview-wrapper" class="book-cover-preview-wrapper d-none">
                            <label class="d-block mb-2"><strong>Xem trước ảnh mới</strong></label>
                            <div class="book-cover-frame">
                                <img id="preview" src="#" alt="Preview" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Advanced Options Section -->
        <div class="form-section-card">
            <h5><i class="fas fa-cog mr-2"></i>Tùy chọn nâng cao</h5>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="noi_bat" name="noi_bat" value="1"
                    <?php echo $book['noi_bat'] ? 'checked' : ''; ?>>
                <label class="custom-control-label" for="noi_bat">
                    <i class="fas fa-star text-warning mr-1"></i>
                    Sách nổi bật (Hiển thị trên trang chủ)
                </label>
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
                        <span class="text">Cập nhật thông tin</span>
                    </button>
                    <a href="index.php?page=books" class="btn btn-secondary btn-icon-split">
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
                    <span class="text">Xóa sách này</span>
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
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa sách</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa sách <strong><?php echo htmlspecialchars($book['ten_sach']); ?></strong>?<br>
                Hành động này không thể hoàn tác.
                <div class="alert alert-warning mt-2">
                    <small><i class="fas fa-exclamation-triangle mr-1"></i> Lưu ý: Nếu sách đã có đơn hàng/đánh giá, hệ thống sẽ chuyển sang trạng thái "Ngừng kinh doanh".</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="index.php?page=book_delete" method="POST" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="book_id" value="<?php echo $book['ma_sach']; ?>">
                    <button type="submit" class="btn btn-danger">Xóa ngay</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update file input label with selected filename
        var fileInput = document.getElementById('anh_bia');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                var fileName = e.target.value.split("\\").pop();
                var label = e.target.nextElementSibling;
                if (label && label.classList.contains('custom-file-label')) {
                    label.classList.add("selected");
                    label.innerHTML = fileName || 'Chọn file ảnh mới...';
                }
            });
        }
    });

    // Image preview
    function previewImage(input) {
        var previewWrapper = document.getElementById('preview-wrapper');
        var preview = document.getElementById('preview');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                if (preview) {
                    preview.src = e.target.result;
                    if (previewWrapper) {
                        previewWrapper.classList.remove('d-none');
                        previewWrapper.style.display = 'block'; // Fallback if d-none override needed, but class removal should suffice if d-none is !important? No d-none is simple display:none.
                        // Wait, d-none has !important in bootstrap. So removing it is correct.
                        // But I need to make sure I don't use inline style display:block if d-none is removed.
                        // Actually, just removing d-none is enough if the element is div (block).
                        previewWrapper.style.display = '';
                    }
                }
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            if (previewWrapper) {
                previewWrapper.classList.add('d-none');
            }
        }
    }

    // Custom Dropdown Handling for Edit Page
    function selectEditOption(fieldName, value, label) {
        // Update hidden input
        document.getElementById(fieldName).value = value;
        // Update button text
        var dropdownId = '';
        if (fieldName === 'ma_tac_gia') dropdownId = 'authorDropdown';
        else if (fieldName === 'ma_nha_xuat_ban') dropdownId = 'publisherDropdown';
        else if (fieldName === 'ma_danh_muc') dropdownId = 'categoryDropdown';
        else if (fieldName === 'tinh_trang') dropdownId = 'statusDropdown';

        var btn = document.getElementById(dropdownId);
        if (btn) {
            btn.querySelector('.text-value').textContent = label;
            // Close the dropdown using Bootstrap's dropdown method
            $(btn).dropdown('toggle');
        }
    }
</script>