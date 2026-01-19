    </div>
    <!-- End Main Content -->

    <!-- Footer -->
    <footer class="footer bg-dark text-white mt-5">
        <div class="container py-5">
            <div class="row">
                <!-- About Us -->
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-book-reader"></i> BookStore
                    </h5>
                    <p class="text-muted">
                        Nhà sách trực tuyến hàng đầu Việt Nam, cung cấp hàng ngàn đầu sách chất lượng với giá tốt nhất.
                    </p>
                    <div class="social-links mt-3">
                        <a href="#" class="text-white mr-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white mr-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white mr-3"><i class="fab fa-youtube fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>

                <!-- Customer Service -->
                <div class="col-md-3 mb-4">
                    <h6 class="mb-3">DỊCH VỤ KHÁCH HÀNG</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Chính sách đổi trả</a></li>
                        <li><a href="#" class="text-muted">Chính sách bảo mật</a></li>
                        <li><a href="#" class="text-muted">Chính sách vận chuyển</a></li>
                        <li><a href="#" class="text-muted">Phương thức thanh toán</a></li>
                        <li><a href="#" class="text-muted">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div class="col-md-3 mb-4">
                    <h6 class="mb-3">LIÊN KẾT NHANH</h6>
                    <ul class="list-unstyled">
                        <li><a href="?page=home" class="text-muted">Trang chủ</a></li>
                        <li><a href="?page=books" class="text-muted">Sản phẩm</a></li>
                        <li><a href="?page=books&sort=new" class="text-muted">Sách mới</a></li>
                        <li><a href="?page=books&sort=bestseller" class="text-muted">Sách bán chạy</a></li>
                        <li><a href="#" class="text-muted">Tin tức</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-md-3 mb-4">
                    <h6 class="mb-3">LIÊN HỆ</h6>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            123 Nguyễn Huệ, Q.1, TP.HCM
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone mr-2"></i>
                            Hotline: 1900-xxxx
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope mr-2"></i>
                            support@bookstore.vn
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock mr-2"></i>
                            T2-T7: 8:00 - 22:00<br>
                            <span class="ml-4">CN: 9:00 - 21:00</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <!-- Payment Methods -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-2"><small>PHƯƠNG THỨC THANH TOÁN</small></p>
                    <div class="payment-methods">
                        <img src="<?php echo BASE_URL; ?>Content/images/icons/vnpay.png"
                            alt="VNPay" class="mr-2" style="height: 30px;">
                        <img src="<?php echo BASE_URL; ?>Content/images/icons/momo.png"
                            alt="MoMo" class="mr-2" style="height: 30px;">
                        <img src="<?php echo BASE_URL; ?>Content/images/icons/zalopay.png"
                            alt="ZaloPay" class="mr-2" style="height: 30px;">
                        <img src="<?php echo BASE_URL; ?>Content/images/icons/visa.png"
                            alt="Visa" class="mr-2" style="height: 30px;">
                        <img src="<?php echo BASE_URL; ?>Content/images/icons/mastercard.png"
                            alt="Mastercard" style="height: 30px;">
                    </div>
                </div>
                <div class="col-md-6 text-md-right">
                    <p class="mb-2"><small>ĐỐI TÁC VẬN CHUYỂN</small></p>
                    <div class="shipping-partners">
                        <img src="<?php echo BASE_URL; ?>Content/images/icons/ghn.png"
                            alt="GHN" class="mr-2" style="height: 30px;">
                        <img src="<?php echo BASE_URL; ?>Content/images/icons/ghtk.png"
                            alt="GHTK" class="mr-2" style="height: 30px;">
                        <img src="<?php echo BASE_URL; ?>Content/images/icons/vnpost.png"
                            alt="VNPost" style="height: 30px;">
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <!-- Copyright -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0 text-muted">
                        &copy; <?php echo date('Y'); ?> BookStore. All rights reserved.
                        Designed with <i class="fas fa-heart text-danger"></i> by Khoa
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Confirmation Modal -->
    <div class="modal fade" id="globalConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="globalConfirmTitle">Xác nhận</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="globalConfirmMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="globalConfirmBtn">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Message Modal -->
    <div class="modal fade" id="globalMessageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="globalMessageTitle">Thông báo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="globalMessageContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // CSRF Token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"] ?? ""; ?>'
                }
            });

            // User dropdown toggle
            $('#userDropdownBtn').click(function(e) {
                e.preventDefault();
                $('#userMenu').toggleClass('show');
                $(this).toggleClass('active');
            });

            // Close dropdown when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('.user-dropdown').length) {
                    $('#userMenu').removeClass('show');
                    $('#userDropdownBtn').removeClass('active');
                }
            });

            // Quick search
            let searchTimeout;
            $('#quickSearch').on('keyup', function() {
                clearTimeout(searchTimeout);
                const keyword = $(this).val().trim();

                if (keyword.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        $.ajax({
                            url: '?page=ajax_quick_search',
                            method: 'POST',
                            data: {
                                keyword: keyword
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success && response.data.length > 0) {
                                    let html = '';
                                    response.data.forEach(function(book) {
                                        const coverUrl = book.isbn ?
                                            `/book_store/?page=cover&isbn=${encodeURIComponent(String(book.isbn).replace(/[^0-9Xx]/g, ''))}` :
                                            '/book_store/Content/images/books/no-image.jpg';
                                        html += `
                                            <a href="?page=book_detail&id=${book.ma_sach}" class="quick-search-item text-decoration-none">
                                                <img src="${coverUrl}" alt="${book.ten_sach}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/book_store/Content/images/books/no-image.jpg';">
                                                <div class="book-info">
                                                    <div class="book-title">${book.ten_sach}</div>
                                                    <div class="book-price">${formatPrice(book.gia)} đ</div>
                                                </div>
                                            </a>
                                        `;
                                    });
                                    $('#quickSearchResults').html(html).show();
                                } else {
                                    $('#quickSearchResults').html('<div class="quick-search-item">Không tìm thấy kết quả</div>').show();
                                }
                            }
                        });
                    }, 300);
                } else {
                    $('#quickSearchResults').hide();
                }
            });

            // Close quick search when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('.search-bar').length) {
                    $('#quickSearchResults').hide();
                }
            });

            // Update cart badge
            function updateCartBadge() {
                $.ajax({
                    url: '?page=ajax_cart_summary',
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#cartBadge').text(response.data.item_count || 0);
                        }
                    }
                });
            }

            // Call on page load
            updateCartBadge();

            // Back to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#backToTop').fadeIn();
                } else {
                    $('#backToTop').fadeOut();
                }
            });

            $('#backToTop').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 600);
                return false;
            });

            // Format price helper
            window.formatPrice = function(price) {
                return new Intl.NumberFormat('vi-VN').format(price);
            };

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Global Modal Helpers
            window.showMessageModal = function(title, message) {
                $('#globalMessageTitle').text(title);
                $('#globalMessageContent').html(message);
                $('#globalMessageModal').modal('show');
            };

            let globalConfirmCallback = null;
            window.showConfirmModal = function(message, callback) {
                $('#globalConfirmMessage').text(message);
                $('#globalConfirmModal').modal('show');
                globalConfirmCallback = callback;
            };

            $('#globalConfirmBtn').click(function() {
                if (globalConfirmCallback) {
                    globalConfirmCallback();
                    $('#globalConfirmModal').modal('hide');
                    globalConfirmCallback = null;
                }
            });
        });
    </script>

    <style>
        footer a {
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: #007bff !important;
        }

        .social-links a {
            transition: transform 0.3s;
            display: inline-block;
        }

        .social-links a:hover {
            transform: translateY(-3px);
        }

        footer ul li {
            margin-bottom: 8px;
        }
    </style>
    </body>

    </html>