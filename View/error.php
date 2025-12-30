<?php
$pageTitle = 'Error - BookStore';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Đã xảy ra lỗi</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-bug fa-5x text-danger opacity-50"></i>
                    </div>
                    <p class="lead text-center">Rất tiếc, đã có lỗi xảy ra trong quá trình xử lý yêu cầu của bạn.</p>
                    
                    <?php if (isset($viewData['error']) && !empty($viewData['error'])): ?>
                        <div class="alert alert-secondary mt-3">
                            <strong>Chi tiết lỗi:</strong>
                            <pre class="mt-2 mb-0 text-danger"><?php echo htmlspecialchars($viewData['error']); ?></pre>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">
                            <i class="fas fa-home"></i> Trở về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
