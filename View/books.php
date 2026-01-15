<?php
$pageTitle = isset($_GET['keyword']) ? 'Tìm kiếm: ' . htmlspecialchars($_GET['keyword']) : 'Danh sách sách';
?>

<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="?page=home">Trang chủ</a></li>
                    <?php if (isset($_GET['category']) && isset($currentCategory)): ?>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($currentCategory['ten_danh_muc']); ?></li>
                    <?php elseif (isset($_GET['keyword'])): ?>
                        <li class="breadcrumb-item active">Tìm kiếm</li>
                    <?php else: ?>
                        <li class="breadcrumb-item active">Tất cả sách</li>
                    <?php endif; ?>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="page-header mb-4">
                <h2 class="mb-2">
                    <?php if (isset($_GET['category']) && isset($currentCategory)): ?>
                        <i class="fas fa-bookmark"></i> <?php echo htmlspecialchars($currentCategory['ten_danh_muc']); ?>
                    <?php elseif (isset($_GET['keyword'])): ?>
                        <i class="fas fa-search"></i> Kết quả tìm kiếm: "<?php echo htmlspecialchars($_GET['keyword']); ?>"
                    <?php else: ?>
                        <i class="fas fa-book"></i> Tất cả sách
                    <?php endif; ?>
                </h2>

                <?php if (isset($totalBooks)): ?>
                    <p class="text-muted">Tìm thấy <?php echo $totalBooks; ?> cuốn sách</p>
                <?php endif; ?>
            </div>

            <!-- Filter & Sort Bar -->
            <div class="filter-sort-bar mb-4">
                <div class="row align-items-center">
                    <div class="col-md-12 text-md-right">
                        <!-- View Toggle & Sort -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group mr-3" role="group" aria-label="View Mode">
                                <button type="button" class="btn btn-outline-secondary active" id="btnGrid" title="Lưới">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btnList" title="Danh sách">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>

                            <form method="GET" action="?page=books" class="form-inline">
                                <input type="hidden" name="page" value="books">
                                <?php if (isset($_GET['category'])): ?>
                                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
                                <?php endif; ?>
                                <?php if (isset($_GET['keyword'])): ?>
                                    <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($_GET['keyword']); ?>">
                                <?php endif; ?>
                                <?php if (isset($_GET['price_range'])): ?>
                                    <input type="hidden" name="price_range" value="<?php echo htmlspecialchars($_GET['price_range']); ?>">
                                <?php endif; ?>

                                <label class="mr-2 d-none d-sm-inline-block">Sắp xếp:</label>
                                <select name="sort" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="default" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'default') ? 'selected' : ''; ?>>Mặc định</option>
                                    <option value="name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Tên: A-Z</option>
                                    <option value="name_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : ''; ?>>Tên: Z-A</option>
                                    <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Giá: Thấp đến cao</option>
                                    <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Giá: Cao đến thấp</option>
                                    <option value="bestseller" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'bestseller') ? 'selected' : ''; ?>>Bán chạy nhất</option>
                                    <option value="new" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'new') ? 'selected' : ''; ?>>Mới nhất</option>
                                    <option value="rating" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating') ? 'selected' : ''; ?>>Đánh giá cao nhất</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <?php if (isset($books) && !empty($books)): ?>
                <div class="books-container" id="booksGrid">
                    <div class="row">
                        <?php foreach ($books as $book): ?>
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4 book-item-col">
                                <?php include 'book_card.php'; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- No Results -->
                <div class="no-results text-center py-5">
                    <i class="fas fa-search fa-5x text-muted mb-3"></i>
                    <h4>Không tìm thấy sách nào</h4>
                    <p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc thay đổi bộ lọc</p>
                    <a href="?page=books" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Xem tất cả sách
                    </a>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if (isset($pagination) && $pagination instanceof Pagination && $pagination->getTotalPages() > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?php echo $pagination->getCurrentPage() <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link"
                                href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=<?php echo $pagination->getCurrentPage() - 1; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                        $current_page = $pagination->getCurrentPage();
                        $total_pages = $pagination->getTotalPages();
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=1">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link"
                                    href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Next -->
                        <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link"
                                href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=<?php echo $current_page + 1; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .filter-sort-bar {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .page-header h2 {
        color: #333;
        font-weight: 700;
    }

    .pagination .page-link {
        color: #007bff;
        border-radius: 5px;
        margin: 0 3px;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .no-results {
        background: #f8f9fa;
        border-radius: 10px;
    }

    /* --- List View Styles --- */
    /* Wrapper */
    .book-item-list {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .book-item-list .card {
        flex-direction: row;
        /* Image | Body */
        align-items: stretch;
        border: 1px solid #e0e0e0;
    }

    /* Image - Left Side */
    .book-item-list .book-image-wrapper {
        width: 200px;
        flex-shrink: 0;
        height: auto !important;
        /* Override default grid height */
    }

    .book-item-list .book-image-wrapper a {
        display: block;
        height: 100%;
    }

    .book-item-list .book-image-wrapper img {
        height: 100% !important;
        object-fit: contain !important;
        /* Keep contain to show full book cover */
        padding: 10px;
    }

    /* Badges position fix for list view */
    .book-item-list .book-image-wrapper .badge {
        top: 10px;
        left: 10px;
    }

    .book-item-list .book-image-wrapper .badge-danger[style*="right"] {
        left: auto;
        right: 10px;
    }

    /* Card Body - Center & Right Side */
    .book-item-list .card-body {
        display: flex;
        flex-direction: row;
        /* Info | Actions */
        padding: 20px;
        flex: 1;
    }

    /* Info Column (Center) */
    .book-item-list .book-info-main {
        flex: 1;
        text-align: left;
        padding-right: 25px;
        border-right: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .book-item-list .card-title a {
        font-size: 1.4rem;
        font-weight: 700;
    }

    /* Actions Column (Right) */
    .book-item-list .book-actions {
        width: auto;
        /* Allow flexibility */
        min-width: 300px;
        flex-shrink: 0;
        display: flex;
        flex-direction: row;
        /* Horizontal layout */
        justify-content: flex-end;
        /* Align right */
        align-items: center;
        padding-left: 25px;
        margin-top: 0 !important;
        /* Reset mt-auto */
    }

    /* Child adjustments for horizontal layout */
    .book-item-list .book-price {
        margin-bottom: 0 !important;
        margin-right: 20px;
    }

    .book-item-list .book-actions>div[class*="mt-"] {
        margin-top: 0 !important;
        /* Remove top margin from button container */
    }

    .book-item-list .add-to-cart-btn {
        width: auto;
        /* Auto width instead of block */
        padding-left: 20px;
        padding-right: 20px;
    }

    /* Reveal Hidden Elements */
    .book-item-list .book-extra-info {
        display: block !important;
    }

    .book-item-list .btn-text {
        display: inline;
    }

    /* Mobile Responsive for List View */
    @media (max-width: 768px) {
        .book-item-list .card {
            flex-direction: column;
        }

        .book-item-list .book-image-wrapper {
            width: 100%;
            height: 250px !important;
        }

        .book-item-list .card-body {
            flex-direction: column;
        }

        .book-item-list .book-info-main {
            border-right: none;
            padding-right: 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .book-item-list .book-actions {
            width: 100%;
            min-width: 0;
            padding-left: 0;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .book-item-list .book-price {
            margin-right: 15px;
        }
    }

    .btn-group .btn.active {
        background-color: #6c757d;
        color: white;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnGrid = document.getElementById('btnGrid');
        const btnList = document.getElementById('btnList');
        const container = document.getElementById('booksGrid');

        // Only proceed if elements exist
        if (!btnGrid || !btnList) return;

        // Check local storage
        const currentView = localStorage.getItem('booksViewMode') || 'grid';
        applyViewMode(currentView);

        btnGrid.addEventListener('click', function() {
            applyViewMode('grid');
        });

        btnList.addEventListener('click', function() {
            applyViewMode('list');
        });

        function applyViewMode(mode) {
            // Toggle active state regardless of container existence
            if (mode === 'list') {
                btnList.classList.add('active');
                btnGrid.classList.remove('active');
                localStorage.setItem('booksViewMode', 'list');
            } else {
                btnGrid.classList.add('active');
                btnList.classList.remove('active');
                localStorage.setItem('booksViewMode', 'grid');
            }

            // Only manipulate container if it exists (it might be missing if no books found)
            if (!container) return;

            if (mode === 'list') {
                // Apply List Classes
                const cols = container.querySelectorAll('.book-item-col');
                cols.forEach(col => {
                    col.className = 'col-12 mb-3 book-item-col book-item-list';
                    // Reset any inline styles if they exist (cleanup)
                    const img = col.querySelector('img');
                    if (img) img.style = '';
                });
            } else {
                // Apply Grid Classes
                const cols = container.querySelectorAll('.book-item-col');
                cols.forEach(col => {
                    col.className = 'col-lg-4 col-md-6 col-sm-6 mb-4 book-item-col';
                    // Reset any inline styles
                    const img = col.querySelector('img');
                    if (img) img.style = 'height: 420px; object-fit: contain; background: #fff;';
                });
            }
        }
    });
</script>