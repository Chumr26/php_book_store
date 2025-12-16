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
                    <div class="col-md-6 mb-3 mb-md-0">
                        <!-- View Mode -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="gridView">
                                <i class="fas fa-th"></i> Lưới
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="listView">
                                <i class="fas fa-list"></i> Danh sách
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6 text-md-right">
                        <!-- Sort Dropdown -->
                        <form method="GET" action="?page=books" class="form-inline justify-content-md-end">
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
                            
                            <label class="mr-2">Sắp xếp:</label>
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
            
            <!-- Books Grid -->
            <?php if (isset($books) && !empty($books)): ?>
                <div class="books-container" id="booksGrid">
                    <div class="row">
                        <?php foreach ($books as $book): ?>
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
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
            <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" 
                               href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=<?php echo $pagination['current_page'] - 1; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Page Numbers -->
                        <?php
                        $start_page = max(1, $pagination['current_page'] - 2);
                        $end_page = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=1">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" 
                                   href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $pagination['total_pages']): ?>
                            <?php if ($end_page < $pagination['total_pages'] - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=<?php echo $pagination['total_pages']; ?>"><?php echo $pagination['total_pages']; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next -->
                        <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" 
                               href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?><?php echo isset($_GET['price_range']) ? '&price_range=' . $_GET['price_range'] : ''; ?>&p=<?php echo $pagination['current_page'] + 1; ?>">
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
</style>

<script>
$(document).ready(function() {
    // View mode toggle (Grid/List)
    $('#gridView').click(function() {
        $(this).addClass('active');
        $('#listView').removeClass('active');
        $('#booksGrid .row > div').removeClass('col-12').addClass('col-lg-4 col-md-6');
    });
    
    $('#listView').click(function() {
        $(this).addClass('active');
        $('#gridView').removeClass('active');
        $('#booksGrid .row > div').removeClass('col-lg-4 col-md-6').addClass('col-12');
    });
});
</script>
