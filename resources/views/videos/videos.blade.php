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
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['tv/videos']]) !!}
                    <div class="form-group m-3 w-70">
                        <div class="row mb-0">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Data Inicial</label>
                                    <input type="text" class="form-control datepicker dt-search" name="dt_inicial" id="dt_inicial" required="true" value="{{ ($dt_inicial) ? date('d/m/Y', strtotime($dt_inicial)) : date('d/m/Y') }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Data Final</label>
                                    <input type="text" class="form-control datepicker dt-search" name="dt_final" required="true" value="{{ ($dt_final) ? date('d/m/Y', strtotime($dt_final)) : date('d/m/Y') }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Fonte</label>
                                    <select class="form-control select2" name="regra" id="regra">
                                        <option value="">Selecione uma fonte</option>
                                        @foreach ($emissoras as $emissora)
                                            <option value="{{ $emissora->id }}">{{ $emissora->nome_emissora }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca na transcrição</span></label>
                                    <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-md-12 checkbox-radios mb-0">
                                <button type="submit" id="btn-find" class="btn btn-primary mt-4 btn-search"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
            <div class="col-md-12">
                @if(count($videos) > 0)
                    <h6 class="px-3">Mostrando {{ $videos->count() }} de {{ $videos->total() }} vídeos coletados</h6>
                    {{ $videos->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }}
                @endif

                @foreach ($videos as $key => $video)
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
                                        {!! Str::limit($video->transcricao, 700, '<a href="../tv/video/detalhes/'.$video->id.'"><strong> Veja Mais</strong></a>') !!}
                                    </p> 
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection