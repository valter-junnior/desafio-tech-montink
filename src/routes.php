<?php
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestUri) {
    case '/':
    case '/products':
        if ($requestMethod == 'GET') {
            $controller = new ProductController();
            $controller->index();
        } elseif ($requestMethod == 'POST') {
            $controller = new ProductController();
            $controller->store();
        }
        break;
    case '/products/edit':
        if ($requestMethod == 'GET' && isset($_GET['id'])) {
            $controller = new ProductController();
            $controller->edit($_GET['id']);
        } elseif ($requestMethod == 'POST' && isset($_POST['id'])) {
            $controller = new ProductController();
            $controller->update($_POST['id']);
        }
        break;
    case '/cart/add':
        if ($requestMethod == 'POST') {
            $controller = new CartController();
            $controller->add();
        }
        break;
    case '/cart':
        if ($requestMethod == 'GET') {
            $controller = new CartController();
            $controller->show();
        }
        break;
    case '/cart/remove':
        if ($requestMethod == 'POST') {
            $controller = new CartController();
            $controller->remove();
        }
        break;
    case '/checkout':
        if ($requestMethod == 'GET') {
            $controller = new OrderController();
            $controller->checkout();
        } elseif ($requestMethod == 'POST') {
            $controller = new OrderController();
            $controller->placeOrder();
        }
        break;
    case '/api/cep':
        if ($requestMethod == 'GET' && isset($_GET['cep'])) {
            $service = new ViaCEPService();
            echo json_encode($service->getAddress($_GET['cep']));
            exit;
        }
        break;
    case '/webhook':
        if ($requestMethod == 'POST') {
            $controller = new WebhookController();
            $controller->handle();
        }
        break;
    case '/orders':
        if ($requestMethod == 'GET') {
            $controller = new OrderController();
            $controller->index(); 
        }
        break;
    case '/orders/details':
        if ($requestMethod == 'GET' && isset($_GET['id'])) {
            $controller = new OrderController();
            $controller->details($_GET['id']);
        }
        break;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
