<?php
$title = (isset($product['id']) ? 'Editar' : 'Cadastrar') . ' Produto';
ob_start();
?>

<div class="card my-4">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0"><?php echo $title; ?></h2>
    </div>
    <div class="card-body">
        <form action="<?php echo isset($product['id']) ? '/products/edit' : '/products'; ?>" method="POST">
            <?php if (isset($product['id'])): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id'] ?? ''); ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="name" class="form-label">Nome do Produto:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars((string)($product['name'] ?? '')); ?>" required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Preço Base:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars((string)($product['base_price'] ?? '')); ?>" required>
            </div>

            <h3 class="mt-4 mb-3">Variações e Estoque</h3>
            <div id="variations-container">
                <?php if (isset($variations) && !empty($variations)): ?>
                    <?php foreach ($variations as $key => $variation): ?>
                        <div class="p-3 mb-2 border rounded bg-light variation-item d-flex flex-wrap align-items-center">
                            <input type="hidden" name="variation_ids[]" value="<?php echo htmlspecialchars($variation['id'] ?? ''); ?>">
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="form-label d-block">Nome:</label>
                                <input type="text" class="form-control form-control-sm" name="variation_names[]" value="<?php echo htmlspecialchars((string)($variation['name'] ?? '')); ?>" required>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="form-label d-block">SKU:</label>
                                <input type="text" class="form-control form-control-sm" name="variation_skus[]" value="<?php echo htmlspecialchars((string)($variation['sku'] ?? '')); ?>" required>
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="form-label d-block">Preço Adicional:</label>
                                <input type="number" class="form-control form-control-sm" name="variation_prices[]" step="0.01" value="<?php echo htmlspecialchars((string)($variation['additional_price'] ?? '0.00')); ?>" required>
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="form-label d-block">Estoque:</label>
                                <input type="number" class="form-control form-control-sm" name="variation_stocks[]" min="0" value="<?php echo htmlspecialchars((string)($variation['stock_quantity'] ?? '0')); ?>" required>
                            </div>
                            <div class="col-md-1 d-flex justify-content-end align-items-center mt-auto">
                                <button type="button" class="btn btn-danger btn-sm remove-variation-btn">Remover</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-3 mb-2 border rounded bg-light variation-item d-flex flex-wrap align-items-center">
                        <input type="hidden" name="variation_ids[]" value="0">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="form-label d-block">Nome:</label>
                            <input type="text" class="form-control form-control-sm" name="variation_names[]" placeholder="Ex: Tamanho P, Cor Vermelha" required>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="form-label d-block">SKU:</label>
                            <input type="text" class="form-control form-control-sm" name="variation_skus[]" placeholder="Ex: PROD001-P" required>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label class="form-label d-block">Preço Adicional:</label>
                            <input type="number" class="form-control form-control-sm" name="variation_prices[]" step="0.01" value="0.00" required>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label class="form-label d-block">Estoque:</label>
                            <input type="number" class="form-control form-control-sm" name="variation_stocks[]" min="0" value="0" required>
                        </div>
                        <div class="col-md-1 d-flex justify-content-end align-items-center mt-auto">
                            <button type="button" class="btn btn-danger btn-sm remove-variation-btn">Remover</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" id="add-variation-btn" class="btn btn-secondary btn-sm mt-3 mb-4">Adicionar Variação</button>

            <button type="submit" class="btn btn-primary"><?php echo isset($product['id']) ? 'Atualizar' : 'Cadastrar'; ?> Produto</button>
        </form>
    </div>
</div>

<div class="card my-4">
    <div class="card-header bg-dark text-white">
        <h3 class="mb-0">Produtos Cadastrados</h3>
    </div>
    <div class="card-body">
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $productItem): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($productItem['name'] ?? ''); ?> <span class="badge bg-info">R$ <?php echo number_format($productItem['base_price'], 2, ',', '.'); ?></span></h5>
                                <p class="card-text"><strong>Variações:</strong></p>
                                <ul class="list-group list-group-flush">
                                    <?php if (!empty($productItem['variations'])): ?>
                                        <?php foreach ($productItem['variations'] as $variation): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <?php echo htmlspecialchars($variation['name'] ?? ''); ?> (SKU: <?php echo htmlspecialchars($variation['sku'] ?? ''); ?>)
                                                    <br> Preço Adicional: R$ <?php echo number_format($variation['additional_price'], 2, ',', '.'); ?>
                                                    <br> Estoque: <span class="badge bg-<?php echo ($variation['stock_quantity'] > 0 ? 'success' : 'danger'); ?>"><?php echo htmlspecialchars($variation['stock_quantity'] ?? ''); ?></span>
                                                </span>
                                                <form action="/cart/add" method="POST" class="d-flex align-items-center">
                                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productItem['id'] ?? ''); ?>">
                                                    <input type="hidden" name="variation_id" value="<?php echo htmlspecialchars($variation['id'] ?? ''); ?>">
                                                    <input type="number" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($variation['stock_quantity'] ?? ''); ?>" class="form-control form-control-sm me-2" style="width: 70px;">
                                                    <button type="submit" class="btn btn-success btn-sm" <?php echo ($variation['stock_quantity'] <= 0 ? 'disabled' : ''); ?>>Comprar</button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item text-muted">Nenhuma variação cadastrada.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="card-footer text-end">
                                <a href="/products/edit?id=<?php echo htmlspecialchars($productItem['id'] ?? ''); ?>" class="btn btn-info btn-sm">Editar Produto</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><p class="text-muted">Nenhum produto cadastrado ainda.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<template id="variation-template">
    <div class="p-3 mb-2 border rounded bg-light variation-item d-flex flex-wrap align-items-center">
        <input type="hidden" name="variation_ids[]" value="0">
        <div class="col-md-3 mb-2 mb-md-0">
            <label class="form-label d-block">Nome:</label>
            <input type="text" class="form-control form-control-sm" name="variation_names[]" placeholder="Ex: Tamanho P, Cor Vermelha" required>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <label class="form-label d-block">SKU:</label>
            <input type="text" class="form-control form-control-sm" name="variation_skus[]" placeholder="Ex: PROD001-P" required>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <label class="form-label d-block">Preço Adicional:</label>
            <input type="number" class="form-control form-control-sm" name="variation_prices[]" step="0.01" value="0.00" required>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <label class="form-label d-block">Estoque:</label>
            <input type="number" class="form-control form-control-sm" name="variation_stocks[]" min="0" value="0" required>
        </div>
        <div class="col-md-1 d-flex justify-content-end align-items-center mt-auto">
            <button type="button" class="btn btn-danger btn-sm remove-variation-btn">Remover</button>
        </div>
    </div>
</template>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>