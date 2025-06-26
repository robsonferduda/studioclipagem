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
                            <div class="mb-3">
                                <div class="btn-group" role="group" id="presetsData">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="hoje">Hoje</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="ontem">Ontem</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="7dias">Últimos 7 dias</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="30dias">Últimos 30 dias</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="mes">Este mês</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="mesanterior">Mês anterior</button>
                                </div>
                            </div>
                        </div>
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
                        <div class="col-md-6">
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Áreas do Cliente</label>
                                <div id="areas-checkbox-group" class="d-flex flex-wrap gap-2">
            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Termo de busca</label>
                                <input type="text" class="form-control" name="termo" id="termo" placeholder="Termo" value="{{ old('nome') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold mb-2">
                                Sentimento
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <label class="form-check-label check-midia">
                                        <input class="form-check-input" type="checkbox" name="sentimento[]" value="1" id="sentimento_positivo" checked>
                                        <span class="form-check-sign"></span>
                                        <span class="text-success"><i class="fa fa-smile-o text-success"></i> Positivo</span>
                                    </label>
                                </div>
                                <div class="form-check ml-3">
                                    <label class="form-check-label check-midia">
                                        <input class="form-check-input" type="checkbox" name="sentimento[]" value="-1" id="sentimento_negativo" checked>
                                        <span class="form-check-sign"></span>
                                        <span class="text-danger"><i class="fa fa-frown-o text-danger"></i> Negativo</span>
                                    </label>
                                </div>
                                <div class="form-check ml-3">
                                    <label class="form-check-label check-midia">
                                        <input class="form-check-input" type="checkbox" name="sentimento[]" value="0" id="sentimento_neutro" checked>
                                        <span class="form-check-sign"></span>
                                        <span class="text-secondary"><i class="fa fa-ban text-default"></i> Neutro</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-semibold mb-2">
                                Clipagem por tipo de mídia
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <div class="form-check">
                                        <label class="form-check-label check-midia">
                                            <input class="form-check-input" type="checkbox" name="fl_impresso" {{ ($fl_impresso == true) ? 'checked' : '' }} checked value="true">
                                            <span class="form-check-sign"></span>
                                            <span class="text-secondary"><i class="fa fa-newspaper-o"></i> Impressos</span>
                                        </label>
                                    </div>
                                </div>
                        
                                <div class="form-check ml-3">
                                    <div class="form-check">
                                        <label class="form-check-label check-midia">
                                            <input class="form-check-input" type="checkbox" name="fl_web" {{ ($fl_web == true) ? 'checked' : '' }} checked value="true">
                                            <span class="form-check-sign"></span>
                                            <span class="text-secondary"><i class="fa fa-globe"></i> Web</span>
                                        </label>
                                    </div>
                                </div>
                        
                                <div class="form-check ml-3">
                                    <div class="form-check">
                                        <label class="form-check-label check-midia">
                                            <input class="form-check-input" type="checkbox" name="fl_radio" {{ ($fl_radio == true) ? 'checked' : '' }} checked value="true">
                                            <span class="form-check-sign"></span>
                                            <span class="text-secondary"><i class="fa fa-volume-up"></i> Rádio</span>
                                        </label>
                                    </div>
                                </div>
                        
                                <div class="form-check ml-3">
                                    <div class="form-check">
                                        <label class="form-check-label check-midia">
                                            <input class="form-check-input" type="checkbox" name="fl_tv" {{ ($fl_tv == true) ? 'checked' : '' }} checked value="true">
                                            <span class="form-check-sign"></span>
                                            <span class="text-secondary"><i class="fa fa-television"></i> TV</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>        
                    </div>             
                    <div class="card-footer text-center mb-3">
                        <button type="button" class="btn btn-info" id="btn-pesquisar" name="acao" value="pesquisar"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                {!! Form::close() !!} 
            </div>
            <div class="border-top p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex gap-4 flex-wrap align-items-center">
                        <div>
                            <strong>Total encontradas:</strong>
                            <span id="totalNoticias" class="badge bg-secondary">0</span>
                        </div>
                        <div class="ml-2">
                            <strong>Selecionadas para relatório:</strong>
                            <span id="totalSelecionadas" class="badge bg-info">0</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center mt-2 mt-md-0">
                        <button type="button" class="btn btn-primary" id="btnSelecionarTodasGeral">
                            <i class="fa fa-list-ul" aria-hidden="true"></i>
                            Selecionar Todas
                        </button>
                        <button type="button" class="btn btn-danger" id="btnGerarRelatorio">
                            <i class="fa fa-file-pdf-o"></i>
                            Gerar Relatório PDF (<span id="qtdSelecionadasBtn">0</span>)
                        </button>
                    </div>
                </div>
            </div>
            <div id="resultado-relatorio">
                {{-- Os resultados serão inseridos aqui via AJAX --}}
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

        $(document).on('click', '#btn-pesquisar', function(e) {
            e.preventDefault();

            var form = $('#frm_user_create');
            var url = form.attr('action');
            var dados = form.serialize();

            $('#resultado-relatorio').html('<div class="text-center my-4"><i class="fa fa-spinner fa-spin fa-2x"></i> Carregando...</div>');

            $.ajax({
                url: url,
                type: 'POST',
                data: dados,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#resultado-relatorio').html(response.html);
                },
                error: function(xhr) {
                    $('#resultado-relatorio').html('<div class="alert alert-danger">Erro ao buscar resultados.</div>');
                }
            });
        });

        $('#presetsData button').on('click', function() {
            let preset = $(this).data('preset');
            let hoje = moment();
            let dt_inicial = '';
            let dt_final = '';

            switch(preset) {
                case 'hoje':
                    dt_inicial = hoje.format('DD/MM/YYYY');
                    dt_final = hoje.format('DD/MM/YYYY');
                    break;
                case 'ontem':
                    dt_inicial = hoje.clone().subtract(1, 'days').format('DD/MM/YYYY');
                    dt_final = hoje.clone().subtract(1, 'days').format('DD/MM/YYYY');
                    break;
                case '7dias':
                    dt_inicial = hoje.clone().subtract(6, 'days').format('DD/MM/YYYY');
                    dt_final = hoje.format('DD/MM/YYYY');
                    break;
                case '30dias':
                    dt_inicial = hoje.clone().subtract(29, 'days').format('DD/MM/YYYY');
                    dt_final = hoje.format('DD/MM/YYYY');
                    break;
                case 'mes':
                    dt_inicial = hoje.clone().startOf('month').format('DD/MM/YYYY');
                    dt_final = hoje.format('DD/MM/YYYY');
                    break;
                case 'mesanterior':
                    dt_inicial = hoje.clone().subtract(1, 'months').startOf('month').format('DD/MM/YYYY');
                    dt_final = hoje.clone().subtract(1, 'months').endOf('month').format('DD/MM/YYYY');
                    break;
            }

            $('#dt_inicial').val(dt_inicial);
            $('#dt_final').val(dt_final);
        });

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
        // Limpa o grupo antes de adicionar
        $('#areas-checkbox-group').empty();

        if(cliente == '') {
            $('#areas-checkbox-group').html('<span class="text-muted">Cliente não possui áreas</span>');
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
                $('#areas-checkbox-group').html('<span>Carregando...</span>');
            },
            success: function(data) {
                $('#areas-checkbox-group').empty();

                if(data.length == 0) {                            
                    $('#areas-checkbox-group').html('<span class="text-muted">Cliente não possui áreas vinculadas</span>');
                    return;
                }
                
               data.forEach(element => {
                let checkbox = `
                    <div class="form-check mr-3">
                        <label class="form-check-label">
                            <input class="form-check-input area-checkbox" type="checkbox" name="areas[]" value="${element.id}" id="area_${element.id}">
                            <span class="form-check-sign"></span>
                            ${element.descricao}
                        </label>
                    </div>
                `;
                $('#areas-checkbox-group').append(checkbox);
            });            
            }
        });
    }  

    });
    
    $(document).ready(function(){
        $("#id_cliente").trigger('change');
    });
</script>
@endsection