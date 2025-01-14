<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Security-Policy" content="script-src 'none'; connect-src 'none'; object-src 'none'; form-action 'none';"> 
    <meta charset="UTF-8"> 
    <meta content="width=device-width, initial-scale=1" name="viewport"> 
    <meta name="x-apple-disable-message-reformatting"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta content="telephone=no" name="format-detection"> 
    <title>Notificação de Monitoramento</title> 
    <style>

    </style> 
</head> 
<body style="background: #f7f7f7; font-family: Tahoma, Arial,sans-serif; font-size: 12px; padding-top: 20px; padding-bottom: 20px;">
    <div style="width: 90%; margin: 0 auto; background: white; padding: 10px 20px; margin-top: 30px;">
        <table>
            <thead>
                <tr>
                    <td style="width: 100%;">
                        <h4 style="text-align: center;">Processamento de Monitoramento - Erro de Execução</h4>
                    </td>
                </tr>
            </thead>
        </table>  
        <table>
            <tbody>
                <tr>
                    <td style="width: 100%;">
                        <p><strong>Cliente</strong>: {{ $dados['cliente'] }}</p>
                        <p><strong>Expressão</strong>: {{ $dados['expressao'] }}</p>
                        <p><strong>URL</strong>: <a href="https://studioclipagem.com/monitoramento/{{ $dados['id'] }}/editar" target="BLANK">https://studioclipagem.com/monitoramento/{{ $dados['id'] }}/editar</a></p>
                    </td>
                </tr>
            </tbody>
        </table>  
    </div> 
  </body>
</html>