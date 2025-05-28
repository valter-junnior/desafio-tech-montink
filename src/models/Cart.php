<?php

class Cart {
    const SESSION_KEY = 'cart';

    public function __construct() {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    public function add($productId, $variationId, $quantity) {
        $productId = (int)$productId;
        $variationId = (int)$variationId;
        $quantity = (int)$quantity;

        if ($quantity <= 0) {
            return false;
        }

        $productModel = new Product();
        $variationModel = new Variation();
        $stockModel = new Stock();

        if (!$productModel->find($productId)) {
            return false; // Produto não encontrado
        }
        $variation = $variationModel->find($variationId);
        if (!$variation || $variation['product_id'] != $productId) {
            return false; // Variação inválida para este produto
        }
        $stock = $stockModel->findByVariationId($variationId);
        if (!$stock || $stock['quantity'] < $quantity) {
            return false; // Estoque insuficiente
        }

        // Verifica se o item já está no carrinho
        $found = false;
        foreach ($_SESSION[self::SESSION_KEY] as &$item) {
            if ($item['variation_id'] == $variationId) {
                // Se adicionar a um item existente, verificar estoque para a nova quantidade total
                if ($stock['quantity'] < ($item['quantity'] + $quantity)) {
                    return false; // Estoque insuficiente para adicionar mais
                }
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        unset($item); // Quebra a referência

        if (!$found) {
            $_SESSION[self::SESSION_KEY][] = [
                'product_id' => $productId,
                'variation_id' => $variationId,
                'product_name' => $productModel->name,
                'variation_name' => $variation['name'],
                'unit_price' => $productModel->base_price + $variation['additional_price'],
                'quantity' => $quantity,
                'sku' => $variation['sku']
            ];
        }
        return true;
    }

    public function remove($variationId) {
        foreach ($_SESSION[self::SESSION_KEY] as $key => $item) {
            if ($item['variation_id'] == $variationId) {
                unset($_SESSION[self::SESSION_KEY][$key]);
                $_SESSION[self::SESSION_KEY] = array_values($_SESSION[self::SESSION_KEY]); // Reindex array
                return true;
            }
        }
        return false;
    }

    public function getItems() {
        return $_SESSION[self::SESSION_KEY];
    }

    public function getTotal() {
        $subtotal = 0;
        foreach ($this->getItems() as $item) {
            $subtotal += $item['unit_price'] * $item['quantity'];
        }
        return (float) $subtotal;
    }

    public function calculateShipping($subtotal) {
        if ($subtotal > 200.00) {
            return 0.00; // Frete grátis
        } elseif ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }

    public function clear() {
        $_SESSION[self::SESSION_KEY] = [];
    }
}