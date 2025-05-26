<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obter dados do formulário
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

// Validar dados obrigatórios
$required_fields = ['nome', 'email', 'telefone', 'servico'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Campo obrigatório: $field"]);
        exit;
    }
}

// Configurações SMTP
$smtp_host = 'smtp.hostinger.com';
$smtp_port = 465;
$smtp_username = 'noreply@zapseller.tech';
$smtp_password = 'SenhaUserNoReply123@@@';
$smtp_secure = 'ssl';

// Destinatário
$to_email = '12213451carlos@gmail.com';
$to_name = 'Studio Clipagem';

// Dados do formulário
$nome = htmlspecialchars($input['nome']);
$email = htmlspecialchars($input['email']);
$telefone = htmlspecialchars($input['telefone']);
$empresa = htmlspecialchars($input['empresa'] ?? 'Não informado');
$servico = htmlspecialchars($input['servico']);
$midias_sociais = htmlspecialchars($input['midias-sociais'] ?? 'Não informado');
$mensagem = htmlspecialchars($input['mensagem'] ?? 'Não informado');

// Mapear serviços
$servicos_map = [
    'tv' => 'Clipagem de TV (+200 emissoras)',
    'radio' => 'Clipagem de Rádio (+750 emissoras)',
    'jornal' => 'Clipagem de Jornal (+4.000 impressos)',
    'web' => 'Clipagem de Web (+1M sites/blogs)',
    'social' => 'Monitoramento de Redes Sociais',
    'analise' => 'Análise de Mídia',
    'completo' => 'Pacote Completo'
];

$servico_nome = $servicos_map[$servico] ?? $servico;

// Assunto do email
$subject = 'AGENDAMENTO DEMONSTRAÇÃO - ' . $nome;

// Corpo do email em HTML
$html_body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f1926e 0%, #e8744a 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; }
        .value { margin-left: 10px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🎯 Nova Solicitação de Demonstração</h1>
            <p>Studio Clipagem - Monitoramento de Mídia</p>
        </div>
        <div class='content'>
            <h2>Dados do Cliente:</h2>
            
            <div class='field'>
                <span class='label'>👤 Nome:</span>
                <span class='value'>$nome</span>
            </div>
            
            <div class='field'>
                <span class='label'>📧 E-mail:</span>
                <span class='value'>$email</span>
            </div>
            
            <div class='field'>
                <span class='label'>📱 Telefone:</span>
                <span class='value'>$telefone</span>
            </div>
            
            <div class='field'>
                <span class='label'>🏢 Empresa:</span>
                <span class='value'>$empresa</span>
            </div>
            
            <div class='field'>
                <span class='label'>🎯 Serviço de Interesse:</span>
                <span class='value'>$servico_nome</span>
            </div>
            
            <div class='field'>
                <span class='label'>📱 Mídias Sociais:</span>
                <span class='value'>$midias_sociais</span>
            </div>
            
            <div class='field'>
                <span class='label'>💬 Observações:</span>
                <span class='value'>$mensagem</span>
            </div>
            
            <hr style='margin: 20px 0; border: 1px solid #ddd;'>
            
            <p><strong>📅 Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>
            <p><strong>🌐 IP:</strong> " . $_SERVER['REMOTE_ADDR'] . "</p>
        </div>
        <div class='footer'>
            <p>Este e-mail foi enviado automaticamente pelo formulário do site Studio Clipagem</p>
        </div>
    </div>
</body>
</html>
";

// Versão texto simples
$text_body = "
NOVA SOLICITAÇÃO DE DEMONSTRAÇÃO - STUDIO CLIPAGEM

Dados do Cliente:
- Nome: $nome
- E-mail: $email
- Telefone: $telefone
- Empresa: $empresa
- Serviço de Interesse: $servico_nome
- Mídias Sociais: $midias_sociais
- Observações: $mensagem

Data/Hora: " . date('d/m/Y H:i:s') . "
IP: " . $_SERVER['REMOTE_ADDR'] . "
";

// Usar PHPMailer se disponível, senão usar mail() nativo
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    // Usar PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    $mail = new PHPMailer(true);
    
    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;
        $mail->CharSet = 'UTF-8';
        
        // Remetente
        $mail->setFrom($smtp_username, 'Studio Clipagem - Site');
        $mail->addReplyTo($email, $nome);
        
        // Destinatário
        $mail->addAddress($to_email, $to_name);
        
        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html_body;
        $mail->AltBody = $text_body;
        
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'E-mail enviado com sucesso!']);
        
    } catch (Exception $e) {
        error_log("Erro PHPMailer: " . $mail->ErrorInfo);
        echo json_encode(['success' => false, 'message' => 'Erro ao enviar e-mail: ' . $mail->ErrorInfo]);
    }
    
} else {
    // Usar mail() nativo com headers SMTP
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Studio Clipagem <$smtp_username>\r\n";
    $headers .= "Reply-To: $nome <$email>\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    if (mail($to_email, $subject, $html_body, $headers)) {
        echo json_encode(['success' => true, 'message' => 'E-mail enviado com sucesso!']);
    } else {
        error_log("Erro ao enviar e-mail com mail() nativo");
        echo json_encode(['success' => false, 'message' => 'Erro ao enviar e-mail. Tente novamente.']);
    }
}
?> 