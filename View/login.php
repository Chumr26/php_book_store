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
                        
                        <?php if (isset($debug_users) && !empty($debug_users)): ?>
                        <!-- Quick Login (Dev Mode) -->
                        <div class="card bg-light mb-4 border-info">
                            <div class="card-body py-3">
                                <strong class="text-info"><i class="fas fa-tools"></i> Quick Login (Dev Mode)</strong>
                                <div class="form-group mt-2 mb-0">
                                <select class="form-control" id="quickLoginSelect" onchange="autofillUser(this)">
                                    <option value="">-- Chọn tài khoản để tự động điền --</option>
                                    <?php foreach ($debug_users as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user['email']); ?>" 
                                                data-name="<?php echo htmlspecialchars($user['full_name']); ?>">
                                            <?php echo htmlspecialchars($user['full_name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <script>
                                function autofillUser(select) {
                                    if (select.value) {
                                        document.getElementById('emailInput').value = select.value;
                                        document.getElementById('passwordInput').value = '123456'; // Default seed password
                                        // Optional: Highlight that it was filled
                                        document.getElementById('emailInput').style.backgroundColor = '#e8f0fe';
                                        document.getElementById('passwordInput').style.backgroundColor = '#e8f0fe';
                                    } else {
                                        document.getElementById('emailInput').value = '';
                                        document.getElementById('passwordInput').value = '';
                                        document.getElementById('emailInput').style.backgroundColor = '';
                                        document.getElementById('passwordInput').style.backgroundColor = '';
                                    }
                                }
                            </script>
                            </div>
                        </div>
                        <?php endif; ?>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" id="emailInput" class="form-control" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Mật khẩu</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordInput" class="form-control" required>
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
                    
                    <div class="text-center mt-3">
                        Chưa có tài khoản? <a href="?page=register">Đăng ký ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
