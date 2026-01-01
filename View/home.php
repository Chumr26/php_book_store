<?php
$pageTitle = "Trang chủ";

// Define banners directly (no database needed)
$staticBanners = [
    [
        'image' => '/book_store/Content/images/banners/banner_welcome_1766452567061.png',
        'title' => 'Chào mừng đến BookStore',
        'description' => 'Hàng ngàn đầu sách chất lượng với giá tốt nhất',
        'link' => '?page=books',
        'btn_text' => 'Khám phá ngay',
        'btn_class' => 'btn-light',
        'btn_icon' => 'fa-book-open'
    ],
    [
        'image' => '/book_store/Content/images/banners/banner_bestsellers_1766452583769.png',
        'title' => 'Sách Bán Chạy',
        'description' => 'Giảm giá lên đến 50% cho các đầu sách hot nhất',
        'link' => '?page=books&sort=bestseller',
        'btn_text' => 'Xem ngay',
        'btn_class' => 'btn-warning',
        'btn_icon' => 'fa-fire'
    ],
    [
        'image' => '/book_store/Content/images/banners/banner_new_arrivals_1766452605679.png',
        'title' => 'Sách Mới Về',
        'description' => 'Cập nhật hàng tuần với những đầu sách mới nhất',
        'link' => '?page=books&sort=new',
        'btn_text' => 'Khám phá',
        'btn_class' => 'btn-info',
        'btn_icon' => 'fa-certificate'
    ],
    [
        'image' => '/book_store/Content/images/banners/banner_free_shipping_1766452627060.png',
        'title' => 'Miễn Phí Vận Chuyển',
        'description' => 'Cho đơn hàng từ 200.000đ trở lên - Giao hàng toàn quốc',
        'link' => '?page=books',
        'btn_text' => 'Mua ngay',
        'btn_class' => 'btn-success',
        'btn_icon' => 'fa-shipping-fast'
    ],
    [
        'image' => '/book_store/Content/images/banners/banner_special_offer_1766452645672.png',
        'title' => 'Ưu Đãi Đặc Biệt',
        'description' => 'Tiết kiệm đến 40% cho sách khuyến mãi',
        'link' => '?page=books&sort=discount',
        'btn_text' => 'Xem ưu đãi',
        'btn_class' => 'btn-danger',
        'btn_icon' => 'fa-gift'
    ]
];
?>

<!-- Hero Slider -->
<!-- Hero Slider -->
<section class="hero-slider">
    <div id="heroCarousel" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php foreach ($staticBanners as $index => $banner): ?>
                <li data-target="#heroCarousel" data-slide-to="<?php echo $index; ?>" <?php echo $index == 0 ? 'class="active"' : ''; ?>></li>
            <?php endforeach; ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach ($staticBanners as $index => $banner): ?>
                <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                    <div class="hero-image-wrapper">
                        <img src="<?php echo htmlspecialchars($banner['image']); ?>" 
                             class="d-block w-100 hero-img" 
                             alt="<?php echo htmlspecialchars($banner['title']); ?>">
                        <div class="carousel-caption d-none d-md-block">
                            <h2 class="display-4 font-weight-bold"><?php echo htmlspecialchars($banner['title']); ?></h2>
                            <p class="lead text-white"><?php echo htmlspecialchars($banner['description']); ?></p>
                            <a href="<?php echo htmlspecialchars($banner['link']); ?>" 
                               class="btn <?php echo $banner['btn_class']; ?> btn-lg shadow">
                                <i class="fas <?php echo $banner['btn_icon']; ?>"></i> <?php echo htmlspecialchars($banner['btn_text']); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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

<style>
    /* Hero Slider Styles */
    .hero-slider {
        overflow: hidden;
        position: relative;
    }

    .hero-slider .carousel-inner {
        overflow: hidden; /* Ensure content doesn't spill out */
        /* border-radius: 0 0 10px 10px; Optional rounded corners bottom */
    }

    .hero-slider .hero-image-wrapper {
        height: 450px;
        position: relative;
        overflow: hidden;
    }

    .hero-slider .hero-img {
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .hero-slider .carousel-caption {
        background: rgba(0,0,0,0.3);
        padding: 20px;
        border-radius: 10px;
        bottom: 50px;
        backdrop-filter: blur(3px);
    }
    
    @media (max-width: 768px) {
        .hero-slider .hero-image-wrapper {
            height: 300px; /* Smaller height for mobile */
        }
        
        .hero-slider .display-4 {
            font-size: 2rem;
        }
    }
</style>

<!-- Statistics Counter Section - Redesigned -->
<?php if (isset($statistics) && !empty($statistics)): ?>
<section class="statistics-section-redesign py-5 mb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="stats-main-title">
                <span class="title-icon">📊</span>
                Thành tích của chúng tôi
            </h2>
            <p class="stats-subtitle">Những con số ấn tượng tạo nên sự khác biệt</p>
        </div>
        
        <div class="row">
            <!-- Books Stat -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card-modern stat-card-books">
                    <div class="stat-card-inner">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon-bg">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number-modern" data-target="<?php echo $statistics['total_books']; ?>">0</div>
                            <div class="stat-label-modern">Đầu sách</div>
                            <div class="stat-description">Đa dạng thể loại</div>
                        </div>

                    </div>
                </div>
            </div>
            
            <!-- Customers Stat -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card-modern stat-card-customers">
                    <div class="stat-card-inner">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon-bg">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number-modern" data-target="<?php echo $statistics['total_customers']; ?>">0</div>
                            <div class="stat-label-modern">Khách hàng</div>
                            <div class="stat-description">Tin tưởng & hài lòng</div>
                        </div>

                    </div>
                </div>
            </div>
            
            <!-- Orders Stat -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card-modern stat-card-orders">
                    <div class="stat-card-inner">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon-bg">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number-modern" data-target="<?php echo $statistics['total_orders']; ?>">0</div>
                            <div class="stat-label-modern">Đơn hàng</div>
                            <div class="stat-description">Giao thành công</div>
                        </div>

                    </div>
                </div>
            </div>
            
            <!-- Authors Stat -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card-modern stat-card-authors">
                    <div class="stat-card-inner">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon-bg">
                                <i class="fas fa-pen-fancy"></i>
                            </div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number-modern" data-target="<?php echo $statistics['total_authors']; ?>">0</div>
                            <div class="stat-label-modern">Tác giả</div>
                            <div class="stat-description">Nổi tiếng & uy tín</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

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
                       class="category-card text-decoration-none h-100 d-block">
                        <div class="card h-100 shadow-sm hover-shadow">
                            <div class="card-body text-center d-flex flex-column">
                                <div class="category-icon mb-3">
                                    <i class="fas fa-book fa-3x text-primary"></i>
                                </div>
                                <?php
                                $categoryName = (string)($category['ten_danh_muc'] ?? '');
                                $categoryDesc = (string)($category['mo_ta'] ?? '');
                                ?>
                                <h5 class="card-title text-line-clamp-2 mb-2" style="height: 48px; overflow: hidden;"><?php echo htmlspecialchars($categoryName); ?></h5>
                                <?php if ($categoryDesc !== ''): ?>
                                    <p class="card-text text-muted small text-line-clamp-2 mb-0 mb-auto">
                                        <?php echo htmlspecialchars($categoryDesc); ?>
                                    </p>
                                <?php else: ?>
                                    <!-- Spacer to maintain alignment if no description -->
                                     <p class="card-text small mb-0 mt-auto invisible">
                                        &nbsp;<br>&nbsp;
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

    <!-- Deals of the Day -->
    <?php if (isset($dealsOfTheDay) && !empty($dealsOfTheDay)): ?>
    <section class="deals-section mb-5">
        <div class="section-header mb-4">
            <h3 class="section-title">
                <i class="fas fa-fire text-danger"></i> Ưu đãi hôm nay
            </h3>
            <a href="?page=books&sort=discount" class="btn btn-outline-danger">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row">
            <?php foreach ($dealsOfTheDay as $book): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="book-card deal-card">
                        <div class="deal-badge">
                            -<?php echo $book['phan_tram_giam']; ?>%
                        </div>
                        <?php include 'book_card.php'; ?>
                    </div>
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
                <i class="fas fa-trophy text-warning"></i> Sách bán chạy
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

    <!-- Author Spotlight -->
    <?php if (isset($featuredAuthors) && !empty($featuredAuthors)): ?>
    <section class="author-spotlight mb-5">
        <div class="section-header mb-4">
            <h3 class="section-title">
                <i class="fas fa-user-edit text-info"></i> Tác giả nổi bật
            </h3>
        </div>
        <div class="row">
            <?php foreach ($featuredAuthors as $author): ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="author-card card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="author-avatar mb-3">
                                <img src="Content/images/authors/default-author.png" 
                                     alt="<?php echo htmlspecialchars($author['ten_tac_gia']); ?>"
                                     class="rounded-circle author-img"
                                     data-author-name="<?php echo htmlspecialchars($author['ten_tac_gia']); ?>"
                                     style="width: 120px; height: 120px; object-fit: cover; display: none;"
                                     onload="if(this.naturalWidth > 1) { this.style.display='inline-block'; this.nextElementSibling.style.display='none'; } else { this.style.display='none'; this.nextElementSibling.style.display='inline-block'; }"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                <i class="fas fa-user-circle fa-5x text-primary default-icon"></i>
                            </div>
                            <h5 class="author-name"><?php echo htmlspecialchars($author['ten_tac_gia']); ?></h5>
                            <?php if (!empty($author['but_danh'])): ?>
                                <p class="author-pen-name text-muted">
                                    <i class="fas fa-signature"></i> <?php echo htmlspecialchars($author['but_danh']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($author['quoc_tich'])): ?>
                                <p class="author-country">
                                    <i class="fas fa-flag"></i> <?php echo htmlspecialchars($author['quoc_tich']); ?>
                                </p>
                            <?php endif; ?>
                            <div class="author-stats mt-3">
                                <span class="badge badge-primary">
                                    <i class="fas fa-book"></i> <?php echo $author['so_luong_sach']; ?> sách
                                </span>
                                <span class="badge badge-success">
                                    <i class="fas fa-chart-line"></i> <?php echo number_format($author['tong_ban'] ?? 0); ?> lượt bán
                                </span>
                            </div>
                            <?php if (!empty($author['tieu_su'])): ?>
                                <p class="author-bio mt-3 text-muted small text-line-clamp-3">
                                    <?php echo htmlspecialchars($author['tieu_su']); ?>
                                </p>
                            <?php endif; ?>
                            <a href="?page=books&author=<?php echo $author['ma_tac_gia']; ?>" 
                               class="btn btn-sm btn-outline-primary btn-block mt-auto">
                                Xem sách của tác giả
                            </a>
                        </div>
                    </div>
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

    <!-- Customer Testimonials -->
    <section class="testimonials-section mb-5">
        <div class="section-header mb-4 text-center">
            <h3 class="section-title">
                <i class="fas fa-quote-left text-primary"></i> Khách hàng nói gì về chúng tôi
            </h3>
        </div>
        <div id="testimonialsCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="testimonial-card">
                        <div class="testimonial-stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="testimonial-text">
                            "Sách chất lượng, giao hàng nhanh chóng. Tôi rất hài lòng với dịch vụ của BookStore. 
                            Sẽ tiếp tục ủng hộ!"
                        </p>
                        <div class="testimonial-author">
                            <strong>Nguyễn Văn A</strong>
                            <p class="text-muted small">Khách hàng thân thiết</p>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="testimonial-card">
                        <div class="testimonial-stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="testimonial-text">
                            "Giá cả hợp lý, nhiều chương trình khuyến mãi hấp dẫn. Website dễ sử dụng, 
                            tìm sách rất tiện lợi."
                        </p>
                        <div class="testimonial-author">
                            <strong>Trần Thị B</strong>
                            <p class="text-muted small">Khách hàng mới</p>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="testimonial-card">
                        <div class="testimonial-stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="testimonial-text">
                            "Đội ngũ hỗ trợ nhiệt tình, chuyên nghiệp. Sách được đóng gói cẩn thận, 
                            không bị hư hỏng trong quá trình vận chuyển."
                        </p>
                        <div class="testimonial-author">
                            <strong>Lê Văn C</strong>
                            <p class="text-muted small">Khách hàng VIP</p>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#testimonialsCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next" href="#testimonialsCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        </div>
    </section>

    <!-- Newsletter Subscription -->
    <section class="newsletter-section mb-5">
        <div class="newsletter-card">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h3 class="newsletter-title">
                        <i class="fas fa-envelope-open-text"></i> Đăng ký nhận tin
                    </h3>
                    <p class="newsletter-desc">
                        Nhận thông tin về sách mới, ưu đãi đặc biệt và các chương trình khuyến mãi hấp dẫn
                    </p>
                </div>
                <div class="col-md-6">
                    <form id="newsletterForm" class="newsletter-form">
                        <div class="input-group">
                            <input type="email" 
                                   class="form-control" 
                                   id="newsletterEmail"
                                   placeholder="Nhập email của bạn..." 
                                   required>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i> Đăng ký
                                </button>
                            </div>
                        </div>
                        <small class="form-text mt-2">
                            Chúng tôi cam kết bảo mật thông tin của bạn
                        </small>
                    </form>
                </div>
            </div>
        </div>
    </section>

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
    /* Modern Statistics Section - Redesigned */
    .statistics-section-redesign {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    
    .statistics-section-redesign::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.05)"/></svg>');
        opacity: 0.3;
    }
    
    .stats-main-title {
        font-size: 36px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 10px;
    }
    
    .title-icon {
        font-size: 42px;
        margin-right: 10px;
    }
    
    .stats-subtitle {
        font-size: 18px;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 300;
    }
    
    .stat-card-modern {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 30px 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .stat-card-modern:hover::before {
        transform: scaleX(1);
    }
    
    .stat-card-modern:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    
    .stat-card-inner {
        position: relative;
        z-index: 1;
    }
    
    .stat-icon-wrapper {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .stat-icon-bg {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        transition: all 0.3s ease;
    }
    
    .stat-card-books .stat-icon-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }
    
    .stat-card-customers .stat-icon-bg {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: #fff;
    }
    
    .stat-card-orders .stat-icon-bg {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: #fff;
    }
    
    .stat-card-authors .stat-icon-bg {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: #fff;
    }
    
    .stat-card-modern:hover .stat-icon-bg {
        transform: rotate(360deg) scale(1.1);
    }
    
    .stat-content {
        text-align: center;
    }
    
    .stat-number-modern {
        font-size: 48px;
        font-weight: 700;
        color: #333;
        margin: 15px 0 5px 0;
        line-height: 1;
    }
    
    .stat-label-modern {
        font-size: 18px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }
    
    .stat-description {
        font-size: 13px;
        color: #999;
        font-style: italic;
    }
    
    
    /* Old Statistics Section (keeping for compatibility) */
    .statistics-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .stat-card {
        padding: 20px;
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-10px);
    }
    
    .stat-icon {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 15px;
    }
    
    .stat-number {
        font-size: 48px;
        font-weight: 700;
        margin: 10px 0;
    }
    
    .stat-label {
        font-size: 18px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Section Headers */
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
        margin-left: 40px;
    }
    
    /* Category Cards */
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
    
    /* Deals Section */
    .deal-card {
        position: relative;
    }
    
    .deal-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 14px;
        z-index: 10;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    /* Author Cards */
    .author-card {
        transition: all 0.3s;
        border: none;
    }
    
    .author-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0,0,0,0.15) !important;
    }
    
    .author-avatar {
        text-align: center;
    }
    
    .author-name {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    
    .author-pen-name {
        font-size: 14px;
        font-style: italic;
    }
    
    .author-country {
        font-size: 14px;
        color: #666;
    }
    
    .author-stats .badge {
        margin-right: 5px;
        padding: 8px 12px;
        font-size: 12px;
    }
    
    .author-bio {
        line-height: 1.6;
    }
    
    /* Testimonials */
    .testimonials-section {
        background: #f8f9fa;
        padding: 60px 0;
        border-radius: 10px;
    }
    
    .testimonial-card {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-width: 700px;
        margin: 0 auto;
        text-align: center;
    }
    
    .testimonial-stars {
        font-size: 24px;
    }
    
    .testimonial-text {
        font-size: 18px;
        font-style: italic;
        color: #555;
        margin: 20px 0;
        line-height: 1.8;
    }
    
    .testimonial-author strong {
        font-size: 16px;
        color: #333;
    }
    
    #testimonialsCarousel .carousel-control-prev,
    #testimonialsCarousel .carousel-control-next {
        width: 50px;
        height: 50px;
        background: #007bff;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
    }
    
    #testimonialsCarousel .carousel-control-prev {
        left: -25px;
    }
    
    #testimonialsCarousel .carousel-control-next {
        right: -25px;
    }
    
    /* Newsletter */
    .newsletter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
        border-radius: 10px;
        color: white;
    }
    
    .newsletter-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .newsletter-desc {
        font-size: 16px;
        opacity: 0.9;
    }
    
    .newsletter-form .form-control {
        height: 50px;
        border: none;
        border-radius: 25px 0 0 25px;
    }
    
    .newsletter-form .btn {
        height: 50px;
        border-radius: 0 25px 25px 0;
        padding: 0 30px;
        font-weight: 600;
    }
    
    /* Promo Cards */
    .promo-card {
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .promo-card:hover {
        transform: scale(1.02);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    
    /* Feature Boxes */
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
    
    /* Hero Slider - Full Width */
    /* Hero Slider - Full Width rule removed to prevent overflow */
    
    /* Carousel Caption */
    .carousel-caption {
        background: rgba(0,0,0,0.5);
        padding: 20px;
        border-radius: 10px;
    }
    
    /* Responsive */
    /* Utility Classes for Text Truncation */
    .text-line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .text-line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media (max-width: 767.98px) {
        .stat-number {
            font-size: 36px;
        }
        
        .stat-label {
            font-size: 14px;
        }
        
        .newsletter-title {
            font-size: 22px;
        }
        
        .testimonial-text {
            font-size: 16px;
        }
        
        #testimonialsCarousel .carousel-control-prev,
        #testimonialsCarousel .carousel-control-next {
            display: none;
        }
    }
</style>

<script>
$(document).ready(function() {
    // Counter Animation for Redesigned Stats
    function animateCounter() {
        $('.stat-number-modern').each(function() {
            const $this = $(this);
            const target = parseInt($this.data('target'));
            
            $({ counter: 0 }).animate({ counter: target }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.ceil(this.counter).toLocaleString());
                },
                complete: function() {
                    $this.text(target.toLocaleString());
                }
            });
        });
    }
    
    // Trigger counter animation when section is visible
    const statsSection = $('.statistics-section-redesign');
    if (statsSection.length) {
        const observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                animateCounter();
                observer.disconnect();
            }
        }, { threshold: 0.5 });
        
        observer.observe(statsSection[0]);
    }
    
    // Newsletter Form Submission
    $('#newsletterForm').on('submit', function(e) {
        e.preventDefault();
        const email = $('#newsletterEmail').val();
        
        // Simple validation
        if (!email || !email.includes('@')) {
            alert('Vui lòng nhập email hợp lệ!');
            return;
        }
        
        // TODO: Add AJAX call to save newsletter subscription
        alert('Cảm ơn bạn đã đăng ký nhận tin! Chúng tôi sẽ gửi thông tin mới nhất đến email của bạn.');
        $('#newsletterEmail').val('');
    });
    
    // Auto-play testimonials carousel
    $('#testimonialsCarousel').carousel({
        interval: 5000,
        ride: 'carousel'
    });
    
    // Fetch author images from Open Library
    const authorImages = document.querySelectorAll('.author-img');
    
    authorImages.forEach(img => {
        const authorName = img.getAttribute('data-author-name');
        if (authorName) {
            // Search for author to get OLID
            fetch(`https://openlibrary.org/search/authors.json?q=${encodeURIComponent(authorName)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.numFound > 0 && data.docs && data.docs.length > 0) {
                        // Sort by work_count descending to get the most popular author profile
                        data.docs.sort((a, b) => (b.work_count || 0) - (a.work_count || 0));
                        
                        // Get the most relevant result (first one after sorting)
                        const authorDoc = data.docs[0];
                        const olid = authorDoc.key;
                        
                        // Check if image exists by trying to load it
                        // Size L for large quality
                        const imageUrl = `https://covers.openlibrary.org/a/olid/${olid}-L.jpg`;
                        img.src = imageUrl;
                    }
                })
                .catch(err => {
                    console.log('Error fetching author image:', err);
                    // Default icon will stay visible due to onerror handler
                });
        }
    });
});
</script>
