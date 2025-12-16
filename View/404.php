<?php
$pageTitle = "Lỗi 404 - Không tìm thấy trang";
?>

<div class="container">
    <div class="error-page text-center py-5">
        <div class="error-icon mb-4">
            <i class="fas fa-exclamation-triangle fa-5x text-warning"></i>
        </div>
        
        <h1 class="display-1 font-weight-bold text-primary">404</h1>
        <h2 class="mb-4">Oops! Không tìm thấy trang</h2>
        <p class="lead text-muted mb-4">
            Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.
        </p>
        
        <div class="error-actions">
            <a href="?page=home" class="btn btn-primary btn-lg mr-2">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
            <a href="?page=books" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-book"></i> Xem sách
            </a>
        </div>
    </div>
</div>

<style>
.error-page {
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>
