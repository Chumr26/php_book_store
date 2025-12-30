<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <button type="button" id="sidebarCollapse" class="btn btn-primary">
        <i class="fas fa-bars"></i>
    </button>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> 
                    <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo ADMIN_BASE_URL; ?>index.php?page=profile">Hồ sơ</a>
                    <a class="dropdown-item" href="<?php echo ADMIN_BASE_URL; ?>index.php?page=settings">Cài đặt</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="<?php echo ADMIN_BASE_URL; ?>index.php?page=logout">Đăng xuất</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
