<?php
$title = 'Detalhes do Pedido #' . ($order['id'] ?? 'N/A');
ob_start();
?>

<div class="card my-4">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">Detalhes do Pedido #<?php echo htmlspecialchars($order['id'] ?? 'N/A'); ?></h2>
    </div>
    <div class="card-body">
        <?php if (!isset($order) || empty($order)): ?>
            <div class="alert alert-danger" role="alert">
                Pedido não encontrado.
            </div>
            <a href="/orders" class="btn btn-secondary">Voltar para a Lista de Pedidos</a>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <h4>Informações do Pedido</h4>
                    <p><strong>Status:</strong> <span class="badge bg-<?php
                        switch ($order['status']) {
                            case 'pending': echo 'warning'; break;
                            case 'processing': echo 'info'; break;
                            case 'completed': echo 'success'; break;
                            case 'cancelled': echo 'danger'; break;
                            default: echo 'secondary'; break;
                        }
                    ?>"><?php echo htmlspecialchars(ucfirst($order['status'])); ?></span></p>
                    <p><strong>Data do Pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    <p><strong>Subtotal:</strong> R$ <?php echo number_format($order['subtotal'], 2, ',', '.'); ?></p>
                    <p><strong>Desconto:</strong> R$ <?php echo number_format($order['discount'], 2, ',', '.'); ?></p>
                    <p><strong>Frete:</strong> R$ <?php echo number_format($order['shipping_cost'], 2, ',', '.'); ?></p>
                    <p><strong>Total:</strong> R$ <?php echo number_format($order['total'], 2, ',', '.'); ?></p>
                </div>
                <div class="col-md-6">
                    <h4>Informações do Cliente</h4>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? ""); ?></p>
                    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($order['customer_email'] ?? ""); ?></p>
                    <p><strong>Endereço:</strong> <?php echo htmlspecialchars($order['customer_address'] ?? ""); ?></p>
                    <p><strong>CEP:</strong> <?php echo htmlspecialchars($order['customer_zipcode'] ?? ""); ?></p>
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($order['customer_city'] ?? ""); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($order['customer_state'] ?? ""); ?></p>
                </div>
            </div>

            <h4 class="mt-4">Itens do Pedido</h4>
            <?php if (!empty($order['items'])): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Variação</th>
                                <th>SKU</th>
                                <th>Quantidade</th>
                                <th>Preço Unit.</th>
                                <th>Total do Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name'] ?? ""); ?></td>
                                    <td><?php echo htmlspecialchars($item['variation_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['sku'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity'] ?? ""); ?></td>
                                    <td>R$ <?php echo number_format($item['unit_price'], 2, ',', '.'); ?></td>
                                    <td>R$ <?php echo number_format($item['item_total'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Nenhum item encontrado para este pedido.</p>
            <?php endif; ?>

            <a href="/orders" class="btn btn-secondary mt-3">Voltar para a Lista de Pedidos</a>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>