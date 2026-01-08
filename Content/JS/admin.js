
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

/**
 * Standardized Admin Dropdown Handler
 * 
 * This function handles the pill-style dropdown selections across the admin panel.
 * It updates the hidden input value and the dropdown button text when an option is selected.
 * 
 * @param {string} name - The base name of the dropdown (matches the hidden input ID without '_input' suffix)
 * @param {string} value - The value to set for the hidden input
 * @param {string} label - The display text to show on the dropdown button
 * 
 * Usage:
 * <a class="dropdown-item" onclick="selectOption('status', 'active', 'Active')">
 * 
 * Required HTML structure:
 * - Hidden input with ID: {name}_input
 * - Dropdown button with ID: {name}Dropdown (or variations)
 * - Button must contain element with class 'text-value' for the label
 */
function selectOption(name, value, label) {
    // Update hidden input
    var hiddenInput = document.getElementById(name + '_input');
    if (hiddenInput) {
        hiddenInput.value = value;
    }
    
    // Find the dropdown button using various naming conventions
    var btn = document.getElementById(name + 'Dropdown') || 
              document.getElementById(name.replace('_', '') + 'Dropdown');
    
    if (!btn) {
        // Try alternative naming conventions (camelCase, etc.)
        var possibleIds = [
            name + 'Dropdown',
            name.replace('_', '') + 'Dropdown',
            name.charAt(0).toUpperCase() + name.slice(1).replace('_', '') + 'Dropdown',
            name.split('_').map((word, index) => 
                index === 0 ? word : word.charAt(0).toUpperCase() + word.slice(1)
            ).join('') + 'Dropdown'
        ];
        
        for (var i = 0; i < possibleIds.length; i++) {
            btn = document.getElementById(possibleIds[i]);
            if (btn) break;
        }
    }
    
    if (btn) {
        // Update button text
        var textElement = btn.querySelector('.text-value');
        if (textElement) {
            textElement.textContent = label;
        }
        
        // Close the dropdown using Bootstrap's dropdown method
        if (typeof jQuery !== 'undefined') {
            jQuery(btn).dropdown('toggle');
        }
    }
}
