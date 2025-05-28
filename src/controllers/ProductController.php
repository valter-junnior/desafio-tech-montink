<?php

class ProductController {
    private $productModel;
    private $variationModel;
    private $stockModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->variationModel = new Variation();
        $this->stockModel = new Stock();
    }

    public function index() {
        $products = $this->productModel->getAllWithStockAndVariations();
        require_once __DIR__ . '/../views/product_form.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->name = $_POST['name'];
            $this->productModel->base_price = $_POST['price'];

            try {
                if ($this->productModel->create()) {
                    $productId = $this->productModel->id;

                    // Processar variações e estoque
                    if (isset($_POST['variation_names']) && is_array($_POST['variation_names'])) {
                        foreach ($_POST['variation_names'] as $key => $varName) {
                            $varName = trim($varName);
                            $varSku = trim($_POST['variation_skus'][$key]);
                            $varPrice = floatval($_POST['variation_prices'][$key]);
                            $varStock = intval($_POST['variation_stocks'][$key]);

                            if (!empty($varName) && !empty($varSku)) {
                                $this->variationModel->product_id = $productId;
                                $this->variationModel->name = $varName;
                                $this->variationModel->sku = $varSku;
                                $this->variationModel->additional_price = $varPrice;

                                if ($this->variationModel->create()) {
                                    $variationId = $this->variationModel->id;
                                    $this->stockModel->variation_id = $variationId;
                                    $this->stockModel->quantity = $varStock;
                                    $this->stockModel->create();
                                }
                            }
                        }
                    }
                    $_SESSION['message'] = 'Produto e estoque criados com sucesso!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Erro ao criar produto.';
                    $_SESSION['message_type'] = 'danger';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == '23000') {
                    $_SESSION['message'] = 'Erro: SKU duplicado. Por favor, use SKUs únicos para cada variação.';
                    $_SESSION['message_type'] = 'danger';
                } else {
                    $_SESSION['message'] = 'Erro no banco de dados: ' . $e->getMessage();
                    $_SESSION['message_type'] = 'danger';
                }
            }
        }
        header('Location: /products');
        exit;
    }

    public function edit($id) {
        if ($this->productModel->find($id)) {
            $product = [
                'id' => $this->productModel->id,
                'name' => $this->productModel->name,
                'base_price' => $this->productModel->base_price
            ];
            $variations = $this->variationModel->getByProductId($id);
            require_once __DIR__ . '/../views/product_form.php';
        } else {
            $_SESSION['message'] = 'Produto não encontrado.';
            $_SESSION['message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->id = $id;
            $this->productModel->name = $_POST['name'];
            $this->productModel->base_price = $_POST['price'];

            try {
                if ($this->productModel->update()) {
                    if (isset($_POST['variation_ids']) && is_array($_POST['variation_ids'])) {
                        foreach ($_POST['variation_ids'] as $key => $varId) {
                            $varName = trim($_POST['variation_names'][$key]);
                            $varSku = trim($_POST['variation_skus'][$key]);
                            $varPrice = floatval($_POST['variation_prices'][$key]);
                            $varStock = intval($_POST['variation_stocks'][$key]);

                            if (empty($varName) || empty($varSku)) {
                                continue; 
                            }

                            $this->variationModel->id = $varId;
                            $this->variationModel->name = $varName;
                            $this->variationModel->sku = $varSku;
                            $this->variationModel->additional_price = $varPrice;

                            $this->stockModel->variation_id = $varId;
                            $this->stockModel->quantity = $varStock;

                            if ($varId == 0) { 
                                $this->variationModel->product_id = $id;
                                if ($this->variationModel->create()) {
                                    $newVariationId = $this->variationModel->id;
                                    $this->stockModel->variation_id = $newVariationId;
                                    $this->stockModel->create();
                                }
                            } else { 
                                $this->variationModel->update();
                                $this->stockModel->update(); 
                            }
                        }
                    }
                    $_SESSION['message'] = 'Produto e estoque atualizados com sucesso!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Erro ao atualizar produto.';
                    $_SESSION['message_type'] = 'danger';
                }
            } catch (PDOException $e) {
                // Prever SKUs duplicados
                if ($e->getCode() == '23000') {
                    $_SESSION['message'] = 'Erro: SKU duplicado. Por favor, use SKUs únicos para cada variação.';
                    $_SESSION['message_type'] = 'danger';
                } else {
                    $_SESSION['message'] = 'Erro no banco de dados: ' . $e->getMessage();
                    $_SESSION['message_type'] = 'danger';
                }
            }
        }
        header('Location: /products');
        exit;
    }
}