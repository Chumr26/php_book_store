<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm mã giảm giá mới</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="index.php?page=admin_coupon_create" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="row">
                    <!-- Column 1 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ma_code">Mã Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ma_code" name="ma_code" required
                                style="text-transform: uppercase;" placeholder="VD: SALE2025">
                            <small class="form-text text-muted">Mã phải là duy nhất, viết liền không dấu.</small>
                        </div>

                        <div class="form-group">
                            <label for="ten_chuongtrinh">Tên chương trình <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_chuongtrinh" name="ten_chuongtrinh" required
                                placeholder="VD: Khuyến mãi Tết Dương Lịch">
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="loai_giam">Loại giảm giá</label>
                                <select class="form-control" id="loai_giam" name="loai_giam">
                                    <option value="percent">Phần trăm (%)</option>
                                    <option value="fixed">Số tiền cố định (VNĐ)</option>
                                    <option value="free_shipping">Miễn phí vận chuyển</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="gia_tri_giam">Giá trị giảm <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="gia_tri_giam" name="gia_tri_giam" required min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="gia_tri_toi_thieu">Đơn hàng tối thiểu</label>
                                <input type="number" class="form-control" id="gia_tri_toi_thieu" name="gia_tri_toi_thieu" value="0" min="0">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="giam_toi_da">Giảm tối đa (đối với %)</label>
                                <input type="number" class="form-control" id="giam_toi_da" name="giam_toi_da" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="so_luong">Số lượng mã <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="so_luong" name="so_luong" required value="100" min="1">
                        </div>

                        <div class="form-group">
                            <label for="ngay_bat_dau">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" required>
                        </div>

                        <div class="form-group">
                            <label for="ngay_ket_thuc">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
                        </div>

                        <div class="form-group">
                            <label for="trang_thai">Trạng thái</label>
                            <select class="form-control" id="trang_thai" name="trang_thai">
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="index.php?page=admin_coupons" class="btn btn-secondary mr-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu mã giảm giá</button>
                </div>
            </form>
        </div>
    </div>
</div>