<?php

class OrderController {
    private $cart;
    private $orderModel;
    private $couponModel;
    private $emailService;

    public function __construct() {
        $this->cart = new Cart();
        $this->orderModel = new Order();
        $this->couponModel = new Coupon();
        $this->emailService = new EmailService(); // Inicia o serviço de e-mail
    }
    public function index() {
        $orders = $this->orderModel->getAllOrders(); // Novo método no modelo
        require_once __DIR__ . '/../views/orders_list.php'; // Nova view
    }

    public function details($orderId) {
        $order = $this->orderModel->getOrderDetails($orderId);
        if (!$order) {
            $_SESSION['message'] = 'Pedido não encontrado.';
            $_SESSION['message_type'] = 'danger';
            header('Location: /orders');
            exit;
        }
        require_once __DIR__ . '/../views/order_details.php'; // Nova view
    }

    public function checkout() {
        $cartItems = $this->cart->getItems();
        if (empty($cartItems)) {
            $_SESSION['message'] = 'Seu carrinho está vazio. Adicione produtos para prosseguir.';
            $_SESSION['message_type'] = 'info';
            header('Location: /products');
            exit;
        }

        $subtotal = $this->cart->getTotal();
        $shippingCost = $this->cart->calculateShipping($subtotal);
        $discount = 0;
        $appliedCouponId = null;

        // Processar cupom, se houver
        if (isset($_GET['coupon_code']) && !empty($_GET['coupon_code'])) {
            $couponCode = trim($_GET['coupon_code']);
            if ($this->couponModel->findByCode($couponCode)) {
                $couponResult = $this->couponModel->apply($subtotal);
                if ($couponResult['success']) {
                    $discount = $couponResult['discount'];
                    $appliedCouponId = $this->couponModel->id;
                    $_SESSION['applied_coupon_code'] = $couponCode;
                    $_SESSION['applied_coupon_discount'] = $discount;
                    $_SESSION['message'] = 'Cupom aplicado com sucesso!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = $couponResult['message'];
                    $_SESSION['message_type'] = 'danger';
                    unset($_SESSION['applied_coupon_code']);
                    unset($_SESSION['applied_coupon_discount']);
                }
            } else {
                $_SESSION['message'] = 'Cupom inválido ou expirado.';
                $_SESSION['message_type'] = 'danger';
                unset($_SESSION['applied_coupon_code']);
                unset($_SESSION['applied_coupon_discount']);
            }
        } elseif (isset($_SESSION['applied_coupon_code'])) { // Manter cupom aplicado se já estiver na sessão
             $couponCode = $_SESSION['applied_coupon_code'];
             if ($this->couponModel->findByCode($couponCode)) {
                 $couponResult = $this->couponModel->apply($subtotal);
                 if ($couponResult['success']) {
                     $discount = $couponResult['discount'];
                     $appliedCouponId = $this->couponModel->id;
                 } else {
                    unset($_SESSION['applied_coupon_code']);
                    unset($_SESSION['applied_coupon_discount']);
                 }
             } else {
                unset($_SESSION['applied_coupon_code']);
                unset($_SESSION['applied_coupon_discount']);
             }
        }

        $total = $subtotal + $shippingCost - $discount;
        $total = max(0, $total); // Garante que o total não seja negativo
        
        // Dados para o formulário de checkout
        $customerData = $_SESSION['checkout_data'] ?? []; // Preencher dados do form se já submetidos

        require_once __DIR__ . '/../views/cart.php'; // Usa a mesma view do carrinho para checkout, mas com form
    }

    public function placeOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartItems = $this->cart->getItems();
            if (empty($cartItems)) {
                $_SESSION['message'] = 'Seu carrinho está vazio. Adicione produtos para prosseguir.';
                $_SESSION['message_type'] = 'info';
                header('Location: /products');
                exit;
            }

            // Validar dados do formulário
            $name = trim($_POST['customer_name'] ?? '');
            $email = filter_var($_POST['customer_email'] ?? '', FILTER_VALIDATE_EMAIL);
            $zipcode = trim($_POST['customer_zipcode'] ?? '');
            $address = trim($_POST['customer_address'] ?? '');
            $city = trim($_POST['customer_city'] ?? '');
            $state = trim($_POST['customer_state'] ?? '');

            if (empty($name) || !$email || empty($zipcode) || empty($address) || empty($city) || empty($state)) {
                $_SESSION['message'] = 'Por favor, preencha todos os campos obrigatórios e um e-mail válido.';
                $_SESSION['message_type'] = 'danger';
                $_SESSION['checkout_data'] = $_POST; // Salva dados para preencher o form novamente
                header('Location: /checkout');
                exit;
            }

            $subtotal = $this->cart->getTotal();
            $shippingCost = $this->cart->calculateShipping($subtotal);
            $discount = $_SESSION['applied_coupon_discount'] ?? 0;
            $appliedCouponId = null;
            if (isset($_SESSION['applied_coupon_code'])) {
                if ($this->couponModel->findByCode($_SESSION['applied_coupon_code'])) {
                    $appliedCouponId = $this->couponModel->id;
                }
            }
            $total = $subtotal + $shippingCost - $discount;
            $total = max(0, $total);

            $orderData = [
                'name' => $name,
                'email' => $email,
                'zipcode' => $zipcode,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'subtotal' => $subtotal,
                'shipping' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending' // Status inicial
            ];

            $orderId = $this->orderModel->create($orderData, $cartItems, $appliedCouponId);
            if ($orderId) {
                // Limpa o carrinho e as informações do cupom na sessão
                $this->cart->clear();
                unset($_SESSION['applied_coupon_code']);
                unset($_SESSION['applied_coupon_discount']);
                unset($_SESSION['checkout_data']);

                // Enviar e-mail de confirmação
                $orderData['id'] = $orderId; // Adiciona o ID do pedido para o e-mail
                $this->emailService->sendOrderConfirmation($email, $name, $orderData, $cartItems);

                $_SESSION['message'] = 'Pedido #' . $orderId . ' realizado com sucesso!';
                $_SESSION['message_type'] = 'success';
               //  header('Location: /order_confirmation?order_id=' . $orderId); // Redireciona para confirmação
                header('Location: /orders'); 
                exit;
            } else {
                $_SESSION['message'] = 'Erro ao finalizar pedido. Por favor, tente novamente.';
                $_SESSION['message_type'] = 'danger';
                header('Location: /checkout');
                exit;
            }
        } else {
            header('Location: /checkout');
            exit;
        }
    }
}