<?php

class Order {
    private $conn;
    private $table_name = "orders";
    private $items_table_name = "order_items";

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($data, $cartItems, $appliedCouponId = null) {
        $this->conn->beginTransaction();
        try {
            // 1. Inserir o pedido principal
            $query = "INSERT INTO " . $this->table_name . " (customer_name, customer_email, customer_address, customer_city, customer_state, customer_zipcode, subtotal, shipping_cost, discount, total, status, coupon_id)
                      VALUES (:customer_name, :customer_email, :customer_address, :customer_city, :customer_state, :customer_zipcode, :subtotal, :shipping_cost, :discount, :total, :status, :coupon_id)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':customer_name', $data['name']);
            $stmt->bindParam(':customer_email', $data['email']);
            $stmt->bindParam(':customer_address', $data['address']);
            $stmt->bindParam(':customer_city', $data['city']);
            $stmt->bindParam(':customer_state', $data['state']);
            $stmt->bindParam(':customer_zipcode', $data['zipcode']);
            $stmt->bindParam(':subtotal', $data['subtotal']);
            $stmt->bindParam(':shipping_cost', $data['shipping']);
            $stmt->bindParam(':discount', $data['discount']);
            $stmt->bindParam(':total', $data['total']);
            $status = 'pending';
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':coupon_id', $appliedCouponId, PDO::PARAM_INT);

            $stmt->execute();
            $orderId = $this->conn->lastInsertId();

            // 2. Inserir os itens do pedido e decrementar estoque
            $stockModel = new Stock();
            foreach ($cartItems as $item) {
                if (!$stockModel->decreaseStock($item['variation_id'], $item['quantity'])) {
                    throw new Exception("Estoque insuficiente para " . $item['product_name'] . " - " . $item['variation_name']);
                }

                $itemQuery = "INSERT INTO " . $this->items_table_name . " (order_id, product_id, variation_id, product_name, variation_name, sku, quantity, unit_price, item_total)
                              VALUES (:order_id, :product_id, :variation_id, :product_name, :variation_name, :sku, :quantity, :unit_price, :item_total)";
                $itemStmt = $this->conn->prepare($itemQuery);

                $itemTotal = $item['unit_price'] * $item['quantity'];

                $itemStmt->bindParam(':order_id', $orderId);
                $itemStmt->bindParam(':product_id', $item['product_id']);
                $itemStmt->bindParam(':variation_id', $item['variation_id']);
                $itemStmt->bindParam(':product_name', $item['product_name']);
                $itemStmt->bindParam(':variation_name', $item['variation_name']);
                $itemStmt->bindParam(':sku', $item['sku']);
                $itemStmt->bindParam(':quantity', $item['quantity']);
                $itemStmt->bindParam(':unit_price', $item['unit_price']);
                $itemStmt->bindParam(':item_total', $itemTotal);

                $itemStmt->execute();
            }

            $this->conn->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Order creation failed: " . $e->getMessage()); // Log do erro
            return false;
        }
    }

    public function updateStatus($orderId, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $orderId);
        return $stmt->execute();
    }

    public function cancelOrder($orderId) {
        $this->conn->beginTransaction();
        try {
            // 1. Obter itens do pedido para retornar ao estoque
            $itemsQuery = "SELECT variation_id, quantity FROM " . $this->items_table_name . " WHERE order_id = :order_id";
            $itemsStmt = $this->conn->prepare($itemsQuery);
            $itemsStmt->bindParam(':order_id', $orderId);
            $itemsStmt->execute();
            $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            $stockModel = new Stock();
            foreach ($orderItems as $item) {
                $stockModel->increaseStock($item['variation_id'], $item['quantity']);
            }

            // 2. Remover o pedido
            $deleteQuery = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':id', $orderId);
            $deleteStmt->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Order cancellation failed: " . $e->getMessage());
            return false;
        }
    }

    public function getOrderDetails($orderId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $itemsQuery = "SELECT * FROM " . $this->items_table_name . " WHERE order_id = :order_id";
            $itemsStmt = $this->conn->prepare($itemsQuery);
            $itemsStmt->bindParam(':order_id', $orderId);
            $itemsStmt->execute();
            $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $order;
    }
}