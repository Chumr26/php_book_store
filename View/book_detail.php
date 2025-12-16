<?php
$pageTitle = isset($book) ? htmlspecialchars($book['ten_sach']) : 'Chi tiết sách';
?>

<div class="container mt-4">
    <?php if (isset($book)): ?>
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=home">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="?page=books">Sách</a></li>
            <?php if (isset($book['ten_danh_muc'])): ?>
                <li class="breadcrumb-item"><a href="?page=books&category=<?php echo $book['ma_danh_muc']; ?>"><?php echo htmlspecialchars($book['ten_danh_muc']); ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($book['ten_sach']); ?></li>
        </ol>
    </nav>
    
    <!-- Book Detail -->
    <div class="row">
        <!-- Book Image -->
        <div class="col-md-4 mb-4">
            <div class="book-image-detail sticky-top" style="top: 160px;">
                <img src="<?php echo htmlspecialchars($book['hinh_anh']); ?>" 
                     alt="<?php echo htmlspecialchars($book['ten_sach']); ?>"
                     class="img-fluid rounded shadow">
            </div>
        </div>
        
        <!-- Book Info -->
        <div class="col-md-8">
            <h1 class="book-title mb-3"><?php echo htmlspecialchars($book['ten_sach']); ?></h1>
            
            <!-- Rating & Reviews -->
            <div class="book-rating mb-3">
                <?php 
                $rating = floatval($book['diem_trung_binh'] ?? 0);
                for ($i = 1; $i <= 5; $i++): 
                    echo $i <= $rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                endfor;
                ?>
                <span class="ml-2"><?php echo number_format($rating, 1); ?> / 5.0</span>
                <span class="text-muted ml-2">(<?php echo $book['so_luong_danh_gia'] ?? 0; ?> đánh giá)</span>
            </div>
            
            <!-- Price -->
            <div class="book-price mb-4">
                <h2 class="text-danger font-weight-bold">
                    <?php echo number_format($book['gia'], 0, ',', '.'); ?>đ
                </h2>
            </div>
            
            <!-- Book Info Table -->
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="30%">Tác giả</th>
                    <td><?php echo htmlspecialchars($book['ten_tac_gia'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Nhà xuất bản</th>
                    <td><?php echo htmlspecialchars($book['ten_nxb'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Năm xuất bản</th>
                    <td><?php echo htmlspecialchars($book['nam_xuat_ban'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Số trang</th>
                    <td><?php echo htmlspecialchars($book['so_trang'] ?? 'N/A'); ?> trang</td>
                </tr>
                <tr>
                    <th>ISBN</th>
                    <td><?php echo htmlspecialchars($book['isbn'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Danh mục</th>
                    <td>
                        <a href="?page=books&category=<?php echo $book['ma_danh_muc']; ?>">
                            <?php echo htmlspecialchars($book['ten_danh_muc'] ?? 'N/A'); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Tình trạng</th>
                    <td>
                        <?php if ($book['so_luong_ton'] > 10): ?>
                            <span class="badge badge-success">Còn hàng</span>
                        <?php elseif ($book['so_luong_ton'] > 0): ?>
                            <span class="badge badge-warning">Sắp hết (còn <?php echo $book['so_luong_ton']; ?> cuốn)</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Hết hàng</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <!-- Add to Cart Form -->
            <?php if ($book['so_luong_ton'] > 0): ?>
            <form id="addToCartForm" class="mb-4">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label>Số lượng:</label>
                        <input type="number" 
                               class="form-control" 
                               name="quantity" 
                               id="quantity"
                               value="1" 
                               min="1" 
                               max="<?php echo $book['so_luong_ton']; ?>">
                    </div>
                    <div class="col-md-9">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-lg ml-2">
                            <i class="far fa-heart"></i> Yêu thích
                        </button>
                    </div>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Sản phẩm hiện đang hết hàng
            </div>
            <?php endif; ?>
            
            <!-- Description -->
            <div class="book-description">
                <h4>Mô tả sản phẩm</h4>
                <div class="content">
                    <?php echo nl2br(htmlspecialchars($book['mo_ta'] ?? 'Chưa có mô tả')); ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="reviews-section mt-5">
        <h3 class="mb-4">Đánh giá sản phẩm</h3>
        
        <!-- Write Review Form (for logged-in users) -->
        <?php if (isset($_SESSION['customer_id'])): ?>
        <div class="write-review mb-4">
            <h5>Viết đánh giá của bạn</h5>
            <form id="reviewForm">
                <input type="hidden" name="book_id" value="<?php echo $book['ma_sach']; ?>">
                <div class="form-group">
                    <label>Đánh giá:</label>
                    <div class="rating-input">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                    <input type="hidden" name="rating" id="ratingValue" required>
                </div>
                <div class="form-group">
                    <label>Nhận xét:</label>
                    <textarea name="comment" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
            </form>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            Vui lòng <a href="?page=login">đăng nhập</a> để viết đánh giá
        </div>
        <?php endif; ?>
        
        <!-- Reviews List -->
        <?php if (isset($reviews) && !empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
            <div class="review-item">
                <div class="review-header">
                    <strong><?php echo htmlspecialchars($review['ten_khach_hang']); ?></strong>
                    <span class="ml-3">
                        <?php for ($i = 1; $i <= 5; $i++): 
                            echo $i <= $review['diem'] ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                        endfor; ?>
                    </span>
                    <small class="text-muted ml-3"><?php echo date('d/m/Y', strtotime($review['ngay_danh_gia'])); ?></small>
                </div>
                <p><?php echo nl2br(htmlspecialchars($review['noi_dung'])); ?></p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <p class="text-muted">Chưa có đánh giá nào</p>
        <?php endif; ?>
    </div>
    
    <?php else: ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> Không tìm thấy sản phẩm
    </div>
    <?php endif; ?>
</div>

<style>
.book-title { font-size: 28px; font-weight: 700; color: #333; }
.review-item { border-bottom: 1px solid #ddd; padding: 15px 0; }
.rating-input i { font-size: 24px; cursor: pointer; margin-right: 5px; color: #ccc; }
.rating-input i.active { color: #ffc107; }
</style>

<script>
$(document).ready(function() {
    // Rating input
    $('.rating-input i').click(function() {
        const rating = $(this).data('rating');
        $('#ratingValue').val(rating);
        $('.rating-input i').removeClass('fas active').addClass('far');
        for (let i = 1; i <= rating; i++) {
            $(`.rating-input i[data-rating="${i}"]`).removeClass('far').addClass('fas active');
        }
    });
    
    // Add to cart
    $('#addToCartForm').submit(function(e) {
        e.preventDefault();
        const quantity = $('#quantity').val();
        $.post('?page=add_to_cart', {
            book_id: <?php echo $book['ma_sach'] ?? 0; ?>,
            quantity: quantity,
            csrf_token: '<?php echo $_SESSION["csrf_token"] ?? ""; ?>'
        }, function(response) {
            if (response.success) {
                alert('Đã thêm vào giỏ hàng!');
                $('#cartBadge').text(response.data.item_count);
            } else {
                alert(response.message || 'Có lỗi xảy ra');
            }
        }, 'json');
    });
    
    // Submit review
    $('#reviewForm').submit(function(e) {
        e.preventDefault();
        $.post('?page=ajax_submit_review', $(this).serialize() + '&csrf_token=<?php echo $_SESSION["csrf_token"] ?? ""; ?>', function(response) {
            if (response.success) {
                alert('Cảm ơn bạn đã đánh giá!');
                location.reload();
            } else {
                alert(response.message || 'Có lỗi xảy ra');
            }
        }, 'json');
    });
});
</script>
