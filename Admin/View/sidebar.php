<nav id="sidebar" class="sidebar">
    <div class="sidebar-header" id="sidebarCollapse">
        <h3><i class="fas fa-book-open"></i> <span>BookStore</span></h3>
    </div>

    <ul class="list-unstyled components">
        <li class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=dashboard">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
        </li>

        <li class="<?php echo ($page === 'orders') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=orders">
                <i class="fas fa-shopping-cart"></i> <span>Đơn hàng</span>
            </a>
        </li>

        <li class="<?php echo ($page === 'books') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=books">
                <i class="fas fa-book"></i> <span>Sách</span>
            </a>
        </li>

        <li class="<?php echo ($page === 'categories') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=categories">
                <i class="fas fa-folder-open"></i> <span>Thể loại</span>
            </a>
        </li>

        <li class="<?php echo ($page === 'authors') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=authors">
                <i class="fas fa-pen-nib"></i> <span>Tác giả</span>
            </a>
        </li>

        <li class="<?php echo ($page === 'publishers') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=publishers">
                <i class="fas fa-building"></i> <span>Nhà xuất bản</span>
            </a>
        </li>

        <li class="<?php echo ($page === 'customers') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=customers">
                <i class="fas fa-users"></i> <span>Khách hàng</span>
            </a>
        </li>

        <li class="<?php echo ($page === 'coupons') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=admin_coupons">
                <i class="fas fa-ticket-alt"></i> <span>Mã giảm giá</span>
            </a>
        </li>

        <!-- <li class="<?php echo ($page === 'settings') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=settings">
                <i class="fas fa-cog"></i> <span>Cài đặt</span>
            </a>
        </li> -->
    </ul>
</nav>