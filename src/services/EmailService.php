<?php
// Requer instalação do PHPMailer via Composer: composer require phpmailer/phpmailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mail;

    public function __construct() {
        require_once __DIR__ . '/../vendor/autoload.php'; 

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = getenv('SMTP_HOST') ?: 'localhost';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = getenv('SMTP_USERNAME') ?: '';
        $this->mail->Password = getenv('SMTP_PASSWORD') ?: '';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = getenv('SMTP_PORT') ?: 587;

        $this->mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'no-reply@example.com', getenv('SMTP_FROM_NAME') ?: 'Seu E-commerce');
        $this->mail->isHTML(true);
    }

    public function sendOrderConfirmation($toEmail, $toName, $orderData, $cartItems) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = 'Confirmação do seu pedido #' . $orderData['id'];

            // Construir o corpo do e-mail
            $body = "<h2>Confirmação do Pedido #" . $orderData['id'] . "</h2>";
            $body .= "<p>Obrigado por sua compra, <strong>" . htmlspecialchars($toName) . "</strong>!</p>";
            $body .= "<p>Detalhes do Pedido:</p>";
            $body .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
            $body .= "<thead><tr><th>Produto</th><th>Variação</th><th>SKU</th><th>Qtd</th><th>Preço Unit.</th><th>Total</th></tr></thead><tbody>";
            foreach ($cartItems as $item) {
                $body .= "<tr>";
                $body .= "<td>" . htmlspecialchars($item['product_name']) . "</td>";
                $body .= "<td>" . htmlspecialchars($item['variation_name'] ?: 'N/A') . "</td>";
                $body .= "<td>" . htmlspecialchars($item['sku'] ?: 'N/A') . "</td>";
                $body .= "<td>" . htmlspecialchars($item['quantity']) . "</td>";
                $body .= "<td>R$ " . number_format($item['unit_price'], 2, ',', '.') . "</td>";
                $body .= "<td>R$ " . number_format($item['unit_price'] * $item['quantity'], 2, ',', '.') . "</td>";
                $body .= "</tr>";
            }
            $body .= "</tbody></table>";

            $body .= "<p>Subtotal: R$ " . number_format($orderData['subtotal'], 2, ',', '.') . "</p>";
            $body .= "<p>Desconto: R$ " . number_format($orderData['discount'], 2, ',', '.') . "</p>";
            $body .= "<p>Frete: R$ " . number_format($orderData['shipping'], 2, ',', '.') . "</p>";
            $body .= "<h3>Total do Pedido: R$ " . number_format($orderData['total'], 2, ',', '.') . "</h3>";
            $body .= "<p>Endereço de Entrega:</p>";
            $body .= "<p>" . htmlspecialchars($orderData['address']) . "<br>";
            $body .= htmlspecialchars($orderData['zipcode']) . ", " . htmlspecialchars($orderData['city']) . " - " . htmlspecialchars($orderData['state']) . "</p>";
            $body .= "<p>Status do Pedido: <strong>" . htmlspecialchars($orderData['status']) . "</strong></p>";
            $body .= "<p>Qualquer dúvida, entre em contato conosco.</p>";

            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}