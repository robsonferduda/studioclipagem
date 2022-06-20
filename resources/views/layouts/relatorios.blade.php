<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="base-url" content="{{ env('BASE_URL') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
        <link href="images/favicon.png" rel="shortcut icon">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>{{ config('app.name', 'Studio K Sistema de Gerenciamento de Eventos') }}</title>
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
        <!--     Fonts and icons     -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
        <!-- CSS Files -->
        <link href="{{ public_path('css/bootstrap.min.css') }}" rel="stylesheet" />
        <link href="{{ public_path('css/paper-dashboard.css?v=2.0.1') }}" rel="stylesheet" />
        <!-- CSS Just for demo purpose, don't include it in your project -->
        <link href="{{ public_path('demo/demo.css') }}" rel="stylesheet" />
        <link href="{{ public_path('css/custom.css') }}" rel="stylesheet" />
        <link href="{{ public_path('css/schedule.css') }}" rel="stylesheet" />
        <link href="{{ public_path('css/croppie.min.css') }}" rel="stylesheet" />
        <link href="{{ public_path('css/jqcloud.min.css') }}" rel="stylesheet" />
        <link href="{{ public_path('css/jquery.loader.min.css') }}" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    </head>
    <body>
        <div class="content">       
            @yield('content')          
        </div>
    </body>
</html>