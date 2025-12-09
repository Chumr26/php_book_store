<?php
/**
 * Pagination Helper Class
 * 
 * Handles pagination calculations and generation
 */

class Pagination {
    private $totalRecords;
    private $perPage;
    private $currentPage;
    private $totalPages;
    
    /**
     * Constructor
     * 
     * @param int $totalRecords Total number of records
     * @param int $perPage Records per page
     * @param int $currentPage Current page number
     */
    public function __construct($totalRecords, $perPage = 12, $currentPage = 1) {
        $this->totalRecords = (int)$totalRecords;
        $this->perPage = (int)$perPage;
        $this->currentPage = max(1, (int)$currentPage);
        $this->totalPages = ceil($this->totalRecords / $this->perPage);
        
        // Adjust current page if it exceeds total pages
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
    }
    
    /**
     * Get SQL OFFSET value
     * 
     * @return int Offset for SQL query
     */
    public function getOffset() {
        return ($this->currentPage - 1) * $this->perPage;
    }
    
    /**
     * Get SQL LIMIT value
     * 
     * @return int Limit for SQL query
     */
    public function getLimit() {
        return $this->perPage;
    }
    
    /**
     * Get total pages
     * 
     * @return int Total number of pages
     */
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    /**
     * Get current page
     * 
     * @return int Current page number
     */
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    /**
     * Check if there is a next page
     * 
     * @return bool True if next page exists
     */
    public function hasNextPage() {
        return $this->currentPage < $this->totalPages;
    }
    
    /**
     * Check if there is a previous page
     * 
     * @return bool True if previous page exists
     */
    public function hasPreviousPage() {
        return $this->currentPage > 1;
    }
    
    /**
     * Get next page number
     * 
     * @return int|null Next page number or null
     */
    public function getNextPage() {
        return $this->hasNextPage() ? $this->currentPage + 1 : null;
    }
    
    /**
     * Get previous page number
     * 
     * @return int|null Previous page number or null
     */
    public function getPreviousPage() {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }
    
    /**
     * Get pagination info summary
     * 
     * @return array Pagination information
     */
    public function getInfo() {
        $start = ($this->currentPage - 1) * $this->perPage + 1;
        $end = min($this->currentPage * $this->perPage, $this->totalRecords);
        
        return [
            'total_records' => $this->totalRecords,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages,
            'start' => $start,
            'end' => $end,
            'has_next' => $this->hasNextPage(),
            'has_previous' => $this->hasPreviousPage()
        ];
    }
    
    /**
     * Generate HTML pagination links
     * 
     * @param string $baseUrl Base URL for pagination links
     * @param int $displayPages Number of page links to display
     * @return string HTML pagination
     */
    public function generateHTML($baseUrl, $displayPages = 5) {
        if ($this->totalPages <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center">';
        
        // Previous button
        if ($this->hasPreviousPage()) {
            $prevUrl = $baseUrl . '?page=' . $this->getPreviousPage();
            $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '">Trước</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Trước</span></li>';
        }
        
        // Page numbers
        $start = max(1, $this->currentPage - floor($displayPages / 2));
        $end = min($this->totalPages, $start + $displayPages - 1);
        
        // Adjust start if we're near the end
        if ($end - $start < $displayPages - 1) {
            $start = max(1, $end - $displayPages + 1);
        }
        
        // First page + ellipsis
        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=1">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Page numbers
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $this->currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $pageUrl = $baseUrl . '?page=' . $i;
                $html .= '<li class="page-item"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
            }
        }
        
        // Ellipsis + last page
        if ($end < $this->totalPages) {
            if ($end < $this->totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $lastUrl = $baseUrl . '?page=' . $this->totalPages;
            $html .= '<li class="page-item"><a class="page-link" href="' . $lastUrl . '">' . $this->totalPages . '</a></li>';
        }
        
        // Next button
        if ($this->hasNextPage()) {
            $nextUrl = $baseUrl . '?page=' . $this->getNextPage();
            $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">Sau</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Sau</span></li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
}
?>
