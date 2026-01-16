<?php

/**
 * Coupon Model Class
 * 
 * Handles all database operations related to coupons (magiamgia)
 */

class Coupon
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    /**
     * Get coupon by code
     * 
     * @param string $code
     * @return array|null
     */
    public function getCouponByCode($code)
    {
        $sql = "SELECT * FROM magiamgia WHERE ma_code = ? AND trang_thai = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get all coupons (for Admin)
     */
    public function getAllCoupons()
    {
        $sql = "SELECT * FROM magiamgia ORDER BY ngay_tao DESC";
        $result = $this->conn->query($sql);
        $coupons = [];
        while ($row = $result->fetch_assoc()) {
            $coupons[] = $row;
        }
        return $coupons;
    }

    /**
     * Create new coupon
     */
    public function createCoupon($data)
    {
        $sql = "INSERT INTO magiamgia (
                    ma_code, ten_chuongtrinh, loai_giam, gia_tri_giam, 
                    gia_tri_toi_thieu, giam_toi_da, so_luong, 
                    ngay_bat_dau, ngay_ket_thuc, trang_thai
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssdddisss",
            $data['ma_code'],
            $data['ten_chuongtrinh'],
            $data['loai_giam'],
            $data['gia_tri_giam'],
            $data['gia_tri_toi_thieu'],
            $data['giam_toi_da'],
            $data['so_luong'],
            $data['ngay_bat_dau'],
            $data['ngay_ket_thuc'],
            $data['trang_thai']
        );

        return $stmt->execute();
    }

    /**
     * Update coupon
     */
    public function updateCoupon($id, $data)
    {
        $sql = "UPDATE magiamgia SET 
                    ma_code = ?, 
                    ten_chuongtrinh = ?, 
                    loai_giam = ?, 
                    gia_tri_giam = ?, 
                    gia_tri_toi_thieu = ?, 
                    giam_toi_da = ?, 
                    so_luong = ?, 
                    ngay_bat_dau = ?, 
                    ngay_ket_thuc = ?, 
                    trang_thai = ?
                WHERE id_magiamgia = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssdddisssi",
            $data['ma_code'],
            $data['ten_chuongtrinh'],
            $data['loai_giam'],
            $data['gia_tri_giam'],
            $data['gia_tri_toi_thieu'],
            $data['giam_toi_da'],
            $data['so_luong'],
            $data['ngay_bat_dau'],
            $data['ngay_ket_thuc'],
            $data['trang_thai'],
            $id
        );

        return $stmt->execute();
    }

    /**
     * Delete coupon
     */
    public function deleteCoupon($id)
    {
        $sql = "DELETE FROM magiamgia WHERE id_magiamgia = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get coupon by ID
     */
    public function getCouponById($id)
    {
        $sql = "SELECT * FROM magiamgia WHERE id_magiamgia = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
