<div class="container-fluid">
    <div class="text-center">
        <div class="error mx-auto" data-text="ERROR">LỖI</div>
        <p class="lead text-gray-800 mb-5">Đã xảy ra lỗi hệ thống</p>
        <p class="text-gray-500 mb-0"><?php echo htmlspecialchars($error ?? 'Đã xảy ra lỗi không xác định.'); ?></p>
        <a href="index.php?page=dashboard" class="btn btn-primary mt-3">&larr; Quay lại Dashboard</a>
    </div>
</div>
