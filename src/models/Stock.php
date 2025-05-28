<?php

class Stock {
    private $conn;
    private $table_name = "stock";

    public $id;
    public $variation_id;
    public $quantity;
    public $last_updated;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (variation_id, quantity) VALUES (:variation_id, :quantity)";
        $stmt = $this->conn->prepare($query);

        $this->variation_id = htmlspecialchars(strip_tags($this->variation_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));

        $stmt->bindParam(":variation_id", $this->variation_id);
        $stmt->bindParam(":quantity", $this->quantity);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET quantity = :quantity, last_updated = CURRENT_TIMESTAMP WHERE variation_id = :variation_id";
        $stmt = $this->conn->prepare($query);

        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->variation_id = htmlspecialchars(strip_tags($this->variation_id));

        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':variation_id', $this->variation_id);

        return $stmt->execute();
    }

    public function findByVariationId($variationId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE variation_id = :variation_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':variation_id', $variationId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function decreaseStock($variationId, $quantity) {
        $query = "UPDATE " . $this->table_name . " SET quantity = quantity - :quantity WHERE variation_id = :variation_id AND quantity >= :quantity";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':variation_id', $variationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0; // Retorna true se a linha foi afetada (estoque diminuído)
    }

    public function increaseStock($variationId, $quantity) {
        $query = "UPDATE " . $this->table_name . " SET quantity = quantity + :quantity WHERE variation_id = :variation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':variation_id', $variationId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}