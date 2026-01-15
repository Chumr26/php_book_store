<?php
/**
 * Reviews Model Class
 *
 * This project uses Vietnamese table names:
 * - Reviews: danhgia
 * - Books: sach
 * - Customers: khachhang
 */

class Reviews {
    private $conn;

    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }

    /**
     * Check if a customer has purchased a book (completed orders only).
     *
     * Business rule: only customers with a completed order containing the book can review.
     */
    public function customerHasPurchasedBook($customerId, $bookId) {
        $sql = "SELECT 1
                FROM hoadon hd
                INNER JOIN chitiet_hoadon ct ON hd.id_hoadon = ct.id_hoadon
                WHERE hd.id_khachhang = ?
                  AND ct.id_sach = ?
                  AND hd.trang_thai = 'completed'
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ii', $customerId, $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        return (bool)($result && $result->fetch_assoc());
    }

    /**
     * Get approved reviews for a book.
     * Returned keys match what the customer view expects.
     */
    public function getApprovedReviewsForBook($bookId) {
        $sql = "SELECT dg.so_sao, dg.noi_dung, dg.ngay_danh_gia, kh.ten_khachhang
                FROM danhgia dg
                LEFT JOIN khachhang kh ON dg.id_khachhang = kh.id_khachhang
                WHERE dg.id_sach = ? AND dg.trang_thai = 'approved'
                ORDER BY dg.ngay_danh_gia DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = [
                'ten_khach_hang' => $row['ten_khachhang'] ?? 'Khách hàng',
                'diem' => (int)($row['so_sao'] ?? 0),
                'noi_dung' => $row['noi_dung'] ?? '',
                'ngay_danh_gia' => $row['ngay_danh_gia'] ?? null
            ];
        }

        return $reviews;
    }

    /**
     * Get the current customer's review for a book (if any).
     */
    public function getCustomerReviewForBook($customerId, $bookId) {
        $sql = "SELECT id_danhgia, so_sao, noi_dung, trang_thai, ngay_danh_gia
                FROM danhgia
                WHERE id_khachhang = ? AND id_sach = ?
                ORDER BY ngay_danh_gia DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('ii', $customerId, $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;

        if (!$row) {
            return null;
        }

        return [
            'id_danhgia' => (int)$row['id_danhgia'],
            'so_sao' => (int)($row['so_sao'] ?? 0),
            'noi_dung' => $row['noi_dung'] ?? '',
            'trang_thai' => $row['trang_thai'] ?? null,
            'ngay_danh_gia' => $row['ngay_danh_gia'] ?? null
        ];
    }

    /**
     * Create a new review OR update the existing one, always auto-approved.
     *
     * Returns true on success.
     */
    public function createOrUpdateApprovedReview($customerId, $bookId, $rating, $comment) {
        // Update first (editable review behavior)
        $updateSql = "UPDATE danhgia
                      SET so_sao = ?, noi_dung = ?, trang_thai = 'approved'
                      WHERE id_sach = ? AND id_khachhang = ?";
        $updateStmt = $this->conn->prepare($updateSql);
        if ($updateStmt) {
            $updateStmt->bind_param('isii', $rating, $comment, $bookId, $customerId);
            if ($updateStmt->execute() && $this->conn->affected_rows > 0) {
                return true;
            }
        }

        // Insert if no existing row
        $insertSql = "INSERT INTO danhgia (id_sach, id_khachhang, so_sao, noi_dung, trang_thai)
                      VALUES (?, ?, ?, ?, 'approved')";
        $insertStmt = $this->conn->prepare($insertSql);
        if (!$insertStmt) {
            return false;
        }
        $insertStmt->bind_param('iiis', $bookId, $customerId, $rating, $comment);
        return $insertStmt->execute();
    }

    /**
     * Backwards-compatible method used by older controller code.
     * Accepts either Vietnamese keys or English keys.
     */
    public function addReview($data) {
        $bookId = (int)($data['id_sach'] ?? $data['id_book'] ?? 0);
        $customerId = (int)($data['id_khachhang'] ?? $data['id_customer'] ?? 0);
        $rating = (int)($data['so_sao'] ?? $data['rating'] ?? 0);
        $comment = (string)($data['noi_dung'] ?? $data['content'] ?? '');

        if ($bookId <= 0 || $customerId <= 0) {
            return false;
        }

        return $this->createOrUpdateApprovedReview($customerId, $bookId, $rating, $comment);
    }

    /**
     * Check if customer has already reviewed a book
     */
    public function hasReviewed($customerId, $bookId) {
        $sql = "SELECT COUNT(*) as count FROM danhgia WHERE id_khachhang = ? AND id_sach = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ii', $customerId, $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        return ((int)($row['count'] ?? 0) > 0);
    }
}
?>
