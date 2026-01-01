<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý sách</h1>
        <a href="index.php?page=book_create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Thêm sách mới
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc tìm kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="form-inline">
                <input type="hidden" name="page" value="books">
                
                <div class="form-group mb-2 mr-3">
                    <label for="search" class="sr-only">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Tên sách, ISBN..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>

                <div class="form-group mb-2 mr-3">
                    <select class="form-control" name="category">
                        <option value="0">-- Tất cả danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['ma_danh_muc']; ?>" 
                                <?php echo $filters['category'] == $cat['ma_danh_muc'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-2 mr-3">
                    <select class="form-control" name="status">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo $st; ?>" 
                                <?php echo $filters['status'] == $st ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($st); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group mb-2 mr-3">
                    <select class="form-control" name="sort_by">
                        <option value="ngay_tao" <?php echo $filters['sort_by'] == 'ngay_tao' ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="ten_sach" <?php echo $filters['sort_by'] == 'ten_sach' ? 'selected' : ''; ?>>Tên sách</option>
                        <option value="gia" <?php echo $filters['sort_by'] == 'gia' ? 'selected' : ''; ?>>Giá</option>
                        <option value="so_luong_ton" <?php echo $filters['sort_by'] == 'so_luong_ton' ? 'selected' : ''; ?>>Tồn kho</option>
                        <option value="luot_ban" <?php echo $filters['sort_by'] == 'luot_ban' ? 'selected' : ''; ?>>Bán chạy</option>
                    </select>
                </div>

                 <div class="form-group mb-2 mr-3">
                    <select class="form-control" name="order">
                        <option value="DESC" <?php echo $filters['order'] == 'DESC' ? 'selected' : ''; ?>>Giảm dần</option>
                        <option value="ASC" <?php echo $filters['order'] == 'ASC' ? 'selected' : ''; ?>>Tăng dần</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <a href="index.php?page=books" class="btn btn-secondary mb-2 ml-2">Đặt lại</a>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <form id="bulk-action-form" action="index.php?page=book_bulk_delete" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa các sách đã chọn?');">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger btn-sm" id="bulk-delete-btn" disabled>
                            <i class="fas fa-trash"></i> Xóa đã chọn
                        </button>
                    </div>

                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 40px" class="text-center">
                                    <input type="checkbox" id="check-all">
                                </th>
                                <th style="width: 60px">Hình</th>
                                <th>Tên sách</th>
                                <th>Danh mục</th>
                                <th>Giá bán</th>
                                <th>Tồn kho</th>
                                <th>Trạng thái</th>
                                <th style="width: 150px">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($books)): ?>
                                <?php foreach ($books as $book): ?>
                                <tr>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" name="book_ids[]" value="<?php echo $book['ma_sach']; ?>" class="book-check">
                                    </td>
                                    <td class="text-center">
                                        <?php if ($book['anh_bia']): ?>
                                            <img src="<?php echo BASE_URL . $book['anh_bia']; ?>" alt="Cover" style="height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 50px; width: 35px; margin: 0 auto;">
                                                <i class="fas fa-book text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-primary mb-1"><?php echo htmlspecialchars($book['ten_sach']); ?></div>
                                        <small class="text-muted">ISBN: <?php echo htmlspecialchars($book['isbn']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['ten_danh_muc'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($book['gia_goc'] > $book['gia']): ?>
                                            <div class="text-danger font-weight-bold"><?php echo number_format($book['gia']); ?>đ</div>
                                            <small class="text-muted text-decoration-line-through"><?php echo number_format($book['gia_goc']); ?>đ</small>
                                        <?php else: ?>
                                            <div class="font-weight-bold"><?php echo number_format($book['gia']); ?>đ</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="<?php echo $book['so_luong_ton'] < 10 ? 'text-danger font-weight-bold' : ''; ?>">
                                        <?php echo number_format($book['so_luong_ton']); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        switch($book['tinh_trang']) {
                                            case 'Còn hàng': $statusClass = 'badge-success'; break;
                                            case 'Hết hàng': $statusClass = 'badge-warning'; break;
                                            case 'Ngừng kinh doanh': $statusClass = 'badge-danger'; break;
                                            default: $statusClass = 'badge-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($book['tinh_trang']); ?></span>
                                    </td>
                                    <td>
                                        <a href="index.php?page=book_edit&id=<?php echo $book['ma_sach']; ?>" class="btn btn-info btn-sm" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                                data-id="<?php echo $book['ma_sach']; ?>" 
                                                data-name="<?php echo htmlspecialchars($book['ten_sach']); ?>"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">Không tìm thấy sách nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=books&p=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($filters['search']); ?>&category=<?php echo $filters['category']; ?>&status=<?php echo urlencode($filters['status']); ?>&sort_by=<?php echo $filters['sort_by']; ?>&order=<?php echo $filters['order']; ?>">Trước</a>
                        </li>
                        
                        <?php 
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="index.php?page=books&p=1">1</a></li>';
                            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        
                        for ($i = $start; $i <= $end; $i++): 
                        ?>
                        <li class="page-item <?php echo $pagination['current_page'] == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="index.php?page=books&p=<?php echo $i; ?>&search=<?php echo urlencode($filters['search']); ?>&category=<?php echo $filters['category']; ?>&status=<?php echo urlencode($filters['status']); ?>&sort_by=<?php echo $filters['sort_by']; ?>&order=<?php echo $filters['order']; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php 
                        if ($end < $pagination['total_pages']) {
                            if ($end < $pagination['total_pages'] - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo '<li class="page-item"><a class="page-link" href="index.php?page=books&p=' . $pagination['total_pages'] . '">' . $pagination['total_pages'] . '</a></li>';
                        }
                        ?>

                        <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=books&p=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($filters['search']); ?>&category=<?php echo $filters['category']; ?>&status=<?php echo urlencode($filters['status']); ?>&sort_by=<?php echo $filters['sort_by']; ?>&order=<?php echo $filters['order']; ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa sách</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa sách <strong id="delete-book-name"></strong>?<br>
                Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="index.php?page=book_delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="book_id" id="delete-book-id">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle Delete Modal
    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#delete-book-id').val(id);
        $('#delete-book-name').text(name);
        $('#deleteModal').modal('show');
    });

    // Handle Check All
    $('#check-all').click(function() {
        $('.book-check').prop('checked', this.checked);
        toggleBulkBtn();
    });

    $('.book-check').change(function() {
        var allChecked = $('.book-check:checked').length == $('.book-check').length;
        $('#check-all').prop('checked', allChecked);
        toggleBulkBtn();
    });

    function toggleBulkBtn() {
        var count = $('.book-check:checked').length;
        $('#bulk-delete-btn').prop('disabled', count === 0);
    }
</script>
