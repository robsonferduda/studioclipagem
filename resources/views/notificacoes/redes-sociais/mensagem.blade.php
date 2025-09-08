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
    <div style="width: 800px; margin: 0 auto; background: white; padding: 10px 20px; margin-top: 30px;">
        <table>
            <tbody>
                <tr>
                    <td style="width: 10%;">
                        <img style="width: 80%;" src="https://studiosocial.app/img/studio_social.png">
                    </td>
                    <td style="width: 90%;">
                        <h1 style="text-align: center;">Notificação de Monitoramento de Rede Social</h1>
                    </td>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <td>
                       <p style="font-size: 15px;">{!! $msg !!}</p>
                    </td>
                </tr>
                @for ($i = 0; $i < count($postagens); $i++)
                    <tr>
                        <td>
                            <p>
                                <img style="vertical-align: middle; width: 4%;" src="https://studiosocial.app/img/icon/{{ $postagens[$i]['img'] }}.png"> 
                                <a href="{{ $postagens[$i]['link'] }}">{{ $postagens[$i]['msg'] }}</a>
                            </p>
                        </td>
                    </tr>   
                @endfor
            </tbody>
        </table>          
    </div> 
  </body>
</html>