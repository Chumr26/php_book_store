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
    <link rel="stylesheet" href="<?php echo $baseUrl ?? '/book_store'; ?>/Content/CSS/style.css">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            /* Space for fixed top-bar + header + nav (avoid overlap) */
            padding-top: calc(var(--header-h) + var(--nav-h) - 1px);
        }

        :root {
            --header-h: 60px;
            --nav-h: 43px;
        }

        /* Main Header */
        .main-header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            padding: 0;
            height: var(--header-h);
            display: flex;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #007bff;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo:hover {
            color: #0056b3;
            text-decoration: none;
        }

        .logo i {
            margin-right: 8px;
            font-size: 28px;
        }

        /* Search Bar */
        .search-bar {
            position: relative;
        }

        .search-bar form {
            position: relative;
        }

        .search-bar .form-control {
            border-radius: 25px;
            padding-right: 45px;
            border: 2px solid #007bff;
            height: 40px;
        }

        .search-bar .btn-search {
            right: 0;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            padding: 0;
            background-color: #007bff;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .search-bar .btn-search:hover {
            background-color: #0056b3;
        }

        /* Quick Search Results */
        .quick-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1040;
            display: none;
            margin-top: 5px;
        }

        .quick-search-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .quick-search-item:hover {
            background-color: #f8f9fa;
        }

        .quick-search-item img {
            width: 40px;
            height: 60px;
            object-fit: cover;
            margin-right: 15px;
        }

        .quick-search-item .book-info {
            flex: 1;
        }

        .quick-search-item .book-title {
            font-weight: 500;
            color: #333;
            margin-bottom: 3px;
            font-size: 14px;
        }

        .quick-search-item .book-price {
            color: #dc3545;
            font-weight: 600;
            font-size: 13px;
        }

        /* Header Icons */
        .header-icons {
            display: flex;
            align-items: center;
            gap: 15px;
            justify-content: flex-end;
        }

        .header-icon {
            position: relative;
            color: #333;
            font-size: 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .header-icon span {
            font-size: 14px;
            font-weight: 500;
        }

        .header-icon:hover {
            color: #007bff;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background-color: #dc3545;
            color: #fff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
        }

        .user-dropdown {
            position: relative;
        }

        .user-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            margin-top: 10px;
            z-index: 1040;
        }

        .user-menu.show {
            display: block;
        }

        .user-menu a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .user-menu a:hover {
            background-color: #f8f9fa;
        }

        .user-menu a:last-child {
            border-bottom: none;
        }

        /* Navigation */
        .main-nav {
            background-color: #007bff;
            position: fixed;
            top: var(--header-h);
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .main-nav .navbar {
            padding: 0;
        }

        .main-nav .navbar-nav .nav-link {
            color: #fff !important;
            padding: 10px 18px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.2s;
        }

        .main-nav .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .main-nav .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.25);
            font-weight: 600;
        }

        /* Ensure main content starts below fixed nav */
        .main-content {
            margin-top: 0;
            padding-top: 0;
        }

        @media (max-width: 767.98px) {
            :root {
                --header-h: 60px;
                --nav-h: 50px;
            }

            body {
                padding-top: calc(var(--header-h) + var(--nav-h));
            }

            .main-header {
                padding: 5px 0;
            }

            .logo {
                font-size: 20px;
            }
            
            .logo i {
                font-size: 24px;
            }

            .header-icons {
                gap: 10px;
            }

            .header-icon {
                font-size: 18px;
            }
        }

        /* Button Animations */
        .transition-3d-hover {
            transition: all 0.2s ease-in-out;
        }

        .transition-3d-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 11px rgba(0, 123, 255, 0.35) !important;
        }

        /* User Profile Pill Redesign */
        .user-profile-pill {
            display: inline-flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 5px 12px 5px 5px;
            border-radius: 30px;
            text-decoration: none;
            color: #333 !important;
            transition: all 0.2s ease;
            border: 1px solid #e9ecef;
            cursor: pointer;
        }

        .user-profile-pill:hover,
        .user-dropdown.show .user-profile-pill {
            background-color: #e9ecef;
            color: #007bff !important;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .user-avatar-circle {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-right: 10px;
            box-shadow: 0 2px 5px rgba(102, 126, 234, 0.3);
            flex-shrink: 0;
        }

        .user-name-text {
            font-weight: 500;
            font-size: 14px;
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            margin-right: 8px;
        }

        .dropdown-arrow {
            font-size: 12px;
            color: #6c757d;
            transition: transform 0.2s ease;
        }

        .user-profile-pill.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        /* Enhanced Dropdown Menu */
        .user-menu {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            min-width: 240px;
            padding: 8px 0;
            margin-top: 10px;
            display: none;
            background: #fff;
            position: absolute;
            right: 0;
            top: 100%;
            z-index: 1050;
            animation: dropdownSlideIn 0.2s ease;
        }

        .user-menu.show {
            display: block;
        }

        @keyframes dropdownSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-menu-header {
            padding: 12px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            margin-top: -8px;
            border-radius: 12px 12px 0 0;
        }

        .user-menu a {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            color: #495057;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            border-bottom: none;
        }

        .user-menu a:hover {
            background-color: #f8f9fa;
            color: #007bff;
            padding-left: 25px;
        }

        .user-menu a i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .user-info-mini strong {
            font-size: 15px;
            color: #333;
        }
    </style>
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