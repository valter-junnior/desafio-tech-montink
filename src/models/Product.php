<?php

class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $base_price;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (name, base_price) VALUES (:name, :base_price)";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->base_price = htmlspecialchars(strip_tags($this->base_price));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":base_price", $this->base_price);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, base_price = :base_price, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->base_price = htmlspecialchars(strip_tags($this->base_price));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':base_price', $this->base_price);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function find($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->base_price = $row['base_price'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function getAllWithStockAndVariations() {
        $query = "
            SELECT
                p.id AS product_id, p.name AS product_name, p.base_price,
                v.id AS variation_id, v.name AS variation_name, v.sku, v.additional_price,
                s.quantity AS stock_quantity
            FROM
                products p
            LEFT JOIN
                variations v ON p.id = v.product_id
            LEFT JOIN
                stock s ON v.id = s.variation_id
            ORDER BY
                p.name, v.name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productId = $row['product_id'];
            if (!isset($products[$productId])) {
                $products[$productId] = [
                    'id' => $row['product_id'],
                    'name' => $row['product_name'],
                    'base_price' => $row['base_price'],
                    'variations' => []
                ];
            }
            if ($row['variation_id']) {
                $products[$productId]['variations'][] = [
                    'id' => $row['variation_id'],
                    'name' => $row['variation_name'],
                    'sku' => $row['sku'],
                    'additional_price' => $row['additional_price'],
                    'stock_quantity' => $row['stock_quantity']
                ];
            }
        }
        return array_values($products);
    }
}