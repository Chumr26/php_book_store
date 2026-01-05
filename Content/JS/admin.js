
(function ($) {
    "use strict"; // Start of use strict

    // Toggle the side navigation
    $("#sidebarCollapse").on('click', function (e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");

        // Save state to localStorage
        if ($("body").hasClass("sidebar-toggled")) {
            localStorage.setItem('sb|sidebar-toggle', 'true');
        } else {
            localStorage.setItem('sb|sidebar-toggle', 'false');
        }
    });

    // Restore state from localStorage
    $(document).ready(function () {
        var sidebar = localStorage.getItem('sb|sidebar-toggle');
        if (sidebar === 'true') {
            $("body").addClass("sidebar-toggled");
            $(".sidebar").addClass("toggled");
        }
    });

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function (e) {
        if ($(window).width() > 768) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

})(jQuery); // End of use strict
