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
                    <?php render_component('book_card', ['book' => $book]); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>