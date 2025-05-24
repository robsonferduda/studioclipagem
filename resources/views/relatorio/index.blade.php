@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-pdf-o ml-3"></i> Relatórios 
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
                {!! Form::open(['id' => 'frm_user_create', 'url' => ['relatorios']]) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Termo de busca</label>
                                <input type="text" class="form-control" name="termo" id="termo" placeholder="Termo" value="{{ old('nome') }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data Inicial</label>
                                <input type="text" class="form-control" name="dt_inicial" id="dt_inicial" placeholder="__/__/____" value="{{ date('d/m/Y') }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data Final</label>
                                <input type="text" class="form-control" name="dt_final" id="dt_final" placeholder="__/__/____" value="{{ date('d/m/Y') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cliente</label>
                                <select class="form-control select2" name="emissora_id" id="emissora_id">
                                    <option value="">Selecione um cliente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Área</label>
                                <select class="form-control select2" name="emissora_id" id="emissora_id">
                                    <option value="">Selecione uma área</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control select2" name="emissora_id" id="emissora_id">
                                    <option value="">Selecione um status</option>
                                </select>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="true">
                                        Clipagem de Jornal
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="true">
                                        Clipagem de Web
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="true">
                                        Clipagem de Rádio
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Emissora</label>
                                <select class="form-control select2" name="emissora_id" id="emissora_id">
                                    <option value="">Selecione uma emissora</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Programa</label>
                                <select class="form-control select2" name="emissora_id" id="emissora_id">
                                    <option value="">Selecione um programa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Horário Inicial</label>
                                <input type="text" class="form-control horario" name="hora_inicio" id="hora_inicio" placeholder="00:00" value="">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Horário Final</label>
                                <input type="text" class="form-control horario" name="hora_fim" id="hora_fim" placeholder="00:00" value="">
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="true">
                                        Clipagem de TV
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Emissora</label>
                                <select class="form-control select2" name="emissora_id" id="emissora_id">
                                    <option value="">Selecione uma emissora</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Programa</label>
                                <select class="form-control select2" name="emissora_id" id="emissora_id">
                                    <option value="">Selecione um programa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Horário Inicial</label>
                                <input type="text" class="form-control horario" name="hora_inicio" id="hora_inicio" placeholder="00:00" value="">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Horário Final</label>
                                <input type="text" class="form-control horario" name="hora_fim" id="hora_fim" placeholder="00:00" value="">
                            </div>
                        </div>                 
                    </div>             
                    <div class="card-footer text-center mb-3">
                        <button type="submit" class="btn btn-danger" name="acao" value="gerar-pdf"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                        <button type="submit" class="btn btn-info" name="acao" value="pesquisar"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                {!! Form::close() !!} 
            </div>
            <div class="col-md-12">
                @forelse ($dados as $noticia)
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    @switch($noticia->tipo)
                                        @case('web')
                                            @php
                                                $tipo_formatado = '<i class="fa fa-globe"></i> Web';
                                            @endphp
                                        @break
                                        @case('tv')
                                            @php
                                                $tipo_formatado = '<i class="fa fa-television"></i> TV';
                                            @endphp
                                        @break
                                        @case('radio')
                                            @php
                                                $tipo_formatado = '<i class="fa fa-volume-up"></i> Rádio';
                                            @endphp
                                        @break
                                        @case('impresso')
                                            @php
                                                $tipo_formatado = '<i class="fa fa-newspaper-o"></i> Impressos';
                                            @endphp
                                        @break
                                        @default
                                            @php
                                                $tipo_formatado = 'Clipagens';
                                            @endphp
                                        @break                                    
                                    @endswitch
                                    <p style="text-transform: uppercase; font-weight: 600;">{!! $tipo_formatado !!}</p>
                            
                                    <h6 style="font-weight: 500;">{{ $noticia->titulo }}</h6>
                                    <p>
                                        {!! $noticia->sinopse !!}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <div style="font-size: 18px;">
                                        @switch($noticia->sentimento)
                                            @case("Negativo")
                                                    <i class="fa fa-frown-o text-danger"></i>
                                                    <i class="fa fa-ban op-2"></i>
                                                    <i class="fa fa-smile-o op-2"></i>
                                                @break
                                            @case("Neutro")
                                                    <i class="fa fa-frown-o op-2"></i>
                                                    <i class="fa fa-ban text-primary"></i>
                                                    <i class="fa fa-smile-o op-2"></i>                                               
                                                @break
                                            @case("Positivo")
                                                    <i class="fa fa-frown-o op-2"></i>
                                                    <i class="fa fa-ban op-2"></i>
                                                    <i class="fa fa-smile-o text-success"></i>
                                            @break                                            
                                        @endswitch
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div style="text-align: right">
                                        <a class="" href="{{ url("relatorios/".$noticia->tipo."/pdf/".$noticia->id) }}" role="button"><i class="fa fa-file-pdf-o"> </i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <h6>Nenhum dado para exibição</h6>
                @endforelse
            </div>
        </div>
    </div>
</div> 
@endsection