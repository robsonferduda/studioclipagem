@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras 
                    </h4>
                </div>
                <div class="col-md-4">
                   
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>    
            <div class="col-lg-3 col-sm-12">
                <div style="text-align: center;">
                    <img src="{{ asset('img/emissoras/'.$gravacao->emissora->logo) }}" alt="Logo {{ ($gravacao->emissora) ? $gravacao->emissora->nome_emissora : 'Não identificada' }}" style="width: 90%; padding: 25px;">
                    <audio width="100%" controls style="width: 100%;">
                        <source src="{{ Storage::disk('s3')->temporaryUrl($gravacao->path_s3, '+30 minutes') }}" type="audio/mpeg">
                        Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.
                    </audio>
                </div>
            </div>
            <div class="col-lg-12 col-sm-12">
                <p><strong>{{ ($gravacao->emissora) ? $gravacao->emissora->nome_emissora : 'Não identificada' }}</strong></p>
                <p> {{ \Carbon\Carbon::parse($gravacao->dt_clipagem)->format('d/m/Y') }} - De {{ \Carbon\Carbon::parse($gravacao->data_hora_inicio)->format('H:i:s') }} às {{ \Carbon\Carbon::parse($gravacao->data_hora_fim)->format('H:i:s') }}</p>
                <p>
                    {!! $gravacao->transcricao !!}
                </p>                                        
                <p class="mb-2"><strong>Retorno de Mídia</strong>: {!! ($gravacao->valor_retorno) ? "R$ ".$gravacao->valor_retorno : '<span class="text-danger">Não calculado</span>' !!}</p>
                <div>
                    <a href="{{ url('jornal-impresso/noticia/extrair/web',$gravacao->id) }}" class="btn btn-success btn-sm"><i class="fa fa-database"></i> Extrair Notícia</a> 
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');

        });
    </script>
@endsection