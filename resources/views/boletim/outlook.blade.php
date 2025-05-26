<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Security-Policy" content="script-src 'none'; connect-src 'none'; object-src 'none'; form-action 'none';"> 
    <meta charset="UTF-8"> 
    <meta content="width=device-width, initial-scale=1" name="viewport"> 
    <meta name="x-apple-disable-message-reformatting"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta content="telephone=no" name="format-detection"> 
    <title>Boletim Digital</title> 
    <style>

    </style> 
</head> 
<body style="background: white; font-family: Tahoma, Arial,sans-serif; font-size: 12px; padding-top: 20px; padding-bottom: 20px;">
    <div style="width: 800px; margin: 0 auto; background: white; padding: 10px 20px; margin-top: 30px;">
    <table width="800px;" style="width: 800px; background: white;">
        <tr>
            <td>
                <table width="800px;" style="width: 800px;">
                    <tbody>
                        <tr>
                            <td>
                                <img src="{{ asset('img/banner/'.$boletim->cliente->logo ) }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="text-align: right;">
                    @if(count($noticias_impresso) > 1)
                        <span>Foram encontradas  {{ count($noticias_impresso) }} notícias</span>
                    @else
                        <span>Foi encontrada  {{ count($noticias_impresso) }} notícia</span>
                    @endif
                </div>  
                <div style="text-align: right; margin-top: 5px;">
                    <span><a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}">Clique aqui</a> para ver o boletim no navegador</span>
                </div>
            </td>
        </tr>
    </table>
    </div> 
  </body>
</html>