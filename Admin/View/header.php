<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle admin-profile-btn" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="profile-icon">
                        <i class="fas fa-user-circle"></i>
                    </span>
                    <span class="profile-name mx-2"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in p-0" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo ADMIN_BASE_URL; ?>index.php?page=profile">
                        <i class="fas fa-user col-1 pl-0"></i>
                        Hồ sơ
                    </a>
                    <a class="dropdown-item" href="<?php echo ADMIN_BASE_URL; ?>index.php?page=settings">
                        <i class="fas fa-cogs col-1 pl-0"></i>
                        Cài đặt
                    </a>
                    <div class="dropdown-divider my-0"></div>
                    <a class="dropdown-item text-danger" href="<?php echo ADMIN_BASE_URL; ?>index.php?page=logout">
                        <i class="fas fa-sign-out-alt col-1 pl-0"></i>
                        Đăng xuất
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>