<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/paper-dashboard.css?v=2.0.1') }}" rel="stylesheet" />
  <title>{{ $boletim->titulo }}</title>
  <style>
      .corpo_boletim{
        background: #f7f7f7;
        padding-bottom: 25px;
      }
      .wrapper{
          width: 800px;
          margin: 60px auto;
          margin-bottom: 30px;
      }
    </style>
</head>
<body class="corpo_boletim">
    <div class="wrapper">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                           <img src="{{ asset('img/clientes/logo_expandida/'.$boletim->cliente->logo_expandida) }}">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include("boletim/corpo_boletim")       
                </div>
            </div>
        </div>
    </div>
</body>