<?php
$pageTitle = "Đặt lại mật khẩu";
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <i class="fas fa-lock-open fa-3x text-primary mb-3"></i>
                        <h2 class="h4 mb-2">Đặt lại mật khẩu</h2>
                        <p class="text-muted">
                            Nhập mật khẩu mới cho tài khoản của bạn
                        </p>
                    </div>

                    <!-- Error/Success Messages -->
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>

                    <!-- Reset Password Form -->
                    <?php if (isset($validToken) && $validToken): ?>
                    <form action="index.php?page=reset_password" method="POST" id="resetPasswordForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                        
                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock"></i> Mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                                       required 
                                       autofocus
                                       minlength="6">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                            </div>
                            <!-- Password Strength Indicator -->
                            <div class="progress mt-2" style="height: 5px;">
                                <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small id="passwordStrengthText" class="form-text text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-lock"></i> Xác nhận mật khẩu <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       placeholder="Nhập lại mật khẩu mới"
                                       required
                                       minlength="6">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                                <?php endif; ?>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Mật khẩu phải có ít nhất 6 ký tự
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block mt-4">
                            <i class="fas fa-check"></i> Đặt lại mật khẩu
                        </button>

                        <div class="text-center mt-4">
                            <a href="index.php?page=login" class="text-decoration-none">
                                <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                            </a>
                        </div>
                    </form>
                    <?php else: ?>
                    <!-- Invalid/Expired Token -->
                    <div class="text-center">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p class="mb-0">Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.</p>
                        </div>
                        <a href="index.php?page=forgot_password" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Yêu cầu link mới
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Password Requirements -->
            <div class="card mt-3 bg-light border-0">
                <div class="card-body">
                    <h6 class="mb-2"><i class="fas fa-shield-alt text-primary"></i> Yêu cầu mật khẩu:</h6>
                    <ul class="mb-0 pl-4">
                        <li class="text-muted small">Tối thiểu 6 ký tự</li>
                        <li class="text-muted small">Nên bao gồm chữ hoa, chữ thường và số</li>
                        <li class="text-muted small">Tránh sử dụng thông tin cá nhân dễ đoán</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 15px;
    }
    .btn-primary {
        border-radius: 8px;
    }
    .form-control-lg {
        border-radius: 8px;
    }
    .progress-bar {
        transition: width 0.3s ease, background-color 0.3s ease;
    }
</style>

<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        var passwordField = $('#password');
        var icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#toggleConfirmPassword').on('click', function() {
        var passwordField = $('#confirm_password');
        var icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password strength indicator
    $('#password').on('keyup', function() {
        var password = $(this).val();
        var strength = 0;
        var strengthBar = $('#passwordStrength');
        var strengthText = $('#passwordStrengthText');

        if (password.length >= 6) strength += 25;
        if (password.length >= 8) strength += 15;
        if (/[a-z]/.test(password)) strength += 15;
        if (/[A-Z]/.test(password)) strength += 15;
        if (/[0-9]/.test(password)) strength += 15;
        if (/[^a-zA-Z0-9]/.test(password)) strength += 15;

        strengthBar.css('width', strength + '%');
        
        if (strength < 40) {
            strengthBar.removeClass().addClass('progress-bar bg-danger');
            strengthText.text('Yếu').removeClass().addClass('form-text text-danger');
        } else if (strength < 70) {
            strengthBar.removeClass().addClass('progress-bar bg-warning');
            strengthText.text('Trung bình').removeClass().addClass('form-text text-warning');
        } else {
            strengthBar.removeClass().addClass('progress-bar bg-success');
            strengthText.text('Mạnh').removeClass().addClass('form-text text-success');
        }
    });

    // Password match validation
    $('#confirm_password').on('keyup', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        }
    });

    // Form submission
    $('#resetPasswordForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        
        if (password !== confirmPassword) {
            e.preventDefault();
            $('#confirm_password').addClass('is-invalid');
            alert('Mật khẩu xác nhận không khớp!');
            return false;
        }
        
        var $btn = $(this).find('button[type="submit"]');
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);
    });

    // Auto-hide alerts after 8 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 8000);
});
</script>
