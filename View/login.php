<?php
$pageTitle = "Đăng nhập";

$prefillEmail = '';
if (class_exists('SessionHelper')) {
    $prefillEmail = SessionHelper::get('login_email', '');
    SessionHelper::remove('login_email');
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </h3>
                    
                    <form method="POST" action="?page=login" autocomplete="on">
                        
                        <?php if (isset($debug_users) && !empty($debug_users)): ?>
                        <!-- Quick Login (Dev Mode) -->
                        <div class="mb-4">
                            <div class="dropdown">
                                <button class="btn btn-outline-info btn-block dropdown-toggle" type="button" id="quickLoginDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bolt text-warning"></i> Quick Login (Dev Mode)
                                </button>
                                <div class="dropdown-menu w-100" aria-labelledby="quickLoginDropdown" style="max-height: 300px; overflow-y: auto;">
                                    <h6 class="dropdown-header">Select a test user to autofill:</h6>
                                    <div class="dropdown-divider"></div>
                                    <?php foreach ($debug_users as $user): ?>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" 
                                           onclick="devQuickLoginCustomer('<?php echo (int)($user['id_khachhang'] ?? 0); ?>', '<?php echo htmlspecialchars($user['email']); ?>'); return false;">
                                            <span><i class="fas fa-user-circle text-muted mr-2"></i> <?php echo htmlspecialchars($user['full_name']); ?></span>
                                            <small class="text-muted ml-2"><?php echo htmlspecialchars($user['email']); ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <script>
                                const devQuickLoginCsrfToken = '<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>';

                                function devQuickLoginCustomer(customerId, email) {
                                    if (!customerId) return;

                                    // Keep a nice visual cue about what's happening
                                    const btn = document.getElementById('quickLoginDropdown');
                                    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-warning"></i> Logging in: ' + (email || '...');
                                    btn.classList.remove('btn-outline-info');
                                    btn.classList.add('btn-info');

                                    const form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = '?page=dev_quick_login';

                                    const csrf = document.createElement('input');
                                    csrf.type = 'hidden';
                                    csrf.name = 'csrf_token';
                                    csrf.value = devQuickLoginCsrfToken;
                                    form.appendChild(csrf);

                                    const id = document.createElement('input');
                                    id.type = 'hidden';
                                    id.name = 'customer_id';
                                    id.value = customerId;
                                    form.appendChild(id);

                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            </script>
                        </div>
                        <?php endif; ?>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" id="emailInput" class="form-control" required 
                                   autocomplete="username"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Mật khẩu</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordInput" class="form-control" required autocomplete="current-password">
                                
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="transition: none !important;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <style>
                            /* Disable global button hover animation for eye icon */
                            #togglePassword:hover {
                                transform: none !important;
                                box-shadow: none !important;
                            }
                        </style>

                        <script>
                            document.getElementById('togglePassword').addEventListener('click', function (e) {
                                const passwordInput = document.getElementById('passwordInput');
                                const icon = this.querySelector('i');
                                
                                if (passwordInput.type === 'password') {
                                    passwordInput.type = 'text';
                                    icon.classList.remove('fa-eye');
                                    icon.classList.add('fa-eye-slash');
                                } else {
                                    passwordInput.type = 'password';
                                    icon.classList.remove('fa-eye-slash');
                                    icon.classList.add('fa-eye');
                                }
                            });
                        </script>
                        
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

                    <div class="mt-3">
                        <p class="text-muted small mb-2">Chưa nhận được email xác minh?</p>
                        <form method="POST" action="?page=resend_verification">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" required
                                       placeholder="Nhập email để gửi lại"
                                       value="<?php echo htmlspecialchars($prefillEmail ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-block">
                                Gửi lại email xác minh
                            </button>
                        </form>
                    </div>
                    
                    <div class="text-center mt-3">
                        Chưa có tài khoản? <a href="?page=register">Đăng ký ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
