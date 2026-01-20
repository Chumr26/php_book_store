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
                    <?php render_component('book_card', ['book' => $book]); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>