<?php

if (!function_exists('render_component')) {
    /**
     * Renders a component view file with specific data.
     * 
     * @param string $componentPath The path to the component (relative to View/components or Admin/View/components).
     *                              Example: 'book_card' or 'admin/stat_card'
     * @param array $data Associative array of data to extract for the component.
     * @param bool $isAdmin Whether the component is in the Admin directory.
     */
    function render_component($componentPath, $data = [], $isAdmin = false)
    {
        // Extract data to variables
        extract($data);

        // Determine base path
        $baseDir = $isAdmin ? 'Admin/View/components/' : 'View/components/';

        // Handle nested paths (e.g. 'admin/stat_card') if strict path is provided
        // But our standard is: 
        // Public components: View/components/*.php
        // Admin components: Admin/View/components/*.php

        $fullPath = BASE_PATH . $baseDir . $componentPath . '.php';

        if (file_exists($fullPath)) {
            include $fullPath;
        } else {
            // Fallback debugging
            echo "<!-- Component not found: {$fullPath} -->";
        }
    }
}

if (!function_exists('render_admin_component')) {
    /**
     * Shorthand for rendering an admin component.
     */
    function render_admin_component($componentPath, $data = [])
    {
        render_component($componentPath, $data, true);
    }
}
