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
            padding-top: 90px;
        }

        :root {
            --topbar-h: 38px;
            --header-h: 50px;
            --nav-h: 46px;
        }

        /* Top Bar */
        .top-bar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px 0;
            font-size: 14px;
        }

        .top-bar a {
            color: #6c757d;
            text-decoration: none;
            margin: 0 10px;
        }

        .top-bar a:hover {
            color: #007bff;
        }

        /* Main Header */
        .main-header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: var(--topbar-h);
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 15px 0;
            height: var(--header-h);
            display: flex;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #007bff;
            text-decoration: none;
        }

        .logo:hover {
            color: #0056b3;
            text-decoration: none;
        }

        .logo i {
            margin-right: 8px;
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
        }

        .search-bar .btn-search {
            right: 0;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            width: 37px;
            height: 37px;
            padding: 0;
            background-color: #007bff;
            border: none;
        }

        .search-bar .btn-search {
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
            z-index: 1001;
            display: none;
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
            width: 50px;
            height: 70px;
            object-fit: cover;
            margin-right: 15px;
        }

        .quick-search-item .book-info {
            flex: 1;
        }

        .quick-search-item .book-title {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }

        .quick-search-item .book-price {
            color: #dc3545;
            font-weight: 600;
        }

        /* Header Icons */
        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
            justify-content: flex-end;
        }

        .header-icon {
            position: relative;
            color: #333;
            font-size: 22px;
            text-decoration: none;
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
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: #fff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
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
            z-index: 1001;
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
            top: calc(var(--topbar-h) + var(--header-h));
            left: 0;
            right: 0;
            z-index: 999;
        }

        .main-nav .navbar {
            padding: 0;
        }

        .main-nav .navbar-nav .nav-link {
            color: #fff !important;
            padding: 12px 20px;
            font-weight: 500;
        }

        .main-nav .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-nav .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Ensure main content starts below fixed nav */
        .main-content {
            margin-top: 46px;
        }

        @media (max-width: 767.98px) {
            :root {
                --header-h: 110px;
                --nav-h: 52px;
            }

            body {
                padding-top: calc(var(--topbar-h) + var(--header-h) + var(--nav-h) + 10px);
            }

            .header-icons {
                gap: 12px;
            }

            .header-icon span {
                font-size: 13px;
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
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar fixed-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span><i class="fas fa-phone"></i> Hotline: 1900-xxxx</span>
                    <span class="ml-3"><i class="fas fa-envelope"></i> support@bookstore.vn</span>
                </div>
                <div class="col-md-6 text-right">
                    <a href="?page=orders"><i class="fas fa-box"></i> Đơn hàng của tôi</a>
                    <a href="#"><i class="fas fa-question-circle"></i> Trợ giúp</a>
                </div>
            </div>
        </div>
    </div>

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
                <div class="col-md-5">
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
                <div class="col-md-4 text-right">
                    <div class="header-icons">
                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <!-- Logged in user -->
                            <div class="user-dropdown">
                                <a href="#" class="header-icon" id="userDropdownBtn">
                                    <i class="fas fa-user-circle"></i>
                                    <span class="ml-2"><?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Tài khoản'); ?></span>
                                </a>
                                <div class="user-menu" id="userMenu">
                                    <a href="?page=orders"><i class="fas fa-box"></i> Đơn hàng của tôi</a>
                                    <a href="?page=profile"><i class="fas fa-user"></i> Thông tin cá nhân</a>
                                    <a href="?page=change_password"><i class="fas fa-key"></i> Đổi mật khẩu</a>
                                    <a href="?page=logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
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