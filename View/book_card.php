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
                    style="height: 420px; object-fit: cover; background: #fff;">
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
                <span class="badge badge-success position-absolute" style="top: 10px; left: 10px;">
                    Mới
                </span>
            <?php endif; ?>
        </div>

        <div class="card-body d-flex flex-column">
            <div class="book-info-main">
                <!-- Book Title -->
                <h5 class="card-title text-line-clamp-2 mb-2" style="height: 44px; overflow: hidden;">
                    <a href="?page=book_detail&id=<?php echo $book['ma_sach']; ?>"
                        class="text-decoration-none text-dark"
                        title="<?php echo htmlspecialchars($book['ten_sach']); ?>">
                        <?php echo htmlspecialchars($book['ten_sach']); ?>
                    </a>
                </h5>

                <!-- Author -->
                <?php if (isset($book['ten_tac_gia']) || isset($book['author_name'])): ?>
                    <p class="text-muted small mb-2 text-truncate">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($book['ten_tac_gia'] ?? $book['author_name']); ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted small mb-2 invisible">
                        <i class="fas fa-user"></i> Unknown
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

                <!-- Extra Info for List View (Hidden in Grid) -->
                <div class="book-extra-info d-none mt-3">
                    <?php if (isset($book['mo_ta'])): ?>
                        <p class="text-muted small text-line-clamp-2 mb-2">
                            <?php echo htmlspecialchars(strip_tags($book['mo_ta'])); ?>
                        </p>
                    <?php endif; ?>

                    <div class="small text-muted mb-1">
                        <?php if (isset($book['ten_nxb']) || isset($book['publisher_name'])): ?>
                            <span class="mr-3"><i class="fas fa-building"></i> <?php echo htmlspecialchars($book['ten_nxb'] ?? $book['publisher_name']); ?></span>
                        <?php endif; ?>

                        <?php if (isset($book['ten_theloai']) || isset($book['category_name'])): ?>
                            <span><i class="fas fa-bookmark"></i> <?php echo htmlspecialchars($book['ten_theloai'] ?? $book['category_name']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Price & Actions -->
            <div class="book-actions mt-auto">
                <!-- Price -->
                <div class="book-price mb-3">
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
                <?php if (isset($book['so_luong_ton']) && $book['so_luong_ton'] > 0): ?>
                    <?php
                    $isInCart = false;
                    if (isset($GLOBALS['globalCartBookIds']) && in_array($book['ma_sach'], $GLOBALS['globalCartBookIds'])) {
                        $isInCart = true;
                    } elseif (isset($globalCartBookIds) && in_array($book['ma_sach'], $globalCartBookIds)) {
                        $isInCart = true;
                    }
                    ?>

                    <?php if ($isInCart): ?>
                        <button type="button"
                            class="btn btn-success btn-block add-to-cart-btn"
                            disabled>
                            <i class="fas fa-check"></i> <span class="btn-text">Đã thêm vào giỏ</span>
                        </button>
                    <?php else: ?>
                        <button type="button"
                            class="btn btn-primary btn-block add-to-cart-btn"
                            data-book-id="<?php echo $book['ma_sach']; ?>"
                            data-book-name="<?php echo htmlspecialchars($book['ten_sach']); ?>">
                            <i class="fas fa-cart-plus"></i> <span class="btn-text">Thêm vào giỏ</span>
                        </button>
                    <?php endif; ?>
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
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
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

    .text-line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

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