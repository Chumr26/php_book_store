<?php
$pageTitle = "Đăng nhập";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </h3>
                    
                    <form method="POST" action="?page=login">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" class="form-control" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="form-group form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <a href="?page=forgot_password" class="text-muted">Quên mật khẩu?</a>
                    </div>
                    
                    <div class="text-center mt-3">
                        Chưa có tài khoản? <a href="?page=register">Đăng ký ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
