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