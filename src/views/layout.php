<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Loja Online - <?php echo $title ?? 'Página Inicial'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/custom.css">
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container text-center">
            <h1>Minha Loja Online</h1>
            <nav class="mt-2">
                <ul class="nav justify-content-center">
                    <li class="nav-item"><a class="nav-link text-white" href="/products">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/cart">Carrinho (<span class="badge bg-secondary"><?php echo count($_SESSION['cart'] ?? []) ?></span>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php echo $content; // Conteúdo dinâmico da página ?>
    </main>

    <footer class="bg-dark text-white py-3 text-center">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Minha Loja Online. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVpdpz5b+15oF86VjofXN/NozM/yqP8iYVjR/gM8w2F" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlco9tFneI+K6RwWfge8fRA2x6AwaM+b3trx7gH1/E5P5F" crossorigin="anonymous"></script>
    <script src="/js/script.js"></script>
</body>
</html>