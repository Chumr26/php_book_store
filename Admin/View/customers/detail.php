<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-modern">
            <li class="breadcrumb-item"><a href="index.php?page=customers"><i class="fas fa-users"></i> Quản lý khách hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết khách hàng</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 page-header-modern">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user"></i> Chi tiết khách hàng
        </h1>
        <a href="index.php?page=customers" class="btn btn-secondary btn-icon-split shadow-sm">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Quay lại danh sách</span>
        </a>
    </div>

    <!-- Customer Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-circle mr-2"></i>Thông tin khách hàng</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <div class="profile-icon mx-auto" style="width: 100px; height: 100px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; box-shadow: 0 .5rem 1.5rem rgba(78, 115, 223, .3);">
                        <?php echo strtoupper(substr($customer['ho_ten'], 0, 1)); ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500 mb-1"><i class="fas fa-user mr-2 text-gray-400"></i>Họ tên:</label>
                        <div class="h5 font-weight-bold text-primary mb-0"><?php echo htmlspecialchars($customer['ho_ten']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500 mb-1"><i class="fas fa-envelope mr-2 text-gray-400"></i>Email:</label>
                        <div class="h6 mb-0"><?php echo htmlspecialchars($customer['email']); ?></div>
                    </div>
                    <div class="mb-0">
                        <label class="small font-weight-bold text-gray-500 mb-1"><i class="fas fa-phone mr-2 text-gray-400"></i>Số điện thoại:</label>
                        <div class="h6 mb-0"><?php echo htmlspecialchars($customer['so_dien_thoai']); ?></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500 mb-1"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>Địa chỉ:</label>
                        <div class="h6 mb-0"><?php echo htmlspecialchars($customer['dia_chi']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500 mb-1"><i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Ngày tham gia:</label>
                        <div class="h6 mb-0"><?php echo date('d/m/Y H:i', strtotime($customer['ngay_tao'])); ?></div>
                    </div>
                    <div class="mb-0">
                        <label class="small font-weight-bold text-gray-500 mb-2"><i class="fas fa-toggle-on mr-2 text-gray-400"></i>Trạng thái:</label>
                        <form action="index.php?page=customer_update_status" method="POST" id="statusForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="customer_id" value="<?php echo $customer['id_khachhang']; ?>">
                            <div class="input-group">
                                <div class="dropdown" style="flex: 1;">
                                    <input type="hidden" name="status" id="customer_status_input" value="<?php echo htmlspecialchars($customer['trang_thai']); ?>">
                                    <button class="btn admin-dropdown-toggle" type="button" id="customerStatusDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="text-value">
                                            <?php
                                            $statusLabels = [
                                                'active' => 'Hoạt động',
                                                'inactive' => 'Không hoạt động',
                                                'banned' => 'Bị khóa'
                                            ];
                                            echo htmlspecialchars($statusLabels[$customer['trang_thai']] ?? 'Hoạt động');
                                            ?>
                                        </span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="customerStatusDropdown">
                                        <div class="admin-dropdown-menu-scrollable">
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('customer_status', 'active', 'Hoạt động')">Hoạt động</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('customer_status', 'inactive', 'Không hoạt động')">Không hoạt động</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="selectOption('customer_status', 'banned', 'Bị khóa')">Bị khóa</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-icon-split" type="submit">
                                        <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                                        <span class="text">Lưu</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng chi tiêu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['total_spent']); ?>đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đơn hàng thành công</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['completed_orders']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
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
    </div>

    <!-- Order History -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shopping-cart mr-2"></i>Lịch sử đơn hàng</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 100px">Mã ĐH</th>
                            <th style="width: 120px">Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th style="width: 150px">Trạng thái</th>
                            <th style="width: 80px" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="align-middle">
                                    <span class="badge badge-light border">#<?php echo $order['id_hoadon']; ?></span>
                                </td>
                                <td class="align-middle"><?php echo date('d/m/Y', strtotime($order['ngay_dat'])); ?></td>
                                <td class="align-middle">
                                    <span class="price-tag"><?php echo number_format($order['tong_thanh_toan']); ?>đ</span>
                                </td>
                                <td class="align-middle">
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
                                    <span class="badge badge-pill-custom badge-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['trang_thai_don_hang']); ?></span>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="index.php?page=order_detail&id=<?php echo $order['id_hoadon']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-gray-500 mb-2"><i class="fas fa-shopping-cart fa-3x"></i></div>
                                    <p>Chưa có đơn hàng nào</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    // Standardized Admin Dropdown Handler
    function selectOption(name, value, label) {
        // Update hidden input
        document.getElementById(name + '_input').value = value;
        // Update button text
        var btn = document.getElementById(name + 'Dropdown') || document.getElementById(name.replace('_', '') + 'Dropdown');
        if (!btn) {
            // Try alternative naming conventions
            var possibleIds = [name + 'Dropdown', name.replace('_', '') + 'Dropdown', name.charAt(0).toUpperCase() + name.slice(1) + 'Dropdown'];
            for (var i = 0; i < possibleIds.length; i++) {
                btn = document.getElementById(possibleIds[i]);
                if (btn) break;
            }
        }
        if (btn) {
            var textElement = btn.querySelector('.text-value');
            if (textElement) {
                textElement.textContent = label;
            }
            // Close the dropdown using Bootstrap's dropdown method
            $(btn).dropdown('toggle');
        }
    }
</script>