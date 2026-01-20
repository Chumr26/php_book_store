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
                            <div class="author-avatar mb-3 mx-auto">
                                <img src="Content/images/authors/default-author.svg"
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
                            <a href="?page=author_detail&id=<?php echo $author['ma_tac_gia']; ?>"
                                class="btn btn-sm btn-outline-primary btn-block mt-auto">
                                Xem chi tiết tác giả
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>