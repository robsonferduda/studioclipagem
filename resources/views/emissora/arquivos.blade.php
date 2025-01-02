@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-volume-up"></i> Rádio
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Arquivos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('radio/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('emissoras/radio') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-volume-up"></i> Emissoras de Rádio</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['radio/arquivos']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Fonte</label>
                                        <select class="form-control select2" name="fonte" id="fonte">
                                            <option value="">Selecione uma fonte</option>
                                            @foreach ($emissoras as $emissora)
                                                <option value="{{ $emissora->id }}" {{ ($emissora->id == $fonte) ? 'selected' : '' }}>{{ $emissora->nome_emissora }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="expressao" class="form-label">Expressão de Busca <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <textarea class="form-control" name="expressao" id="expressao" rows="3">{{ $expressao }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}

                    @if(count($arquivos) > 0)
                        <h6 class="px-3">Mostrando {{ $arquivos->count() }} de {{ $arquivos->total() }} Páginas</h6>

                        {{ $arquivos->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final, 'fonte' => $fonte, 'expressao' => $expressao])->links('vendor.pagination.bootstrap-4') }}
                    @endif

                    @foreach ($arquivos as $key => $noticia)
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <div style="text-align: center;">
                                            @if($noticia->emissora and $noticia->emissora->logo)
                                                <img src="{{ asset('img/emissoras/'.$noticia->emissora->logo) }}" alt="Logo {{ ($noticia->emissora) ? $noticia->emissora->nome_emissora : 'Não identificada' }}" style="width: 90%; padding: 25px;">
                                            @endif
                                            @if(Storage::disk('s3')->temporaryUrl($noticia->path_s3, '+30 minutes'))
                                                <audio width="100%" controls style="width: 100%;">
                                                    <source src="{{ Storage::disk('s3')->temporaryUrl($noticia->path_s3, '+30 minutes') }}" type="audio/mpeg">
                                                    Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.
                                                </audio>
                                            @else

                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-9 col-sm-12">
                                        <p><strong>{{ ($noticia->emissora) ? $noticia->emissora->nome_emissora : 'Não identificada' }}</strong></p>
                                        <p> {{ \Carbon\Carbon::parse($noticia->data_hora_inicio)->format('d/m/Y') }} - De {{ \Carbon\Carbon::parse($noticia->data_hora_inicio)->format('H:i:s') }} às {{ \Carbon\Carbon::parse($noticia->data_hora_fim)->format('H:i:s') }}</p>
                                        <p>
                                            {!! Str::limit($noticia->transcricao, 700, '<a href="../radio/arquivos/detalhes/'.$noticia->id.'"><strong> Veja Mais</strong></a>') !!}
                                        </p>                                        
                                        <p class="mb-2"><strong>Retorno de Mídia</strong>: {!! ($noticia->valor_retorno) ? "R$ ".$noticia->valor_retorno : '<span class="text-danger">Não calculado</span>' !!}</p>
                                        <div>
                                            <a href="{{ url('jornal-impresso/noticia/extrair/web',$noticia->id) }}" class="btn btn-success btn-sm"><i class="fa fa-database"></i> Extrair Notícia</a> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection