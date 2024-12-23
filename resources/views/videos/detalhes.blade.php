@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Vídeos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Detalhes
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('tv/videos') }}" class="btn btn-warning pull-right"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <video width="100%" height="240" controls>
                                        <source src="{{ Storage::disk('s3')->temporaryUrl($video->video_path, '+30 minutes') }}" type="video/mp4">
                                        <source src="movie.ogg" type="video/ogg">
                                        Seu navegador não suporta a exibição de vídeos.
                                      </video>   
                                </div>
                                <div class="col-lg-8 col-sm-12">
                                    <p class="mb-1"><strong>{{ $video->programa->emissora->nome_emissora }}</strong> - <strong>{{ $video->programa->nome_programa }}</strong></p>
                                    <p class="mb-1">
                                        {{ date('d/m/Y', strtotime($video->horario_start_gravacao)) }} - Das 
                                        {{ date('H:i:s', strtotime($video->horario_start_gravacao)) }} às 
                                        {{ date('H:i:s', strtotime($video->horario_end_gravacao)) }}
                                    </p>
                                    <p>
                                        {!! $video->transcricao !!}
                                    </p> 
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection