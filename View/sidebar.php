<!-- Sidebar for book filtering -->
<div class="sidebar">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Bộ lọc</h5>
        </div>
        <div class="card-body">
            <!-- Categories Filter -->
            <div class="filter-section mb-4">
                <h6 class="font-weight-bold mb-3">Danh mục</h6>
                <div class="list-group">
                    <a href="?page=books" 
                       class="list-group-item list-group-item-action <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">
                        <i class="fas fa-border-all"></i> Tất cả sách
                    </a>
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <a href="?page=books&category=<?php echo $category['ma_danh_muc']; ?>" 
                               class="list-group-item list-group-item-action <?php echo (isset($_GET['category']) && $_GET['category'] == $category['ma_danh_muc']) ? 'active' : ''; ?>">
                                <i class="fas fa-angle-right"></i> <?php echo htmlspecialchars($category['ten_danh_muc']); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Price Range Filter -->
            <div class="filter-section mb-4">
                <h6 class="font-weight-bold mb-3">Khoảng giá</h6>
                <form method="GET" action="?page=books" id="priceFilterForm">
                    <input type="hidden" name="page" value="books">
                    <?php if (isset($_GET['category'])): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['keyword'])): ?>
                        <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($_GET['keyword']); ?>">
                    <?php endif; ?>
                    
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="price1" name="price_range" value="0-100000" 
                               class="custom-control-input" 
                               <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '0-100000') ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="price1">Dưới 100.000đ</label>
                    </div>
                    
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="price2" name="price_range" value="100000-200000" 
                               class="custom-control-input"
                               <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '100000-200000') ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="price2">100.000đ - 200.000đ</label>
                    </div>
                    
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="price3" name="price_range" value="200000-300000" 
                               class="custom-control-input"
                               <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '200000-300000') ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="price3">200.000đ - 300.000đ</label>
                    </div>
                    
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="price4" name="price_range" value="300000-500000" 
                               class="custom-control-input"
                               <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '300000-500000') ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="price4">300.000đ - 500.000đ</label>
                    </div>
                    
                    <div class="custom-control custom-radio mb-3">
                        <input type="radio" id="price5" name="price_range" value="500000-9999999" 
                               class="custom-control-input"
                               <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '500000-9999999') ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="price5">Trên 500.000đ</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                        <i class="fas fa-filter"></i> Áp dụng
                    </button>
                    
                    <?php if (isset($_GET['price_range'])): ?>
                        <a href="?page=books<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['keyword']) ? '&keyword=' . $_GET['keyword'] : ''; ?>" 
                           class="btn btn-secondary btn-sm btn-block mt-2">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Rating Filter -->
            <div class="filter-section mb-4">
                <h6 class="font-weight-bold mb-3">Đánh giá</h6>
                <form method="GET" action="?page=books">
                    <input type="hidden" name="page" value="books">
                    <?php if (isset($_GET['category'])): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['price_range'])): ?>
                        <input type="hidden" name="price_range" value="<?php echo htmlspecialchars($_GET['price_range']); ?>">
                    <?php endif; ?>
                    
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="rating5" name="min_rating" value="5" class="custom-control-input"
                               <?php echo (isset($_GET['min_rating']) && $_GET['min_rating'] == '5') ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="rating5">
                            <span class="text-warning">★★★★★</span> (5 sao)
                        </label>
                    </div>
                    
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="rating4" name="min_rating" value="4" class="custom-control-input"
                               <?php echo (isset($_GET['min_rating']) && $_GET['min_rating'] == '4') ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="rating4">
                            <span class="text-warning">★★★★☆</span> trở lên
                        </label>
                    </div>
                    
                    <div class="custom-control custom-radio mb-3">
                        <input type="radio" id="rating3" name="min_rating" value="3" class="custom-control-input"
                               <?php echo (isset($_GET['min_rating']) && $_GET['min_rating'] == '3') ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="rating3">
                            <span class="text-warning">★★★☆☆</span> trở lên
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                        <i class="fas fa-filter"></i> Áp dụng
                    </button>
                </form>
            </div>

            <!-- Publisher Filter -->
            <?php if (isset($publishers) && !empty($publishers)): ?>
            <div class="filter-section mb-4">
                <h6 class="font-weight-bold mb-3">Nhà xuất bản</h6>
                <div class="list-group" style="max-height: 200px; overflow-y: auto;">
                    <?php foreach (array_slice($publishers, 0, 10) as $publisher): ?>
                        <a href="?page=books&publisher=<?php echo $publisher['ma_nxb']; ?>" 
                           class="list-group-item list-group-item-action <?php echo (isset($_GET['publisher']) && $_GET['publisher'] == $publisher['ma_nxb']) ? 'active' : ''; ?>">
                            <small><?php echo htmlspecialchars($publisher['ten_nxb']); ?></small>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Clear All Filters -->
            <?php if (isset($_GET['category']) || isset($_GET['price_range']) || isset($_GET['min_rating']) || isset($_GET['publisher'])): ?>
            <div class="text-center">
                <a href="?page=books" class="btn btn-danger btn-block">
                    <i class="fas fa-redo"></i> Xóa tất cả bộ lọc
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bestsellers Widget -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-fire"></i> Sách bán chạy</h5>
        </div>
        <div class="card-body p-2">
            <?php if (isset($bestsellers) && !empty($bestsellers)): ?>
                <?php foreach (array_slice($bestsellers, 0, 5) as $index => $book): ?>
                    <div class="media mb-3 p-2 border-bottom">
                        <span class="badge badge-danger mr-2 align-self-start"><?php echo $index + 1; ?></span>
                        <img src="<?php echo htmlspecialchars($book['hinh_anh']); ?>" 
                             alt="<?php echo htmlspecialchars($book['ten_sach']); ?>"
                             class="mr-2" style="width: 50px; height: 70px; object-fit: cover;">
                        <div class="media-body">
                            <a href="?page=book_detail&id=<?php echo $book['ma_sach']; ?>" 
                               class="text-decoration-none">
                                <h6 class="mt-0 mb-1" style="font-size: 13px;">
                                    <?php echo htmlspecialchars(mb_substr($book['ten_sach'], 0, 50)); ?>
                                    <?php echo mb_strlen($book['ten_sach']) > 50 ? '...' : ''; ?>
                                </h6>
                            </a>
                            <p class="mb-0 text-danger font-weight-bold" style="font-size: 14px;">
                                <?php echo number_format($book['gia'], 0, ',', '.'); ?> đ
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Banner Widget -->
    <div class="card">
        <div class="card-body p-0">
            <a href="?page=books&sort=discount">
                <img src="<?php echo $baseUrl ?? '/book_store'; ?>/Content/images/banners/sidebar-banner.jpg" 
                     alt="Khuyến mãi" class="img-fluid">
            </a>
        </div>
    </div>
</div>

<style>
    .sidebar .list-group-item {
        border-left: 3px solid transparent;
        transition: all 0.3s;
    }
    
    .sidebar .list-group-item:hover {
        background-color: #f8f9fa;
        border-left-color: #007bff;
    }
    
    .sidebar .list-group-item.active {
        border-left-color: #007bff;
        background-color: #e7f3ff;
        color: #007bff;
    }
    
    .sidebar .card-header h5 {
        font-size: 16px;
    }
    
    .filter-section h6 {
        font-size: 14px;
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }
    
    .custom-control-label {
        font-size: 14px;
        cursor: pointer;
    }
</style>

<script>
    $(document).ready(function() {
        // Auto-submit price filter on change
        $('input[name="price_range"]').change(function() {
            $('#priceFilterForm').submit();
        });
    });
</script>
