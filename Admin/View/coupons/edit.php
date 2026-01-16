<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cập nhật mã giảm giá</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="index.php?page=admin_coupon_edit" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="id_magiamgia" value="<?php echo $coupon['id_magiamgia']; ?>">

                <div class="row">
                    <!-- Column 1 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ma_code">Mã Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ma_code" name="ma_code" required
                                style="text-transform: uppercase;" value="<?php echo htmlspecialchars($coupon['ma_code']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="ten_chuongtrinh">Tên chương trình <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_chuongtrinh" name="ten_chuongtrinh" required
                                value="<?php echo htmlspecialchars($coupon['ten_chuongtrinh']); ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="loai_giam">Loại giảm giá</label>
                                <select class="form-control" id="loai_giam" name="loai_giam">
                                    <option value="percent" <?php echo $coupon['loai_giam'] == 'percent' ? 'selected' : ''; ?>>Phần trăm (%)</option>
                                    <option value="fixed" <?php echo $coupon['loai_giam'] == 'fixed' ? 'selected' : ''; ?>>Số tiền cố định (VNĐ)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="gia_tri_giam">Giá trị giảm <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="gia_tri_giam" name="gia_tri_giam" required min="0"
                                    value="<?php echo $coupon['gia_tri_giam']; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="gia_tri_toi_thieu">Đơn hàng tối thiểu</label>
                                <input type="number" class="form-control" id="gia_tri_toi_thieu" name="gia_tri_toi_thieu" min="0"
                                    value="<?php echo $coupon['gia_tri_toi_thieu']; ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="giam_toi_da">Giảm tối đa (đối với %)</label>
                                <input type="number" class="form-control" id="giam_toi_da" name="giam_toi_da" min="0"
                                    value="<?php echo $coupon['giam_toi_da']; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="so_luong">Số lượng mã <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="so_luong" name="so_luong" required min="1"
                                value="<?php echo $coupon['so_luong']; ?>">
                            <small class="text-muted">Đã sử dụng: <?php echo $coupon['da_su_dung']; ?></small>
                        </div>

                        <div class="form-group">
                            <label for="ngay_bat_dau">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <?php
                            $ngayBatDau = $coupon['ngay_bat_dau'];
                            $timestamp = strtotime($ngayBatDau);
                            $dateValue = ($timestamp && $ngayBatDau !== '0000-00-00 00:00:00') ? date('Y-m-d\TH:i', $timestamp) : date('Y-m-d\TH:i');
                            ?>
                            <input type="datetime-local" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" required
                                value="<?php echo $dateValue; ?>">
                        </div>

                        <div class="form-group">
                            <label for="ngay_ket_thuc">Ngày kết thúc <span class="text-danger">*</span></label>
                            <?php
                            $ngayKetThuc = $coupon['ngay_ket_thuc'];
                            $timestamp = strtotime($ngayKetThuc);
                            $dateValue = ($timestamp && $ngayKetThuc !== '0000-00-00 00:00:00') ? date('Y-m-d\TH:i', $timestamp) : date('Y-m-d\TH:i', strtotime('+1 month'));
                            ?>
                            <input type="datetime-local" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" required
                                value="<?php echo $dateValue; ?>">
                        </div>

                        <div class="form-group">
                            <label for="trang_thai">Trạng thái</label>
                            <select class="form-control" id="trang_thai" name="trang_thai">
                                <option value="active" <?php echo $coupon['trang_thai'] == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo $coupon['trang_thai'] == 'inactive' ? 'selected' : ''; ?>>Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">Xóa mã này</button>
                    <div>
                        <a href="index.php?page=admin_coupons" class="btn btn-secondary mr-2">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa mã giảm giá <strong><?php echo htmlspecialchars($coupon['ma_code']); ?></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="index.php?page=admin_coupon_delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="id" value="<?php echo $coupon['id_magiamgia']; ?>">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>