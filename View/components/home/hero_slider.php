<?php
$staticBanners = [
    [
        'image' => 'Content/images/banners/banner_welcome_1766452567061.png',
        'title' => 'Chào mừng đến BookStore',
        'description' => 'Hàng ngàn đầu sách chất lượng với giá tốt nhất',
        'link' => '?page=books',
        'btn_text' => 'Khám phá ngay',
        'btn_class' => 'btn-light',
        'btn_icon' => 'fa-book-open'
    ],
    [
        'image' => 'Content/images/banners/banner_bestsellers_1766452583769.png',
        'title' => 'Sách Bán Chạy',
        'description' => 'Giảm giá lên đến 50% cho các đầu sách hot nhất',
        'link' => '?page=books&sort=bestseller',
        'btn_text' => 'Xem ngay',
        'btn_class' => 'btn-warning',
        'btn_icon' => 'fa-fire'
    ],
    [
        'image' => 'Content/images/banners/banner_new_arrivals_1766452605679.png',
        'title' => 'Sách Mới Về',
        'description' => 'Cập nhật hàng tuần với những đầu sách mới nhất',
        'link' => '?page=books&sort=new',
        'btn_text' => 'Khám phá',
        'btn_class' => 'btn-info',
        'btn_icon' => 'fa-certificate'
    ],
    [
        'image' => 'Content/images/banners/banner_free_shipping_1766452627060.png',
        'title' => 'Miễn Phí Vận Chuyển',
        'description' => 'Cho đơn hàng từ 200.000đ trở lên - Giao hàng toàn quốc',
        'link' => '?page=books',
        'btn_text' => 'Mua ngay',
        'btn_class' => 'btn-success',
        'btn_icon' => 'fa-shipping-fast'
    ],
    [
        'image' => 'Content/images/banners/banner_special_offer_1766452645672.png',
        'title' => 'Ưu Đãi Đặc Biệt',
        'description' => 'Tiết kiệm đến 40% cho sách khuyến mãi',
        'link' => '?page=books&sort=discount',
        'btn_text' => 'Xem ưu đãi',
        'btn_class' => 'btn-danger',
        'btn_icon' => 'fa-gift'
    ]
];
?>
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
                        <img src="<?php echo BASE_URL . htmlspecialchars($banner['image']); ?>"
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