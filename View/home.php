<?php
$pageTitle = "Trang chủ";
?>

<!-- Hero Slider -->
<section class="hero-slider mb-5">
    <div id="heroCarousel" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php if (isset($banners) && !empty($banners)): ?>
                <?php foreach ($banners as $index => $banner): ?>
                    <li data-target="#heroCarousel" data-slide-to="<?php echo $index; ?>" <?php echo $index == 0 ? 'class="active"' : ''; ?>></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ol>
        <div class="carousel-inner">
            <?php if (isset($banners) && !empty($banners)): ?>
                <?php foreach ($banners as $index => $banner): ?>
                    <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($banner['hinh_anh']); ?>" 
                             class="d-block w-100" 
                             alt="<?php echo htmlspecialchars($banner['tieu_de']); ?>"
                             style="height: 450px; object-fit: cover;">
                        <div class="carousel-caption d-none d-md-block">
                            <h2 class="display-4"><?php echo htmlspecialchars($banner['tieu_de']); ?></h2>
                            <?php if (!empty($banner['mo_ta'])): ?>
                                <p class="lead"><?php echo htmlspecialchars($banner['mo_ta']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($banner['lien_ket'])): ?>
                                <a href="<?php echo htmlspecialchars($banner['lien_ket']); ?>" 
                                   class="btn btn-primary btn-lg">Xem ngay</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default banner if no banners available -->
                <div class="carousel-item active">
                    <img src="<?php echo $baseUrl ?? '/book_store'; ?>/Content/images/banners/default-banner.jpg" 
                         class="d-block w-100" 
                         alt="Welcome"
                         style="height: 450px; object-fit: cover; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <!-- <div class="carousel-caption d-none d-md-block">
                        <h2 class="display-4">Chào mừng đến BookStore</h2>
                        <p class="lead">Hàng ngàn đầu sách chất lượng với giá tốt nhất</p>
                        <a href="?page=books" class="btn btn-primary btn-lg">Khám phá ngay</a>
                    </div> -->
                </div>
            <?php endif; ?>
        </div>
        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</section>

<div class="container">
    <!-- Featured Categories -->
    <?php if (isset($categories) && !empty($categories)): ?>
    <section class="featured-categories mb-5">
        <div class="section-header mb-4">
            <h3 class="section-title">
                <i class="fas fa-list"></i> Danh mục sách
            </h3>
        </div>
        <div class="row">
            <?php foreach (array_slice($categories, 0, 8) as $category): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <a href="?page=books&category=<?php echo urlencode((string)($category['ma_danh_muc'] ?? '')); ?>" 
                       class="category-card text-decoration-none">
                        <div class="card h-100 shadow-sm hover-shadow">
                            <div class="card-body text-center">
                                <div class="category-icon mb-3">
                                    <i class="fas fa-book fa-3x text-primary"></i>
                                </div>
                                <?php
                                $categoryName = (string)($category['ten_danh_muc'] ?? '');
                                $categoryDesc = (string)($category['mo_ta'] ?? '');
                                ?>
                                <h5 class="card-title"><?php echo htmlspecialchars($categoryName); ?></h5>
                                <?php if ($categoryDesc !== ''): ?>
                                    <p class="card-text text-muted small">
                                        <?php echo htmlspecialchars(mb_substr($categoryDesc, 0, 60)); ?>
                                        <?php echo mb_strlen($categoryDesc) > 60 ? '...' : ''; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Featured Books -->
    <?php if (isset($featuredBooks) && !empty($featuredBooks)): ?>
    <section class="featured-books mb-5">
        <div class="section-header mb-4">
            <h3 class="section-title">
                <i class="fas fa-star text-warning"></i> Sách nổi bật
            </h3>
            <a href="?page=books" class="btn btn-outline-primary">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row">
            <?php foreach ($featuredBooks as $book): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <?php include 'book_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Bestsellers -->
    <?php if (isset($topSellingBooks) && !empty($topSellingBooks)): ?>
    <section class="bestsellers mb-5">
        <div class="section-header mb-4">
            <h3 class="section-title">
                <i class="fas fa-fire text-danger"></i> Sách bán chạy
            </h3>
            <a href="?page=books&sort=bestseller" class="btn btn-outline-primary">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row">
            <?php foreach ($topSellingBooks as $book): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <?php include 'book_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- New Arrivals -->
    <?php if (isset($newArrivals) && !empty($newArrivals)): ?>
    <section class="new-arrivals mb-5">
        <div class="section-header mb-4">
            <h3 class="section-title">
                <i class="fas fa-certificate text-success"></i> Sách mới
            </h3>
            <a href="?page=books&sort=new" class="btn btn-outline-primary">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row">
            <?php foreach ($newArrivals as $book): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <?php include 'book_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Promotional Banner -->
    <section class="promo-banner mb-5">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="promo-card bg-primary text-white p-4 rounded">
                    <h4><i class="fas fa-shipping-fast"></i> Miễn phí vận chuyển</h4>
                    <p class="mb-0">Cho đơn hàng từ 200.000đ trở lên</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="promo-card bg-success text-white p-4 rounded">
                    <h4><i class="fas fa-gift"></i> Ưu đãi đặc biệt</h4>
                    <p class="mb-0">Giảm giá lên đến 50% cho sách chọn lọc</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose-us mb-5">
        <div class="section-header mb-4 text-center">
            <h3 class="section-title">Tại sao chọn chúng tôi?</h3>
        </div>
        <div class="row text-center">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-book-open fa-3x text-primary"></i>
                    </div>
                    <h5>Sách chất lượng</h5>
                    <p class="text-muted">Hàng ngàn đầu sách được tuyển chọn kỹ lưỡng</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-truck fa-3x text-primary"></i>
                    </div>
                    <h5>Giao hàng nhanh</h5>
                    <p class="text-muted">Giao hàng toàn quốc trong 2-3 ngày</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    </div>
                    <h5>Thanh toán an toàn</h5>
                    <p class="text-muted">Nhiều phương thức thanh toán bảo mật</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-headset fa-3x text-primary"></i>
                    </div>
                    <h5>Hỗ trợ 24/7</h5>
                    <p class="text-muted">Đội ngũ chăm sóc khách hàng tận tâm</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #007bff;
        padding-bottom: 15px;
    }
    
    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }
    
    .category-card .card {
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    
    .category-card .card:hover {
        transform: translateY(-5px);
        border-color: #007bff;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    
    .promo-card {
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .promo-card:hover {
        transform: scale(1.02);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    
    .feature-box {
        padding: 20px;
        transition: all 0.3s;
    }
    
    .feature-box:hover {
        transform: translateY(-10px);
    }
    
    .feature-box:hover .feature-icon i {
        transform: scale(1.1);
        transition: all 0.3s;
    }
    
    .carousel-caption {
        background: rgba(0,0,0,0.5);
        padding: 20px;
        border-radius: 10px;
    }
</style>
