<nav id="sidebar" class="sidebar">
    <div class="sidebar-header" id="sidebarCollapse" style="cursor: pointer;">
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
                <i class="fas fa-list"></i> <span>Thể loại</span>
            </a>
        </li>
        
        <li class="<?php echo ($page === 'customers') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=customers">
                <i class="fas fa-users"></i> <span>Khách hàng</span>
            </a>
        </li>

        <li class="<?php echo ($page === 'settings') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=settings">
                <i class="fas fa-cog"></i> <span>Cài đặt</span>
            </a>
        </li>
    </ul>
</nav>
