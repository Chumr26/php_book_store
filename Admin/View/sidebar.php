<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-book-open"></i> BookStore</h3>
    </div>

    <ul class="list-unstyled components">
        <li class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        
        <li class="<?php echo ($page === 'orders') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=orders">
                <i class="fas fa-shopping-cart"></i> Đơn hàng
            </a>
        </li>

        <li class="<?php echo ($page === 'books') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=books">
                <i class="fas fa-book"></i> Sách
            </a>
        </li>

        <li class="<?php echo ($page === 'categories') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=categories">
                <i class="fas fa-list"></i> Thể loại
            </a>
        </li>
        
        <li class="<?php echo ($page === 'customers') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=customers">
                <i class="fas fa-users"></i> Khách hàng
            </a>
        </li>

        <li class="<?php echo ($page === 'settings') ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_BASE_URL; ?>index.php?page=settings">
                <i class="fas fa-cog"></i> Cài đặt
            </a>
        </li>
    </ul>
</nav>
