<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết đơn hàng #<?php echo $order['id_hoadon']; ?></h1>
        <div>
            <a href="index.php?page=order_print_invoice&id=<?php echo $order['id_hoadon']; ?>" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-print fa-sm text-white-50"></i> In hóa đơn
            </a>
            <a href="index.php?page=orders" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Info -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold"><?php echo htmlspecialchars($item['ten_sach']); ?></div>
                                    </td>
                                    <td class="text-center"><?php echo $item['so_luong']; ?></td>
                                    <td class="text-right"><?php echo number_format($item['don_gia']); ?>đ</td>
                                    <td class="text-right"><?php echo number_format($item['so_luong'] * $item['don_gia']); ?>đ</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold">Tổng tiền hàng:</td>
                                    <td class="text-right"><?php echo number_format($order['tong_tien']); ?>đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold">Phí vận chuyển:</td>
                                    <td class="text-right">0đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold text-primary h5">Tổng thanh toán:</td>
                                    <td class="text-right font-weight-bold text-primary h5"><?php echo number_format($order['tong_thanh_toan']); ?>đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cập nhật trạng thái</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?page=order_update_status" method="POST" class="form-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $order['id_hoadon']; ?>">
                        
                        <div class="form-group mb-2 flex-grow-1">
                            <label for="status" class="sr-only">Trạng thái</label>
                            <select class="form-control w-100" id="status" name="status">
                                <?php foreach ($order_statuses as $key => $value): ?>
                                    <option value="<?php echo $key; ?>" 
                                        <?php echo $order['trang_thai_don_hang'] == $key ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($value); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mb-2 ml-2">Cập nhật</button>
                    </form>
                    <small class="text-muted">
                        Lưu ý: Chỉ một số chuyển đổi trạng thái là hợp lệ (Ví dụ: Chờ xác nhận -> Đã xác nhận/Hủy).
                    </small>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Họ tên:</label>
                        <div class="h6"><?php echo htmlspecialchars($customer['ho_ten']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Email:</label>
                        <div class="h6"><?php echo htmlspecialchars($customer['email']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Số điện thoại:</label>
                        <div class="h6"><?php echo htmlspecialchars($customer['so_dien_thoai']); ?></div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin giao hàng</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Người nhận:</label>
                        <div class="h6"><?php echo htmlspecialchars($order['ten_nguoi_nhan']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Địa chỉ:</label>
                        <div class="h6"><?php echo htmlspecialchars($order['dia_chi_giao_hang']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Số điện thoại:</label>
                        <div class="h6"><?php echo htmlspecialchars($order['sdt_nguoi_nhan']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Ghi chú:</label>
                        <div class="h6"><?php echo htmlspecialchars($order['ghi_chu'] ?? 'Không có'); ?></div>
                    </div>
                </div>
            </div>

             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Phương thức:</label>
                        <div class="h6"><?php echo htmlspecialchars($order['phuong_thuc_thanh_toan']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">Trạng thái:</label>
                        <div>
                             <?php
                            $paymentClass = 'secondary';
                            switch($order['trang_thai_thanh_toan']) {
                                case 'Chờ thanh toán': $paymentClass = 'warning'; break;
                                case 'Đã thanh toán': $paymentClass = 'success'; break;
                                case 'Thanh toán thất bại': $paymentClass = 'danger'; break;
                                case 'Hoàn tiền': $paymentClass = 'dark'; break;
                            }
                            ?>
                            <span class="badge badge-<?php echo $paymentClass; ?>"><?php echo htmlspecialchars($order['trang_thai_thanh_toan']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
