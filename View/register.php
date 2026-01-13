<?php
$pageTitle = "Đăng ký";

$old = SessionHelper::get('registration_data', []);
SessionHelper::remove('registration_data');

$oldFullName = htmlspecialchars($old['full_name'] ?? '', ENT_QUOTES, 'UTF-8');
$oldEmail = htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8');
$oldPassword = htmlspecialchars($old['password'] ?? '', ENT_QUOTES, 'UTF-8');
$oldConfirmPassword = htmlspecialchars($old['confirm_password'] ?? '', ENT_QUOTES, 'UTF-8');
$oldPhone = htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8');
$oldAddress = htmlspecialchars($old['address'] ?? '', ENT_QUOTES, 'UTF-8');
$oldGender = $old['gender'] ?? '';
$oldAgreeTerms = !empty($old['agree_terms']);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-user-plus"></i> Đăng ký tài khoản
                    </h3>
                    
                    <form method="POST" action="?page=register">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" value="<?php echo $oldFullName; ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="<?php echo $oldEmail; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" value="<?php echo $oldPassword; ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $oldConfirmPassword; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo $oldPhone; ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Giới tính</label>
                                    <select name="gender" class="form-control">
                                        <option value="Nam" <?php echo ($oldGender === 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                        <option value="Nữ" <?php echo ($oldGender === 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                        <option value="Khác" <?php echo ($oldGender === 'Khác') ? 'selected' : ''; ?>>Khác</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo $oldAddress; ?></textarea>
                        </div>
                        
                        <div class="form-group form-check">
                            <input type="checkbox" name="agree_terms" class="form-check-input" id="agreeTerms" <?php echo $oldAgreeTerms ? 'checked' : ''; ?> required>
                            <label class="form-check-label" for="agreeTerms">
                                Tôi đồng ý với <a href="#">Điều khoản sử dụng</a> và <a href="#">Chính sách bảo mật</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-user-plus"></i> Đăng ký
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        Đã có tài khoản? <a href="?page=login">Đăng nhập ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
