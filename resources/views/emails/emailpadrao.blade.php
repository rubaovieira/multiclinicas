<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificação</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .header { background-color: #007bff; color: #ffffff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 20px; text-align: center; color: #333333; }
        .code { font-size: 24px; font-weight: bold; color: #007bff; margin: 20px 0; }
        .footer { padding: 20px; font-size: 14px; color: #666666; text-align: center; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Código de Verificação</h1>
        </div>
        <div class="content">
            <p>Olá,</p>
            <p>Para confirmar sua identidade, use o código de verificação abaixo:</p>
            <div class="code">{{ $verificationCode }}</div>
            <p>Insira este código em nosso sistema para concluir o processo de verificação.</p>
            <p>Se você não solicitou este código, por favor, ignore este e-mail.</p>
        </div>
        <div class="footer">
            <p>Obrigado,<br>Clinica de exemplo</p>
        </div>
    </div>
</body>
</html>
