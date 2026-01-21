<?php

/**
 * Book Card Component
 * 
 * Renders a single book card with image, title, price, and action buttons.
 * 
 * @var array $book Book data array
 */

if (!isset($book)) return;

// Fallback for cover helper location
$coverHelperPath = defined('BASE_PATH') ? BASE_PATH . 'View/helpers/cover.php' : __DIR__ . '/../helpers/cover.php';
if (file_exists($coverHelperPath)) {
    require_once $coverHelperPath;
}

// Helper to get global cart items if not passed
if (!isset($globalCartBookIds) && isset($GLOBALS['globalCartBookIds'])) {
    $globalCartBookIds = $GLOBALS['globalCartBookIds'];
}

$coverUrl = function_exists('book_cover_url') ? book_cover_url($book['isbn'] ?? null, 'medium') : BASE_URL . 'Content/images/books/no-image.webp';
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
                    onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>Content/images/books/no-image.webp';"
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
                        <i class="fas fa-user"></i>
                        <?php
                        $authorName = $book['ten_tac_gia'] ?? $book['author_name'];
                        // Try to find author ID if available (ma_tac_gia or id_tacgia)
                        $authorId = $book['ma_tac_gia'] ?? ($book['id_tacgia'] ?? null);

                        if ($authorId):
                        ?>
                            <a href="?page=author_detail&id=<?php echo $authorId; ?>" class="text-muted text-decoration-none hover-underline">
                                <?php echo htmlspecialchars($authorName); ?>
                            </a>
                        <?php else: ?>
                            <?php echo htmlspecialchars($authorName); ?>
                        <?php endif; ?>
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
                            <span class="mr-3">
                                <i class="fas fa-building"></i>
                                <?php
                                $publisherName = $book['ten_nxb'] ?? $book['publisher_name'];
                                $publisherId = $book['ma_nha_xuat_ban'] ?? ($book['id_nxb'] ?? null); // Adjust key if needed based on query
                                if ($publisherId):
                                ?>
                                    <a href="?page=publisher_detail&id=<?php echo $publisherId; ?>" class="text-muted text-decoration-none hover-underline">
                                        <?php echo htmlspecialchars($publisherName); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($publisherName); ?>
                                <?php endif; ?>
                            </span>
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
                    if (isset($globalCartBookIds) && in_array($book['ma_sach'], $globalCartBookIds)) {
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