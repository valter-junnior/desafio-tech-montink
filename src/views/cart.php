<?php
$title = 'Seu Carrinho de Compras';
ob_start();
?>

<div class="card my-4">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">Seu Carrinho</h2>
    </div>
    <div class="card-body">
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info" role="alert">
                Seu carrinho está vazio. <a href="/products" class="alert-link">Continue comprando!</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th>Variação</th>
                            <th>SKU</th>
                            <th>Quantidade</th>
                            <th>Preço Unit.</th>
                            <th>Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['variation_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['sku']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>R$ <?php echo number_format($item['unit_price'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($item['unit_price'] * $item['quantity'], 2, ',', '.'); ?></td>
                                <td>
                                    <form action="/cart/remove" method="POST" style="display:inline;">
                                        <input type="hidden" name="variation_id" value="<?php echo htmlspecialchars($item['variation_id']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card p-3 mt-4 text-end bg-light">
                <p class="mb-1">Subtotal: <strong class="fs-5">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></strong></p>

                <form action="/checkout" method="GET" class="d-flex justify-content-end align-items-center mb-3">
                    <div class="col-auto">
                        <label for="coupon_code" class="visually-hidden">Aplicar Cupom:</label>
                        <input type="text" class="form-control form-control-sm me-2" id="coupon_code" name="coupon_code" value="<?php echo htmlspecialchars((string)($couponCode ?? '')); ?>" placeholder="Código do cupom">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-secondary btn-sm">Aplicar Cupom</button>
                    </div>
                </form>

                <?php if (isset($couponDiscount) && $couponDiscount > 0): ?>
                    <p class="mb-1">Desconto do Cupom: <strong class="text-danger">- R$ <?php echo number_format($couponDiscount, 2, ',', '.'); ?></strong></p>
                <?php endif; ?>

                <p class="mb-1">Frete: <strong class="fs-5">R$ <?php echo number_format($shippingCost, 2, ',', '.'); ?></strong></p>
                <h3 class="mt-2">Total: <strong class="text-primary fs-4">R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></h3>
            </div>

            <hr class="my-4">

            <div class="card p-4">
                <h3 class="mb-3">Finalizar Pedido</h3>
                <form action="/checkout" method="POST">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nome Completo:</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars((string)($customerData['customer_name'] ?? '')); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">E-mail:</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars((string)($customerData['customer_email'] ?? '')); ?>" required>
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="flex-grow-1 me-2">
                            <label for="customer_zipcode" class="form-label">CEP:</label>
                            <input type="text" class="form-control" id="customer_zipcode" name="customer_zipcode" value="<?php echo htmlspecialchars((string)($customerData['customer_zipcode'] ?? '')); ?>" required pattern="[0-9]{5}-?[0-9]{3}" placeholder="Ex: 00000-000">
                        </div>
                        <button type="button" id="lookup_cep_btn" class="btn btn-secondary align-self-end mb-3">Buscar CEP</button>
                    </div>
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">Endereço:</label>
                        <input type="text" class="form-control" id="customer_address" name="customer_address" value="<?php echo htmlspecialchars((string)($customerData['customer_address'] ?? '')); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_city" class="form-label">Cidade:</label>
                            <input type="text" class="form-control" id="customer_city" name="customer_city" value="<?php echo htmlspecialchars((string)($customerData['customer_city'] ?? '')); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_state" class="form-label">Estado:</label>
                            <input type="text" class="form-control" id="customer_state" name="customer_state" value="<?php echo htmlspecialchars((string)($customerData['customer_state'] ?? '')); ?>" required maxlength="2" placeholder="Ex: SP">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">Finalizar Pedido</button>
                </form>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>