<?php
$pageTitle = "Quên mật khẩu";
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <i class="fas fa-key fa-3x text-primary mb-3"></i>
                        <h2 class="h4 mb-2">Quên mật khẩu</h2>
                        <p class="text-muted">
                            Nhập địa chỉ email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu
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

                    <!-- Forgot Password Form -->
                    <form action="index.php?page=forgot_password" method="POST" id="forgotPasswordForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i> Địa chỉ Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Nhập email của bạn"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   required 
                                   autofocus>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block mt-4">
                            <i class="fas fa-paper-plane"></i> Gửi link đặt lại mật khẩu
                        </button>

                        <div class="text-center mt-4">
                            <p class="mb-2">
                                <a href="index.php?page=login" class="text-decoration-none">
                                    <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                                </a>
                            </p>
                            <p class="mb-0">
                                Chưa có tài khoản? 
                                <a href="index.php?page=register" class="font-weight-bold">Đăng ký ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Text -->
            <div class="card mt-3 bg-light border-0">
                <div class="card-body">
                    <h6 class="mb-2"><i class="fas fa-info-circle text-primary"></i> Lưu ý:</h6>
                    <ul class="mb-0 pl-4">
                        <li class="text-muted small">Link đặt lại mật khẩu có hiệu lực trong 24 giờ</li>
                        <li class="text-muted small">Kiểm tra cả hộp thư spam nếu không thấy email</li>
                        <li class="text-muted small">Liên hệ hỗ trợ nếu không nhận được email sau 10 phút</li>
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
</style>

<script>
$(document).ready(function() {
    $('#forgotPasswordForm').on('submit', function() {
        var $btn = $(this).find('button[type="submit"]');
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);
    });

    // Auto-hide alerts after 8 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 8000);
});
</script>
