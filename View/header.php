<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Nhà sách trực tuyến</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- jQuery (Load before any scripts that use it) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/components.css">

    <?php
    $page = $_GET['page'] ?? 'home';
    if ($page === 'home'):
    ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/pages/homepage.css">
    <?php endif; ?>
    <?php if ($page === 'books'): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/pages/books.css">
    <?php endif; ?>
</head>

<body>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-md-3">
                    <a href="?page=home" class="logo">
                        <i class="fas fa-book-reader"></i>
                        BookStore
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="col-md-6">
                    <div class="search-bar">
                        <form action="?page=search" method="GET">
                            <input type="hidden" name="page" value="search">
                            <input type="text"
                                class="form-control"
                                name="keyword"
                                id="quickSearch"
                                placeholder="Tìm kiếm sách, tác giả, nhà xuất bản..."
                                value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
                                autocomplete="off">
                            <button type="submit" class="btn btn-primary btn-search">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <div class="quick-search-results" id="quickSearchResults"></div>
                    </div>
                </div>

                <!-- Header Icons -->
                <div class="col-md-3 text-right">
                    <div class="header-icons">
                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <!-- Logged in user -->
                            <div class="user-dropdown">
                                <a href="#" class="user-profile-pill" id="userDropdownBtn">
                                    <div class="user-avatar-circle">
                                        <?php
                                        $initial = mb_substr($_SESSION['customer_name'] ?? 'U', 0, 1, 'UTF-8');
                                        echo strtoupper($initial);
                                        ?>
                                    </div>
                                    <span class="user-name-text"><?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Tài khoản'); ?></span>
                                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                                </a>
                                <div class="user-menu" id="userMenu">
                                    <div class="user-menu-header">
                                        <div class="user-info-mini">
                                            <!-- <strong><?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Khách hàng'); ?></strong> -->
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($_SESSION['customer_email'] ?? ''); ?></small>
                                        </div>
                                    </div>
                                    <!-- <div class="dropdown-divider"></div> -->
                                    <a href="?page=orders"><i class="fas fa-box text-primary"></i> Đơn hàng của tôi</a>
                                    <a href="?page=profile"><i class="fas fa-user text-info"></i> Thông tin cá nhân</a>
                                    <a href="?page=change_password"><i class="fas fa-key text-warning"></i> Đổi mật khẩu</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="?page=logout" class="text-danger"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Guest user -->
                            <a href="?page=login" class="btn btn-outline-primary btn-sm rounded-pill px-3 font-weight-bold transition-3d-hover">
                                <i class="fas fa-sign-in-alt mr-1"></i> Đăng nhập
                            </a>
                        <?php endif; ?>

                        <a href="?page=cart" class="header-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge" id="cartBadge"><?php echo $globalCartCount ?? 0; ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container">
            <ul class="navbar-nav d-flex flex-row">
                <li class="nav-item">
                    <a class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>"
                        href="?page=home">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'books') ? 'active' : ''; ?>"
                        href="?page=books">
                        <i class="fas fa-book"></i> Sách
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'category') ? 'active' : ''; ?>"
                        href="?page=books&category=all">
                        <i class="fas fa-list"></i> Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=books&sort=bestseller">
                        <i class="fas fa-star"></i> Bán chạy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=books&sort=new">
                        <i class="fas fa-certificate"></i> Sách mới
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=books&sort=discount">
                        <i class="fas fa-gift"></i> Khuyến mãi
                    </a>
                </li> -->
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <!-- <div class="main-content"> -->