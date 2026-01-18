<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa nhà xuất bản</h1>
        <form action="index.php?page=publisher_delete" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa NXB này?');">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="id" value="<?php echo $publisher['ma_nxb'] ?? $publisher['id_publisher']; ?>">
            <button type="submit" class="btn btn-danger btn-icon-split shadow-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Xóa NXB</span>
            </button>
        </form>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin nhà xuất bản</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?page=publisher_edit&id=<?php echo $publisher['ma_nxb'] ?? $publisher['id_publisher']; ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="id_nxb" value="<?php echo $publisher['ma_nxb'] ?? $publisher['id_publisher']; ?>">

                        <div class="form-group">
                            <label for="ten_nxb">Tên nhà xuất bản <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_nxb" name="ten_nxb"
                                value="<?php echo htmlspecialchars($publisher['ten_nxb'] ?? $publisher['publisher_name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="dien_thoai">Điện thoại</label>
                                <input type="text" class="form-control" id="dien_thoai" name="dien_thoai"
                                    value="<?php echo htmlspecialchars($publisher['dien_thoai'] ?? $publisher['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($publisher['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" class="form-control" id="website" name="website"
                                value="<?php echo htmlspecialchars($publisher['website'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="dia_chi">Địa chỉ</label>
                            <textarea class="form-control" id="dia_chi" name="dia_chi" rows="3"><?php echo htmlspecialchars($publisher['dia_chi'] ?? $publisher['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="index.php?page=publishers" class="btn btn-secondary mr-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>