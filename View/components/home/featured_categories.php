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