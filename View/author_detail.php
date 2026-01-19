<?php
$pageTitle = isset($author) ? 'Tác giả: ' . htmlspecialchars($author['ten_tac_gia']) : 'Chi tiết tác giả';
?>

<div class="container mt-4">
    <?php if (isset($author)): ?>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=home">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="?page=books">Sách</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($author['ten_tac_gia']); ?></li>
            </ol>
        </nav>

        <!-- Author Info -->
        <div class="row mb-5">
            <div class="col-md-4 col-lg-3 text-center mb-4 mb-md-0">
                <!-- <div class="author-image-wrapper p-3 border rounded shadow-sm bg-white"> -->
                <?php
                $initialImage = !empty($author['hinh_anh']) ? $author['hinh_anh'] : '/book_store/Content/images/authors/default-author.png';
                ?>
                <img src="<?php echo htmlspecialchars($initialImage); ?>"
                    alt="<?php echo htmlspecialchars($author['ten_tac_gia']); ?>"
                    class="img-fluid rounded-circle author-img-detail"
                    data-author-name="<?php echo htmlspecialchars($author['ten_tac_gia']); ?>"
                    style="width: 200px; height: 200px; object-fit: cover;"
                    onerror="if(this.src != '/book_store/Content/images/authors/default-author.png') { this.src='/book_store/Content/images/authors/default-author.png'; }">
                <i class="fas fa-user-circle fa-10x text-muted default-icon" style="display:none;"></i>
                <!-- </div> -->
            </div>
            <div class="col-md-8 col-lg-9">
                <h2 class="mb-3"><?php echo htmlspecialchars($author['ten_tac_gia']); ?></h2>

                <?php if (!empty($author['but_danh'])): ?>
                    <p class="text-muted mb-2">
                        <i class="fas fa-signature mr-2"></i> <strong>Bút danh:</strong> <?php echo htmlspecialchars($author['but_danh']); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($author['quoc_tich'])): ?>
                    <p class="text-muted mb-2">
                        <i class="fas fa-globe mr-2"></i> <strong>Quốc tịch:</strong> <?php echo htmlspecialchars($author['quoc_tich']); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($author['ngay_sinh'])): ?>
                    <p class="text-muted mb-2">
                        <i class="fas fa-birthday-cake mr-2"></i> <strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($author['ngay_sinh'])); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($author['tieu_su'])): ?>
                    <div class="author-bio mt-4">
                        <h5 class="border-bottom pb-2">Tiểu sử</h5>
                        <div class="content text-justify">
                            <?php echo nl2br(htmlspecialchars($author['tieu_su'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <!-- Author's Books -->
        <div class="author-books-section">
            <h3 class="mb-4">
                <i class="fas fa-book mr-2 text-primary"></i>
                Sách của <?php echo htmlspecialchars($author['ten_tac_gia']); ?>
                <span class="badge badge-secondary" style="font-size: 0.5em; vertical-align: middle;"><?php echo $total_books; ?></span>
            </h3>

            <?php if (isset($books) && !empty($books)): ?>
                <div class="row">
                    <?php foreach ($books as $book): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <?php render_component('book_card', ['book' => $book]); ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($pagination) && $pagination instanceof Pagination && $pagination->getTotalPages() > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <li class="page-item <?php echo $pagination->getCurrentPage() <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=author_detail&id=<?php echo $author['id_author'] ?? $author['ma_tac_gia']; ?>&p=<?php echo $pagination->getCurrentPage() - 1; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $pagination->getTotalPages(); $i++): ?>
                                <li class="page-item <?php echo $i == $pagination->getCurrentPage() ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=author_detail&id=<?php echo $author['id_author'] ?? $author['ma_tac_gia']; ?>&p=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next -->
                            <li class="page-item <?php echo $pagination->getCurrentPage() >= $pagination->getTotalPages() ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=author_detail&id=<?php echo $author['id_author'] ?? $author['ma_tac_gia']; ?>&p=<?php echo $pagination->getCurrentPage() + 1; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có sách nào của tác giả này.
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> Không tìm thấy thông tin tác giả.
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch author images from Open Library
        const authorImages = document.querySelectorAll('.author-img-detail');

        authorImages.forEach(img => {
            const authorName = img.getAttribute('data-author-name');

            // Function to fetch image
            const fetchAuthorImage = () => {
                if (authorName) {
                    // Search for author to get OLID
                    fetch(`https://openlibrary.org/search/authors.json?q=${encodeURIComponent(authorName)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.numFound > 0 && data.docs && data.docs.length > 0) {
                                // Sort by work_count descending
                                data.docs.sort((a, b) => (b.work_count || 0) - (a.work_count || 0));

                                const authorDoc = data.docs[0];
                                const olid = authorDoc.key;

                                // Size L for large quality
                                const imageUrl = `https://covers.openlibrary.org/a/olid/${olid}-L.jpg`;

                                // Update source
                                img.src = imageUrl;
                                // Reset error handler to avoid loop if OL image also fails
                                img.onerror = function() {
                                    this.src = '/book_store/Content/images/authors/default-author.png';
                                };
                            }
                        })
                        .catch(e => console.log(e));
                }
            };

            // If image is already default or fails to load (triggering onerror which sets it to default), try fetch
            if (img.src.includes('default-author.png')) {
                fetchAuthorImage();
            } else {
                // If it's a custom image, wait to see if it errors out
                const originalOnError = img.onerror;
                img.onerror = function() {
                    // Call original error handler (sets to default)
                    if (originalOnError) originalOnError.call(this);
                    // Then try to fetch API
                    fetchAuthorImage();
                };

                // Also check if it's arguably "broken" (naturalWidth = 0) immediately if already loaded
                if (img.complete && img.naturalWidth === 0) {
                    img.onerror.call(img);
                }
            }
        });
    });
</script>