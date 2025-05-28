<?php
$title = 'Confirmação do Pedido';
ob_start();
?>

<div class="card my-4">
    <div class="card-header bg-success text-white">
        <h2 class="mb-0">Pedido Confirmado!</h2>
    </div>
    <div class="card-body">
        <p class="lead">Seu pedido foi realizado com sucesso!</p>
        <?php if (isset($_GET['order_id'])): ?>
            <p class="fs-4">Número do Pedido: <strong class="text-success">#<?php echo htmlspecialchars($_GET['order_id'] ?? ''); ?></strong></p>
        <?php endif; ?>
        <p>Você receberá um e-mail de confirmação com os detalhes do seu pedido em breve.</p>
        <p>Obrigado por sua compra!</p>
        <a href="/products" class="btn btn-primary mt-3">Voltar para Produtos</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>