<?php
$pageTitle = $pageTitle ?? 'Hồ sơ';
$admin = $admin ?? [];

$fullName = $admin['ten_admin'] ?? $admin['full_name'] ?? ($_SESSION['admin_name'] ?? '');
$username = $admin['username'] ?? ($_SESSION['admin_username'] ?? '');
$email = $admin['email'] ?? ($_SESSION['admin_email'] ?? '');
$role = $admin['quyen'] ?? $admin['role'] ?? ($_SESSION['admin_role'] ?? 'admin');
?>

<div class="container-fluid py-3">
    <h3 class="mb-3"><i class="fas fa-user"></i> Hồ sơ</h3>

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
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="full_name">Họ tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Quyền</label>
                            <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars($role); ?>" readonly>
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
