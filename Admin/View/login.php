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
