<?php
$title = 'Meus Pedidos';
ob_start();
?>

<div class="card my-4">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">Lista de Pedidos</h2>
    </div>
    <div class="card-body">
        <?php if (empty($orders)): ?>
            <div class="alert alert-info" role="alert">
                Nenhum pedido foi realizado ainda.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID do Pedido</th>
                            <th>Cliente</th>
                            <th>E-mail</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data do Pedido</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($order['id'] ?? ""); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name' ?? ""]); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_email' ?? ""]); ?></td>
                                <td>R$ <?php echo number_format($order['total'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="badge bg-<?php
                                        switch ($order['status']) {
                                            case 'pending': echo 'warning'; break;
                                            case 'processing': echo 'info'; break;
                                            case 'completed': echo 'success'; break;
                                            case 'cancelled': echo 'danger'; break;
                                            default: echo 'secondary'; break;
                                        }
                                    ?>">
                                        <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="/orders/details?id=<?php echo htmlspecialchars($order['id'] ?? ""); ?>" class="btn btn-sm btn-info">Ver Detalhes</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>