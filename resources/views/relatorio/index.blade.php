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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tipo de Data</label>
                                <select class="form-control select2" name="tipo_data" id="tipo_data">
                                    <option value="data_cadastro" {{ ($tipo_data == "data_cadastro") ? 'selected' : '' }}>Data de Cadastro</option>
                                    <option value="data_noticia" {{ ($tipo_data == "data_noticia") ? 'selected' : '' }}>Data do Clipping</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data Inicial</label>
                                <input type="text" class="form-control datepicker" name="dt_inicial" id="dt_inicial" placeholder="__/__/____" value="{{ ($dt_inicial) ? \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data Final</label>
                                <input type="text" class="form-control datepicker" name="dt_final" id="dt_final" placeholder="__/__/____" value="{{ ($dt_final) ? \Carbon\Carbon::parse($dt_final)->format('d/m/Y') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cliente</label>
                                <select class="form-control cliente" name="id_cliente" id="id_cliente">
                                    <option value="">Selecione um cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ ($cliente_selecionado ==  $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Área</label>
                                <select class="form-control area" name="id_area" id="id_area">
                                    <option value="">Selecione uma área</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control select2" name="cd_sentimento" id="cd_sentimento">
                                    <option value="">Selecione um status</option>
                                    <option value="">Selecione um sentimento</option>
                                    <option value="1">Positivo</option>
                                    <option value="0">Neutro</option>
                                    <option value="-1">Negativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <label>Termo de busca</label>
                                <input type="text" class="form-control" name="termo" id="termo" placeholder="Termo" value="{{ old('nome') }}">
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="fl_impresso" {{ ($fl_impresso == true) ? 'checked' : '' }} value="true">
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
                                        <input class="form-check-input" type="checkbox" name="fl_web" {{ ($fl_web == true) ? 'checked' : '' }} value="true">
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
                                        <input class="form-check-input" type="checkbox" name="fl_radio" {{ ($fl_radio == true) ? 'checked' : '' }} value="true">
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
                                        <input class="form-check-input" type="checkbox" name="fl_tv" {{ ($fl_tv == true) ? 'checked' : '' }} value="true">
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
                                    <h6 style="font-weight: 600;">{{ $noticia->titulo }}</h6>
                                    <h6 style="font-weight: 600;" class="text-muted">{{ $noticia->data_formatada }} - {{ $noticia->fonte }}</h6>
                                    <p class="mb-2">
                                        <span>{{ $noticia->cliente }}</span>
                                        @switch($noticia->sentimento)
                                            @case(-1)
                                                <i class="fa fa-frown-o text-danger"></i>
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>
                                            @break
                                            @case(0)
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a> 
                                                <i class="fa fa-ban text-primary"></i>
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>                                            
                                            @break
                                            @case(1)
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a>
                                                <a href="{{ url('noticia/'.$noticia->id.'/tipo/'.$noticia->tipo.'/cliente/'.$noticia->tipo.'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                                                <i class="fa fa-smile-o text-success"></i>
                                            @break                                            
                                        @endswitch
                                    </p>
                                    <p>
                                        {!! $noticia->sinopse !!}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div style="text-align: right">
                                        @if($noticia->tipo == 'web')
                                            <a title="Extrair Imagem" class="btn btn-warning btn-sm" href="{{ url('noticia/web/importar-imagem',$noticia->id) }}" role="button"><i class="fa fa-picture-o"> </i></a>
                                            <a title="Editar" class="btn btn-info btn-sm" href="{{ url('noticia/web/'.$noticia->id.'/editar') }}" target="_BLANK" role="button"><i class="fa fa-edit"> </i></a>
                                        @endif
                                        @if($noticia->tipo == 'impresso')
                                            <a title="Editar" class="btn btn-info btn-sm" href="{{ url('noticia-impressa/'.$noticia->id.'/editar') }}" target="_BLANK" role="button"><i class="fa fa-edit"> </i></a>
                                        @endif
                                        <a title="Gerar PDF" class="btn btn-danger btn-sm" href="{{ url("relatorios/".$noticia->tipo."/pdf/".$noticia->id) }}" role="button"><i class="fa fa-file-pdf-o"> </i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    
                @endforelse
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

        //Inicializa o combo de clientes
        $.ajax({
            url: host+'/api/cliente/buscarClientes',
            type: 'GET',
            beforeSend: function() {
                    
            },
            success: function(data) {
                if(!data) {
                    Swal.fire({
                        text: 'Não foi possível buscar os clientes. Entre em contato com o suporte.',
                        type: "warning",
                        icon: "warning",
                    });
                    return;
                }

                data.forEach(element => {
                    let option = new Option(element.text, element.id);
                    $('.cliente').append(option);
                });
            },
            complete: function(){
                       
            }
        });

        $(document).on('change', '.cliente', function() {
            var cliente = $(this).val();
            buscarAreas(cliente);
        });

        function buscarAreas(cliente){

            if(cliente == '') {
                $('.area').attr('disabled', true);
                $('.area').append('<option value="">Cliente não possui áreas</option>').val('');
                return;
            }

            $.ajax({
                url: host+'/api/cliente/getAreasCliente',
                type: 'GET',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "cliente": cliente,
                },
                beforeSend: function() {
                    $('.area').append('<option value="">Carregando...</option>').val('');
                },
                success: function(data) {

                    $('.area').find('option').remove();
                    $('.area').attr('disabled', false);

                    if(data.length == 0) {                            
                        $('.area').append('<option value="">Cliente não possui áreas vinculadas</option>').val('');
                        return;
                    }
                            
                    $('.area').append('<option value="">Selecione uma área</option>').val('');
                    data.forEach(element => {
                        let option = new Option(element.descricao, element.id);
                        $('.area').append(option);
                    });             
                },
                complete: function(){
                            
                }
            });
        }   

    });
</script>
@endsection