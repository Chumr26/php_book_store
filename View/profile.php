<?php
$pageTitle = $pageTitle ?? 'Thông tin cá nhân';
$customer = $customer ?? [];

$fullName = $customer['ten_khachhang'] ?? $customer['full_name'] ?? '';
$email = $customer['email'] ?? '';
$phone = $customer['dien_thoai'] ?? $customer['phone'] ?? '';
$address = $customer['dia_chi'] ?? $customer['address'] ?? '';
$dateOfBirth = $customer['ngay_sinh'] ?? $customer['date_of_birth'] ?? '';
$gender = $customer['gioi_tinh'] ?? $customer['gender'] ?? '';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-user"></i> Thông tin cá nhân</h2>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <strong>Cập nhật thông tin</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="?page=profile">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? SessionHelper::generateCSRFToken()); ?>">
                        <input type="hidden" name="form_type" value="profile">

                        <div class="form-group">
                            <label for="full_name">Họ tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                            <small class="form-text text-muted">Email hiện chưa hỗ trợ thay đổi.</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="phone">Số điện thoại</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="0xxxxxxxxx">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="date_of_birth">Ngày sinh</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($dateOfBirth); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="gender">Giới tính</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="" <?php echo ($gender === '' ? 'selected' : ''); ?>>-- Chọn --</option>
                                <option value="Nam" <?php echo ($gender === 'Nam' ? 'selected' : ''); ?>>Nam</option>
                                <option value="Nữ" <?php echo ($gender === 'Nữ' ? 'selected' : ''); ?>>Nữ</option>
                                <option value="Khác" <?php echo ($gender === 'Khác' ? 'selected' : ''); ?>>Khác</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Nhập địa chỉ"><?php echo htmlspecialchars($address); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <strong>Đổi mật khẩu</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="?page=profile">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? SessionHelper::generateCSRFToken()); ?>">
                        <input type="hidden" name="form_type" value="password">

                        <div class="form-group">
                            <label for="current_password">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <small class="form-text text-muted">Tối thiểu 8 ký tự, gồm chữ và số.</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Cập nhật mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
