<?php

class Variation {
    private $conn;
    private $table_name = "variations";

    public $id;
    public $product_id;
    public $name;
    public $sku;
    public $additional_price;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (product_id, name, sku, additional_price) VALUES (:product_id, :name, :sku, :additional_price)";
        $stmt = $this->conn->prepare($query);

        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $this->additional_price = htmlspecialchars(strip_tags($this->additional_price));

        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":sku", $this->sku);
        $stmt->bindParam(":additional_price", $this->additional_price);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, sku = :sku, additional_price = :additional_price WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $this->additional_price = htmlspecialchars(strip_tags($this->additional_price));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':sku', $this->sku);
        $stmt->bindParam(':additional_price', $this->additional_price);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function find($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByProductId($productId) {
        $query = "SELECT v.*, s.quantity AS stock_quantity FROM " . $this->table_name . " v LEFT JOIN stock s ON v.id = s.variation_id WHERE v.product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}