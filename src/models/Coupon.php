<?php
class Coupon {
    private $conn;
    private $table_name = "coupons";

    public $id;
    public $code;
    public $discount_value;
    public $is_percentage;
    public $min_cart_value;
    public $expires_at;
    public $is_active;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function findByCode($code) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE code = :code AND is_active = TRUE AND (expires_at IS NULL OR expires_at >= NOW()) LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->discount_value = $row['discount_value'];
            $this->is_percentage = (bool)$row['is_percentage'];
            $this->min_cart_value = $row['min_cart_value'];
            $this->expires_at = $row['expires_at'];
            $this->is_active = (bool)$row['is_active'];
            return true;
        }
        return false;
    }

    public function apply($subtotal) {
        if ($this->min_cart_value > $subtotal) {
            return ['success' => false, 'message' => "Subtotal mínimo de R$ " . number_format($this->min_cart_value, 2, ',', '.') . " não atingido para este cupom."];
        }

        $discountAmount = 0;
        if ($this->is_percentage) {
            $discountAmount = $subtotal * ($this->discount_value / 100);
        } else {
            $discountAmount = $this->discount_value;
        }

        return ['success' => true, 'discount' => $discountAmount];
    }
}