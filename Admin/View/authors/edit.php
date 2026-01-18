<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa tác giả</h1>
        <form action="index.php?page=author_delete" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tác giả này?');">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="id" value="<?php echo $author['ma_tac_gia'] ?? $author['id_author']; ?>">
            <button type="submit" class="btn btn-danger btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Xóa tác giả</span>
            </button>
        </form>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin tác giả</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?page=author_edit&id=<?php echo $author['ma_tac_gia'] ?? $author['id_author']; ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="id_tacgia" value="<?php echo $author['ma_tac_gia'] ?? $author['id_author']; ?>">

                        <div class="form-group">
                            <label for="ten_tacgia">Tên tác giả <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_tacgia" name="ten_tacgia"
                                value="<?php echo htmlspecialchars($author['ten_tac_gia'] ?? $author['author_name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="but_danh">Bút danh</label>
                                <input type="text" class="form-control" id="but_danh" name="but_danh"
                                    value="<?php echo htmlspecialchars($author['but_danh'] ?? $author['pen_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="quoc_tich">Quốc tịch</label>
                                <input type="text" class="form-control" id="quoc_tich" name="quoc_tich"
                                    value="<?php echo htmlspecialchars($author['quoc_tich'] ?? $author['nationality'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ngay_sinh">Ngày sinh</label>
                            <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh"
                                value="<?php echo $author['ngay_sinh'] ?? $author['date_of_birth'] ?? ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="tieu_su">Tiểu sử</label>
                            <textarea class="form-control" id="tieu_su" name="tieu_su" rows="5"><?php echo htmlspecialchars($author['tieu_su'] ?? $author['biography'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="index.php?page=authors" class="btn btn-secondary mr-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>