<!-- Reusable book card component -->
<?php
// This file is included from other views, expects $book variable to be set
if (!isset($book)) return;

require_once __DIR__ . '/helpers/cover.php';

$coverUrl = book_cover_url($book['isbn'] ?? null, 'medium');
?>

<div class="book-card">
    <div class="card h-100 shadow-sm">
        <!-- Book Image -->
        <div class="book-image-wrapper position-relative">
            <a href="?page=book_detail&id=<?php echo $book['ma_sach']; ?>">
                <img src="<?php echo htmlspecialchars($coverUrl); ?>" 
                     class="card-img-top" 
                     alt="<?php echo htmlspecialchars($book['ten_sach']); ?>"
                     loading="lazy" decoding="async"
                     onerror="this.onerror=null;this.src='/book_store/Content/images/books/no-image.jpg';"
                     style="height: 340px; object-fit: contain; background: #fff;">
            </a>
            
            <!-- Stock badge -->
            <?php if (isset($book['so_luong_ton']) && $book['so_luong_ton'] <= 0): ?>
                <span class="badge badge-danger position-absolute" style="top: 10px; left: 10px;">
                    Hết hàng
                </span>
            <?php elseif (isset($book['so_luong_ton']) && $book['so_luong_ton'] <= 10): ?>
                <span class="badge badge-warning position-absolute" style="top: 10px; left: 10px;">
                    Sắp hết
                </span>
            <?php endif; ?>
            
            <!-- Discount badge -->
            <?php if (isset($book['giam_gia']) && $book['giam_gia'] > 0): ?>
                <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">
                    -<?php echo $book['giam_gia']; ?>%
                </span>
            <?php endif; ?>
            
            <!-- New badge -->
            <?php 
            $createdDate = isset($book['ngay_tao']) ? strtotime($book['ngay_tao']) : 0;
            $daysDiff = (time() - $createdDate) / (60 * 60 * 24);
            if ($daysDiff <= 30): 
            ?>
                <span class="badge badge-success position-absolute" style="top: 40px; left: 10px;">
                    Mới
                </span>
            <?php endif; ?>
        </div>
        
        <div class="card-body d-flex flex-column">
            <!-- Book Title -->
            <h5 class="card-title">
                <a href="?page=book_detail&id=<?php echo $book['ma_sach']; ?>" 
                   class="text-decoration-none text-dark" 
                   title="<?php echo htmlspecialchars($book['ten_sach']); ?>">
                    <?php 
                    $title = htmlspecialchars($book['ten_sach']);
                    echo mb_strlen($title) > 50 ? mb_substr($title, 0, 50) . '...' : $title;
                    ?>
                </a>
            </h5>
            
            <!-- Author -->
            <?php if (isset($book['ten_tac_gia'])): ?>
                <p class="text-muted small mb-2">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($book['ten_tac_gia']); ?>
                </p>
            <?php endif; ?>
            
            <!-- Rating -->
            <div class="book-rating mb-2">
                <?php 
                $rating = isset($book['diem_trung_binh']) ? floatval($book['diem_trung_binh']) : 0;
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                $emptyStars = 5 - $fullStars - $halfStar;
                ?>
                <span class="text-warning">
                    <?php for ($i = 0; $i < $fullStars; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                    <?php if ($halfStar): ?>
                        <i class="fas fa-star-half-alt"></i>
                    <?php endif; ?>
                    <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                        <i class="far fa-star"></i>
                    <?php endfor; ?>
                </span>
                <small class="text-muted">
                    (<?php echo isset($book['so_luong_danh_gia']) ? $book['so_luong_danh_gia'] : 0; ?>)
                </small>
            </div>
            
            <!-- Price -->
            <div class="book-price mt-auto">
                <?php if (isset($book['giam_gia']) && $book['giam_gia'] > 0): ?>
                    <span class="text-muted" style="text-decoration: line-through;">
                        <?php echo number_format($book['gia'], 0, ',', '.'); ?>đ
                    </span>
                    <br>
                    <?php 
                    $discountedPrice = $book['gia'] * (1 - $book['giam_gia'] / 100);
                    ?>
                    <span class="text-danger font-weight-bold h5 mb-0">
                        <?php echo number_format($discountedPrice, 0, ',', '.'); ?>đ
                    </span>
                <?php else: ?>
                    <span class="text-danger font-weight-bold h5 mb-0">
                        <?php echo number_format($book['gia'], 0, ',', '.'); ?>đ
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- Add to Cart Button -->
            <div class="mt-3">
                <?php if (isset($book['so_luong_ton']) && $book['so_luong_ton'] > 0): ?>
                    <button type="button" 
                            class="btn btn-primary btn-block add-to-cart-btn"
                            data-book-id="<?php echo $book['ma_sach']; ?>"
                            data-book-name="<?php echo htmlspecialchars($book['ten_sach']); ?>">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-block" disabled>
                        <i class="fas fa-times"></i> Hết hàng
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .book-card .card {
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
    }
    
    .book-card .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
        border-color: #007bff;
    }
    
    .book-image-wrapper {
        overflow: hidden;
        background-color: #f8f9fa;
    }
    
    .book-image-wrapper img {
        transition: transform 0.3s ease;
    }
    
    .book-card .card:hover .book-image-wrapper img {
        transform: scale(1.05);
    }
    
    .card-title a:hover {
        color: #007bff !important;
    }
</style>

<script>
$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart-btn').click(function() {
        const bookId = $(this).data('book-id');
        const bookName = $(this).data('book-name');
        const button = $(this);
        
        // Disable button and show loading
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang thêm...');
        
        $.ajax({
            url: '?page=add_to_cart',
            method: 'POST',
            data: {
                book_id: bookId,
                quantity: 1,
                csrf_token: '<?php echo $_SESSION["csrf_token"] ?? ""; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update cart badge
                    $('#cartBadge').text(response.data.item_count);
                    
                    // Show success message
                    button.html('<i class="fas fa-check"></i> Đã thêm');
                    button.removeClass('btn-primary').addClass('btn-success');
                    
                    // Show toast notification
                    showToast('success', 'Đã thêm "' + bookName + '" vào giỏ hàng!');
                    
                    // Reset button after 2 seconds
                    setTimeout(function() {
                        button.prop('disabled', false)
                              .html('<i class="fas fa-cart-plus"></i> Thêm vào giỏ')
                              .removeClass('btn-success').addClass('btn-primary');
                    }, 2000);
                } else {
                    showToast('error', response.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                    button.prop('disabled', false)
                          .html('<i class="fas fa-cart-plus"></i> Thêm vào giỏ');
                }
            },
            error: function() {
                showToast('error', 'Có lỗi xảy ra, vui lòng thử lại!');
                button.prop('disabled', false)
                      .html('<i class="fas fa-cart-plus"></i> Thêm vào giỏ');
            }
        });
    });
    
    // Toast notification function
    function showToast(type, message) {
        const bgColor = type === 'success' ? '#28a745' : '#dc3545';
        const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        
        const toast = $(`
            <div class="toast-notification" style="
                position: fixed;
                top: 100px;
                right: 20px;
                background: ${bgColor};
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 9999;
                animation: slideIn 0.3s ease;
            ">
                <i class="fas fa-${icon} mr-2"></i> ${message}
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(function() {
            toast.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>

<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
