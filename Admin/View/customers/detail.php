<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết khách hàng</h1>
        <a href="index.php?page=customers" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <!-- Customer Info -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin cá nhân</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle" src="https://source.unsplash.com/QAB-WJcbgJk/120x120" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Họ tên:</label>
                        <div class="h5 font-weight-bold text-primary"><?php echo htmlspecialchars($customer['ho_ten']); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Email:</label>
                        <div class="h6"><?php echo htmlspecialchars($customer['email']); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Số điện thoại:</label>
                        <div class="h6"><?php echo htmlspecialchars($customer['so_dien_thoai']); ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Địa chỉ:</label>
                        <div class="h6"><?php echo htmlspecialchars($customer['dia_chi']); ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Ngày tham gia:</label>
                        <div class="h6"><?php echo date('d/m/Y H:i', strtotime($customer['ngay_tao'])); ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Trạng thái tài khoản:</label>
                        <form action="index.php?page=customer_update_status" method="POST" class="mt-2">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="customer_id" value="<?php echo $customer['ma_khach_hang']; ?>">
                            
                            <div class="input-group">
                                <select class="custom-select" name="status">
                                    <option value="active" <?php echo $customer['trang_thai'] == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                    <option value="inactive" <?php echo $customer['trang_thai'] == 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                                    <option value="banned" <?php echo $customer['trang_thai'] == 'banned' ? 'selected' : ''; ?>>Bị khóa</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Lưu</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                 <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê mua sắm</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-4">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng chi tiêu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['total_spent']); ?>đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <div class="row no-gutters align-items-center mb-4">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đơn hàng thành công</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['completed_orders']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                        </div>
                    </div>

                     <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Giá trị TB/Đơn</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['avg_order_value']); ?>đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id_hoadon']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($order['ngay_dat'])); ?></td>
                                        <td><?php echo number_format($order['tong_thanh_toan']); ?>đ</td>
                                        <td>
                                            <?php
                                            $statusClass = 'secondary';
                                            switch($order['trang_thai_don_hang']) {
                                                case 'Đã giao': $statusClass = 'success'; break;
                                                case 'Đã hủy': $statusClass = 'danger'; break;
                                                case 'Đang xử lý': $statusClass = 'primary'; break;
                                                case 'Đang giao hàng': $statusClass = 'info'; break;
                                                default: $statusClass = 'warning';
                                            }
                                            ?>
                                            <span class="badge badge-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['trang_thai_don_hang']); ?></span>
                                        </td>
                                        <td>
                                            <a href="index.php?page=order_detail&id=<?php echo $order['id_hoadon']; ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Chưa có đơn hàng nào</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
