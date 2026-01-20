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
                        <?php render_component('book_card', ['book' => $book]); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>