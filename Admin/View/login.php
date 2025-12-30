<?php
// Admin Login View
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="font-weight-light my-0"><i class="fas fa-user-shield"></i> Admin Login</h3>
                </div>
                <div class="card-body p-5">
                    <?php if (isset($debug_users) && !empty($debug_users)): ?>
                    <div class="mb-4">
                        <div class="dropdown">
                            <button class="btn btn-outline-danger btn-block dropdown-toggle" type="button" id="adminQuickLogin" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-shield-alt text-danger"></i> Quick Admin Login (Dev)
                            </button>
                            <div class="dropdown-menu w-100" aria-labelledby="adminQuickLogin">
                                <h6 class="dropdown-header">Select an admin account:</h6>
                                <div class="dropdown-divider"></div>
                                <?php foreach ($debug_users as $user): ?>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" 
                                       onclick="autofillAdmin('<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['password']); ?>', '<?php echo htmlspecialchars($user['full_name']); ?>'); return false;">
                                        <span><i class="fas fa-user-tie text-muted mr-2"></i> <?php echo htmlspecialchars($user['full_name']); ?></span>
                                        <small class="text-muted ml-2">@<?php echo htmlspecialchars($user['username']); ?></small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <script>
                            function autofillAdmin(username, password, name) {
                                document.getElementById('username').value = username;
                                document.getElementById('password').value = password;
                                
                                // Visual feedback
                                const btn = document.getElementById('adminQuickLogin');
                                btn.innerHTML = '<i class="fas fa-check"></i> Selected: ' + name;
                                btn.classList.remove('btn-outline-danger');
                                btn.classList.add('btn-danger');
                                
                                // Highlight inputs
                                document.getElementById('username').style.backgroundColor = '#f8d7da';
                                document.getElementById('password').style.backgroundColor = '#f8d7da';
                                setTimeout(() => {
                                    document.getElementById('username').style.backgroundColor = '';
                                    document.getElementById('password').style.backgroundColor = '';
                                }, 1000);
                            }
                        </script>
                    </div>
                    <?php endif; ?>
                    <form method="POST" action="?page=login">
                        <input type="hidden" name="csrf_token" value="<?php echo SessionHelper::generateCSRFToken(); ?>">
                        
                        <div class="form-group">
                            <label class="small mb-1" for="username">Username / Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input class="form-control" id="username" name="username" type="text" placeholder="Enter admin username" required />
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="small mb-1" for="password">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input class="form-control" id="password" name="password" type="password" placeholder="Enter password" required />
                            </div>
                        </div>
                        
                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small" href="../index.php">Back to Store</a>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small"><a href="#">Forgot Password?</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
