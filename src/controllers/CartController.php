<?php
class CartController {
    private $cart;

    public function __construct() {
        $this->cart = new Cart();
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['variation_id']) && isset($_POST['quantity'])) {
            $productId = $_POST['product_id'];
            $variationId = $_POST['variation_id'];
            $quantity = $_POST['quantity'];

            if ($this->cart->add($productId, $variationId, $quantity)) {
                $_SESSION['message'] = 'Produto adicionado ao carrinho!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Não foi possível adicionar o produto ao carrinho. Verifique o estoque.';
                $_SESSION['message_type'] = 'danger';
            }
        }
        header('Location: /cart');
        exit;
    }

    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['variation_id'])) {
            $variationId = $_POST['variation_id'];
            if ($this->cart->remove($variationId)) {
                $_SESSION['message'] = 'Item removido do carrinho.';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Item não encontrado no carrinho.';
                $_SESSION['message_type'] = 'danger';
            }
        }
        header('Location: /cart');
        exit;
    }

    public function show() {
        $cartItems = $this->cart->getItems();
        $subtotal = $this->cart->getTotal();
        $shippingCost = $this->cart->calculateShipping($subtotal);
        $total = $subtotal + $shippingCost;

        $couponCode = $_SESSION['applied_coupon_code'] ?? null;
        $couponDiscount = $_SESSION['applied_coupon_discount'] ?? 0;
        $total -= $couponDiscount; // Aplica o desconto ao total

        require_once __DIR__ . '/../views/cart.php';
    }
}