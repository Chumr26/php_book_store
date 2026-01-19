<?php
$pageTitle = isset($publisher) ? 'Nhà xuất bản: ' . htmlspecialchars($publisher['ten_nxb']) : 'Chi tiết nhà xuất bản';
?>

<div class="container mt-4">
    <?php if (isset($publisher)): ?>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=home">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="?page=books">Sách</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($publisher['ten_nxb']); ?></li>
            </ol>
        </nav>

        <!-- Publisher Info -->
        <div class="row mb-5">
            <div class="col-md-4 col-lg-3 text-center mb-4 mb-md-0">
                <div class="publisher-logo-wrapper p-4 border rounded shadow-sm bg-white d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-building fa-7x text-muted"></i>
                </div>
            </div>
            <div class="col-md-8 col-lg-9">
                <h2 class="mb-3"><?php echo htmlspecialchars($publisher['ten_nxb']); ?></h2>

                <div class="publisher-details mt-4">
                    <?php if (!empty($publisher['dia_chi'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-danger"></i> <strong>Địa chỉ:</strong> <?php echo htmlspecialchars($publisher['dia_chi']); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($publisher['dien_thoai'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone mr-2 text-success"></i> <strong>Điện thoại:</strong> <?php echo htmlspecialchars($publisher['dien_thoai']); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($publisher['email'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-envelope mr-2 text-primary"></i> <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($publisher['email']); ?>"><?php echo htmlspecialchars($publisher['email']); ?></a>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($publisher['website'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-globe mr-2 text-info"></i> <strong>Website:</strong> <a href="<?php echo htmlspecialchars($publisher['website']); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($publisher['website']); ?></a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr>

        <!-- Publisher's Books -->
        <div class="publisher-books-section">
            <h3 class="mb-4">
                <i class="fas fa-book mr-2 text-primary"></i>
                Sách của <?php echo htmlspecialchars($publisher['ten_nxb']); ?>
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
                                <a class="page-link" href="?page=publisher_detail&id=<?php echo $publisher['id_publisher'] ?? $publisher['ma_nxb']; ?>&p=<?php echo $pagination->getCurrentPage() - 1; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $pagination->getTotalPages(); $i++): ?>
                                <li class="page-item <?php echo $i == $pagination->getCurrentPage() ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=publisher_detail&id=<?php echo $publisher['id_publisher'] ?? $publisher['ma_nxb']; ?>&p=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next -->
                            <li class="page-item <?php echo $pagination->getCurrentPage() >= $pagination->getTotalPages() ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=publisher_detail&id=<?php echo $publisher['id_publisher'] ?? $publisher['ma_nxb']; ?>&p=<?php echo $pagination->getCurrentPage() + 1; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có sách nào của nhà xuất bản này.
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> Không tìm thấy thông tin nhà xuất bản.
        </div>
    <?php endif; ?>
</div>