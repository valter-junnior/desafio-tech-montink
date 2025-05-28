<?php

class WebhookController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    public function handle() {
        // Obter o corpo da requisição POST
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Validar os dados recebidos
        if (!isset($data['order_id']) || !isset($data['status'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
            exit;
        }

        $orderId = $data['order_id'];
        $status = $data['status'];

        if ($status === 'cancelled') {
            if ($this->orderModel->cancelOrder($orderId)) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => "Pedido #{$orderId} cancelado e estoque restaurado."]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => "Falha ao cancelar pedido #{$orderId}."]);
            }
        } else {
            if ($this->orderModel->updateStatus($orderId, $status)) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => "Status do pedido #{$orderId} atualizado para '{$status}'."]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => "Falha ao atualizar status do pedido #{$orderId}."]);
            }
        }
        exit;
    }
}