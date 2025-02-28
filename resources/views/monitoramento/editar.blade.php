@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card load-busca">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="nc-icon nc-sound-wave ml-2"></i> Monitoramento 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Editar 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('monitoramentos') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                    <a href="{{ url('monitoramentos') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="nc-icon nc-sound-wave ml-2"></i> Monitoramentos</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row mr-1">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['monitoramento/update']]) !!}
                        <div class="form-group m-3">
                            <div class="row">
                                <input type="hidden" name="id" id="id" value="{{ $monitoramento->id }}">
                                <input type="hidden" name="url_origem" value="{{ url()->previous() }}">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nome <span class="text-danger">Obrigatório</span></label>
                                        <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" value="{{ $monitoramento->nome }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="id_cliente" id="id_cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ ($monitoramento->id_cliente == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker dt_inicial_relatorio" name="dt_inicio" value="{{ \Carbon\Carbon::parse($monitoramento->dt_inicio)->format('d/m/Y') }}">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker dt_final_relatorio" name="dt_fim" value="{{ \Carbon\Carbon::parse($monitoramento->dt_fim)->format('d/m/Y') }}">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Hora Inicial</label>
                                        <input type="text" class="form-control horario" name="hora_inicio" value="{{ \Carbon\Carbon::parse($monitoramento->hora_inicio)->format('H:i') }}">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Hora Final</label>
                                        <input type="text" class="form-control horario" name="hora_fim" value="{{ \Carbon\Carbon::parse($monitoramento->hora_fim)->format('H:i') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label>Frequência de Coletas</label>
                                        <select class="form-control" name="frequencia" id="frequencia">
                                            <option value="">Selecione o valor em horas</option>
                                            <option value="1" {{ ($monitoramento->frequencia == 1) ? 'selected' : '' }}>1 hora</option>
                                            @for($i = 2; $i <= 24; $i++)
                                                <option value="{{ $i }}" {{ ($monitoramento->frequencia == $i) ? 'selected' : '' }}>{{ $i }} horas</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 mt-3">
                                    <p class="mb-1">Selecione uma mídia</p>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($monitoramento->fl_impresso) ? 'checked' : '' }} name="fl_impresso" id="fl_impresso" value="true">
                                            IMPRESSO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($monitoramento->fl_web) ? 'checked' : '' }} name="fl_web" id="fl_web" value="true">
                                            WEB
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($monitoramento->fl_radio) ? 'checked' : '' }} name="fl_radio" id="fl_radio" value="true">
                                            RÁDIO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($monitoramento->fl_tv) ? 'checked' : '' }} name="fl_tv" id="fl_tv" value="true">
                                            TV
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>                                   
                                </div>
                                <div class="col-md-12 col-sm-12 mt-3">
                                    <p class="mb-0">
                                        <i class="fa fa-database fa-1x"></i> Fontes
                                        <button type="button" class="btn btn-sm btn-primary btn-icon btn-email" style="border-radius: 50%; height: 1.5rem;
                                        min-width: 1.5rem;
                                        width: 1.5rem;" data-toggle="modal" data-target="#modalFontes"><i class="fa fa-plus fa-2x"></i></button>
                                    </p>
                                    <p id="selecionadasTexto" class="mt-3">Fontes selecionadas: 0</p>
                                    <input type="hidden" name="selecionadas[]" id="selecionadas">
                                </div>
                                
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Fonte</label>
                                            <div class="form-group">
                                                <label>Estado <span class="text-danger">Obrigatório</span></label>
                                                <select class="form-control select2" name="cd_estado" id="cd_estado">
                                                    <option value="">Selecione um estado</option>
                                                    @foreach ($estados as $estado)
                                                        <option value="{{ $estado->cd_estado }}">{{ $estado->nm_estado }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select multiple="multiple" size="10" name="fontes[]" id="fontes" class="demo1 form-control">
                                                    @foreach ($fontes as $fonte)
                                                        <option value="{{ $fonte['id'] }}" {{ $fonte['flag'] }}>{{ $fonte['estado']."-" }}  {{ $fonte['nome'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                
                                <div class="col-md-12 col-sm-12 mt-3">
                                    <div class="form-group">
                                        <label for="expressao" class="form-label">Expressão de Busca <span class="text-danger">Campo obrigatório</span></label>
                                        <textarea class="form-control" name="expressao" id="expressao" rows="3">{{ $monitoramento->expressao }}</textarea>
                                    </div>
                                </div>
                            </div>               
                            <div class="col-md-12 text-center">
                                <button type="button" id="btn-find" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                                <a href="{{ url('monitoramento') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div>   
                <div class="col-lg-12 col-md-12">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                        <ul id="tabs" class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                            <a class="nav-link active" id="nav-web" data-toggle="tab" href="#panel_web" role="tab" aria-expanded="true" aria-selected="false"><i class="fa fa-globe"></i> Web <span class="monitoramento-total monitoramento-total-web">0</span></a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" id="nav-impresso" data-toggle="tab" href="#panel_impresso" role="tab" aria-expanded="false"><i class="fa fa-newspaper-o"></i> Impressos <span class="monitoramento-total monitoramento-total-impresso">0</span></a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" id="nav-radio" data-toggle="tab" href="#panel_radio" role="tab" aria-expanded="false" aria-selected="true"><i class="fa fa-volume-up"></i> Rádio <span class="monitoramento-total monitoramento-total-radio">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="nav-tv" data-toggle="tab" href="#panel_tv" role="tab" aria-expanded="false" aria-selected="true"><i class="fa fa-volume-up"></i> TV <span class="monitoramento-total monitoramento-total-tv">0</span></a>
                            </li>
                        </ul>
                        </div>
                    </div>
                    <div id="my-tab-content" class="tab-content">
                        
                        <div class="tab-pane active" id="panel_web" role="tabpanel" aria-expanded="true">
                            <div id="accordion_web" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca cabecalho-busca-web d-none">
                                    <div class="col-md-6">
                                        <p class="card-title mb-0">Foram encontradas <strong class="monitoramento-total-web"></strong> notícias</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-primary">
                                                Todas as fontes
                                            </span>
                                        </div>
                                    </div>
                                </div>   
                                <div class="row cabecalho-aguardando-busca-web">
                                    <div class="col-md-6">
                                        <span class="text-info">Aguardando critérios de busca</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="panel_impresso" role="tabpanel" aria-expanded="false">
                            <div id="accordion_impresso" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca cabecalho-busca-impresso d-none">
                                    <div class="col-md-6">
                                        <p class="card-title mb-0">Foram encontradas <strong class="monitoramento-total-impresso"></strong> notícias</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-primary">
                                                Todas as fontes
                                            </span>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                        <div class="tab-pane" id="panel_radio" role="tabpanel" aria-expanded="false">
                            <div id="accordion_radio" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca cabecalho-busca-radio d-none">
                                    <div class="col-md-6">
                                        <p class="card-title mb-0">Foram encontradas <strong class="monitoramento-total-radio"></strong> notícias</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-primary">
                                                Todas as fontes
                                            </span>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                        <div class="tab-pane" id="panel_tv" role="tabpanel" aria-expanded="false">
                            <div id="accordion_tv" role="tablist" aria-multiselectable="true" class="card-collapse">
                                <div class="row cabecalho-busca cabecalho-busca-tv d-none">
                                    <div class="col-md-6">
                                        <p class="card-title mb-0">Foram encontradas <strong class="monitoramento-total-tv"></strong> notícias</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <span class="badge badge-pill badge-primary">
                                                Todas as fontes
                                            </span>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFontes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" >
          <div class="modal-content" style="width: 800px !important;">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-database"></i> Selecionar Fontes</h6>
            </div>
            <div class="modal-body" style="padding: 10px 15px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="filtroUF" class="form-label">Filtrar por Estado:</label>
                            <select class="form-control" name="filtro_uf" id="filtro_uf">
                                <option value="">Todos</option>
                            </select>
                        </div>
                    </div>    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Bucar por Emissora:</label>
                            <input type="mail" class="form-control" name="filtro_nome" id="filtro_nome">
                        </div>
                    </div>  
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary mb-3 pull-left" id="selecionarTodos">Selecionar Filtrados</button>
                        <button type="button" class="btn btn-warning mb-3 pull-right" id="limparSelecao">Limpar Seleção</button>
                    </div>
                    <div class="col-md-12">
                        <div class="table-container">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                    <th></th>
                                    <th>UF</th>
                                    <th>Cidade</th>
                                    <th>Emissora</th>
                                    </tr>
                                </thead>
                                <tbody id="tabela-fontes"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="center">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-primary" id="btn-selecionar"><i class="fa fa-check"></i> Fializar Seleção</button>
                </div>
          </div>
        </div>
      </div>
    </div>
</div> 
@endsection
@section('script')
<script>
        var host =  $('meta[name="base-url"]').attr('content');
        var token = $('meta[name="csrf-token"]').attr('content');
        
        let emissoras = [];
        let selecionadas = [];

        carregarEmissoras();
        carregarUFs();

        async function carregarEmissoras() {

            const id_monitoramento = document.getElementById('id').value;

            try {
                const response = await fetch(host+'/radio/emissoras/'+id_monitoramento);
                emissoras = await response.json();
                
                carregarTabela();
            } catch (error) {
                console.error('Erro ao carregar emissoras:', error);
            }
        }
       
        function atualizarSelecionadasTexto() {
            document.getElementById('selecionadasTexto').textContent = `Fontes selecionadas: ${selecionadas.length}`;
        }

        async function carregarUFs() {           

            const filtroUF = document.getElementById('filtro_uf');
            const response = await fetch(host+'/estado/siglas');

            ufs = await response.json();

            ufs.forEach(uf => {
                let option = document.createElement('option');
                option.value = uf.sg_estado;
                option.textContent = uf.sg_estado;
                filtroUF.appendChild(option);
            });
        }

        function carregarTabela() {

            const filtroUF = document.getElementById('filtro_uf').value;
            const filtroNome = document.getElementById('filtro_nome').value.toLowerCase();
            const tabela = document.getElementById('tabela-fontes');
            tabela.innerHTML = '';
            
            let filtradas = emissoras.filter(e => (filtroUF === '' || e.uf === filtroUF) && e.nome.toLowerCase().includes(filtroNome));

            filtradas.forEach((e, index) => {

                let row = tabela.insertRow();
                let cell1 = row.insertCell(0);
                let cell2 = row.insertCell(1);
                let cell3 = row.insertCell(2);
                let cell4 = row.insertCell(3);
                
                let checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = e.id;
                //checkbox.checked = selecionadas.includes(e.id);
                checkbox.checked = e.fl_filtro === true;
                checkbox.classList.add('checkbox-emissora');

                checkbox.addEventListener('change', (event) => {
                    if (event.target.checked) {
                        if (!selecionadas.includes(e.id)) {
                            selecionadas.push(e.id);
                        }
                    } else {
                        selecionadas = selecionadas.filter(i => i !== e.id);
                    }
                    atualizarSelecionadasTexto();
                });
                
                cell1.appendChild(checkbox);
                cell2.textContent = e.uf;
                cell3.textContent = e.cidade;
                cell4.textContent = e.nome;
            });
        }

        document.getElementById('selecionarTodos').addEventListener('click', function() {
            
            let checkboxes = document.querySelectorAll('.checkbox-emissora');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });
        });

        document.getElementById('limparSelecao').addEventListener('click', function() {
            selecionadas = [];
                       
            carregarTabela();
            atualizarSelecionadasTexto();
        });

        document.getElementById('btn-selecionar').addEventListener('click', function() {

            console.log(selecionadas);
            document.getElementById('selecionadas').value = selecionadas;
            $('#modalFontes').modal('hide');
            
        });

        document.getElementById('filtro_uf').addEventListener('change', carregarTabela);
        document.getElementById('filtro_nome').addEventListener('input', carregarTabela);
        
        document.addEventListener('DOMContentLoaded', () => {
            carregarUFs();
            carregarTabela();
        });
</script>
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');
        var token = $('meta[name="csrf-token"]').attr('content');

        var demo2 = $('.demo1').bootstrapDualListbox({
            nonSelectedListLabel: 'Disponíveis',
            selectedListLabel: 'Selecionadas',
            preserveSelectionOnMove: 'all',
            moveOnSelect: true
        });



        var fl_impresso = $("#fl_impresso").is(":checked");
        var fl_radio = $("#fl_radio").is(":checked");
        var fl_web = $("#fl_web").is(":checked");
        var fl_tv = $("#fl_tv").is(":checked");

        if(fl_impresso){
            $("#nav-impresso").trigger("click");
        }

        if(fl_radio){
            $("#nav-radio").trigger("click");
        }

        if(fl_web){
            $("#nav-web").trigger("click");
        }

        if(fl_tv){
            $("#nav-tv").trigger("click");
        }

        $("#btn-find").click(function(){

            var expressao = $("#expressao").val();
            var dt_inicial = $(".dt_inicial_relatorio").val();
            var dt_final = $(".dt_final_relatorio").val();
            var tipo_data = 'dt_publicacao';
            var flag = false;

            $(".msg-alerta").empty(); //Limpa as mensagens de erro

            if(!dt_inicial & !dt_final){
                flag = false;
                $(".msg-alerta").append('<p class="text-danger mb-0">Obrigatório selecionar uma data de início e uma data de fim. </p>');
            }else{
                flag = true;
            }

            if(!expressao){
                flag = false;
                $(".msg-alerta").append('<p class="text-danger mb-0">Obrigatório informar pelo menos uma expressão de busca. </p>');
            }else{
                flag = true;
            }

            if(flag){

                var fontes = $("#fontes").val();

                $('.tab-pane').each(function(i, obj) {
                    $(this).removeClass("active");
                });

                $('.nav-link').each(function(i, obj) {
                    $(this).removeClass("active");
                });

                //Busca Web
                if(fl_web){

                    $("#panel_web > .tab-pane").addClass("active");
                    $("#nav-web").trigger("click");

                    $.ajax({url: host+'/monitoramento/filtrar',
                        type: 'POST',
                        data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                                "expressao": expressao,
                                "dt_inicial": dt_inicial,
                                "dt_final": dt_final,
                                "tipo_data": tipo_data,
                                "fontes": fontes
                        },
                        beforeSend: function() {
                            $('.load-busca').loader('show');
                            $('.cabecalho-aguardando-busca-web').html('<div class="col-md-6"><span class="text-warning">Buscando dados...</span></div>');
                        },
                        success: function(data) {

                            $("#accordion_web .card").remove();

                            if(data.length == 0){

                                $(".cabecalho-busca-web").addClass("d-none");
                                $(".monitoramento-total-web").html(0);
                                $('.cabecalho-aguardando-busca-web').html('<div class="col-md-6"><span class="text-danger">Nenhum dado encontrado para a busca</span></div>');

                            }else{

                                $(".cabecalho-busca-web").removeClass("d-none");
                                $(".cabecalho-aguardando-busca-web").addClass("d-none");

                                $(".monitoramento-total-web").html(data.length);
                                $.each(data, function(k, v) {
                                // $(".resultados").append('<p><a href="'+v.url_noticia+'" target="BLANK">'+v.titulo_noticia+'</a></p>');
                                // $(".resultados").append('<div><p class="fts_detalhes" style="font-weight: 600;" data-chave="card-txt-'+k+'" data-id="'+v.id+'">'+v.titulo_noticia+'</p><div id="txt-'+k+'"></div></div>');

                                const dataObj = new Date(v.data_noticia);
                                const data_formatada = dataObj.toLocaleDateString("pt-BR", {
                                        day: "2-digit",
                                        month: "2-digit",
                                        year: "numeric"
                                    });

                                    $("#accordion_web").append('<div class="card card-plain">'+
                                    '<div class="card-header card-header-custom" role="tab" id="heading1">'+
                                        '<strong>'+v.nome+'</strong>'+
                                        '<a data-toggle="collapse" data-parent="#accordion_web" href="#collapse_'+v.id+'" data-tipo="web" data-chave="card-web-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.titulo_noticia+
                                        '<i class="nc-icon nc-minimal-down"></i>'+
                                        '</a>'+
                                        '<a href="'+v.url_noticia+'" target="BLANK"><i class="fa fa-external-link" aria-hidden="true"></i></a>'+
                                    '</div>'+
                                    '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                        '<div class="box-destaque-busca destaque-card-web-txt-'+k+'"></div><div class="card-body card-busca card-web-txt-'+k+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '</div>');

                                });
                            }                            
                        },
                        error: function(){
                            $("#accordion_web .card").remove();
                            $('.cabecalho-aguardando-busca-web').html('<div class="col-md-6"><span class="text-danger">Erro ao realizar busca</span></div>');
                        },
                        complete: function(){
                            $('.load-busca').loader('hide');
                        }
                    });
                }

                //Busca Impresso
                if(fl_impresso){

                    $("#panel_impresso > .tab-pane").addClass("active");
                    $("#nav-impresso").trigger("click");

                    $.ajax({url: host+'/monitoramento/filtrar/impresso',
                        type: 'POST',
                        data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                                "expressao": expressao,
                                "dt_inicial": dt_inicial,
                                "dt_final": dt_final,
                                "tipo_data": tipo_data,
                                "fontes": fontes
                        },
                        beforeSend: function() {
                            
                        },
                        success: function(data) {

                            $("#accordion_impresso .card").remove();

                            if(data.length == 0){

                                $(".cabecalho-busca-impresso").addClass("d-none");
                                $(".monitoramento-total-impresso").html(0);

                            }else{

                                $(".cabecalho-busca-impresso").removeClass("d-none");
                                

                                $(".monitoramento-total-impresso").html(data.length);
                                $.each(data, function(k, v) {

                                const dataObj = new Date(v.dt_pub);
                                const data_formatada = dataObj.toLocaleDateString("pt-BR", {
                                        day: "2-digit",
                                        month: "2-digit",
                                        year: "numeric"
                                    });

                                    $("#accordion_impresso").append('<div class="card card-plain">'+
                                    '<div class="card-header card-header-custom" role="tab" id="heading1">'+
                                        '<a data-toggle="collapse" data-parent="#accordion_impresso" href="#collapse_'+v.id+'" data-tipo="impresso" data-chave="card-impresso-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.nome+' - Página '+v.n_pagina+
                                        '<i class="nc-icon nc-minimal-down"></i>'+
                                        '</a>'+
                                    '</div>'+
                                    '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                        '<div class="box-destaque-busca destaque-card-impresso-txt-'+k+'"></div><div class="card-body card-busca card-impresso-txt-'+k+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '</div>');

                                });
                            }                            
                        },
                        error: function(){
                            $("#accordion_impresso .card").remove();
                            $(".msg-alerta").html('<span class="text-danger">Erro ao executar expressão de busca</span>');
                        },
                        complete: function(){
                            
                        }
                    });
                }

                   //Busca Rádio
                if(fl_radio){

                    $("#panel_radio > .tab-pane").addClass("active");
                    $("#nav-radio").trigger("click");

                    $.ajax({url: host+'/monitoramento/filtrar/radio',
                        type: 'POST',
                        data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                                "expressao": expressao,
                                "dt_inicial": dt_inicial,
                                "dt_final": dt_final,
                                "tipo_data": tipo_data,
                                "fontes": fontes
                        },
                        beforeSend: function() {
                        
                        },
                        success: function(data) {

                            $("#accordion_radio .card").remove();

                            if(data.length == 0){

                                $(".cabecalho-busca-radio").addClass("d-none");
                                $(".monitoramento-total-radio").html(0);

                            }else{

                                $(".cabecalho-busca-radio").removeClass("d-none");
                                
                                $(".monitoramento-total-radio").html(data.length);
                                $.each(data, function(k, v) {

                                const dataObj = new Date(v.data_hora_inicio);
                                const data_formatada = dataObj.toLocaleDateString("pt-BR", {
                                        day: "2-digit",
                                        month: "2-digit",
                                        year: "numeric"
                                    });

                                    $("#accordion_radio").append('<div class="card card-plain">'+
                                    '<div class="card-header card-header-custom" role="tab" id="heading1">'+
                                        '<a data-toggle="collapse" data-parent="#accordion_radio" href="#collapse_'+v.id+'" data-tipo="radio" data-chave="card-radio-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.nome_emissora+
                                        '<i class="nc-icon nc-minimal-down"></i>'+
                                        '</a>'+
                                    '</div>'+
                                    '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                        '<div class="box-destaque-busca destaque-card-radio-txt-'+k+'"></div><div class="card-body card-busca card-radio-txt-'+k+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '</div>');

                                });
                            }                            
                        },
                        error: function(){
                            $("#accordion_impresso .card").remove();
                            $(".msg-alerta").html('<span class="text-danger">Erro ao executar expressão de busca</span>');
                        },
                        complete: function(){
                        
                        }
                    });
                }

                if(fl_tv){
                    //Busca TV

                    $("#panel_tv > .tab-pane").addClass("active");
                    $("#nav-tv").trigger("click");

                    $.ajax({url: host+'/monitoramento/filtrar/tv',
                        type: 'POST',
                        data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                                "expressao": expressao,
                                "dt_inicial": dt_inicial,
                                "dt_final": dt_final,
                                "tipo_data": tipo_data,
                                "fontes": fontes
                        },
                        beforeSend: function() {
                        
                        },
                        success: function(data) {

                            $("#accordion_tv .card").remove();

                            if(data.length == 0){

                                $(".cabecalho-busca-tv").addClass("d-none");
                                $(".monitoramento-total-tv").html(0);

                            }else{

                                $(".cabecalho-busca-tv").removeClass("d-none");

                                $(".monitoramento-total-tv").html(data.length);
                                $.each(data, function(k, v) {

                                const dataObj = new Date(v.horario_start_gravacao);
                                const data_formatada = dataObj.toLocaleDateString("pt-BR", {
                                        day: "2-digit",
                                        month: "2-digit",
                                        year: "numeric"
                                    });

                                    $("#accordion_tv").append('<div class="card card-plain">'+
                                    '<div class="card-header card-header-custom" role="tab" id="heading1">'+
                                        '<a data-toggle="collapse" data-parent="#accordion_tv" href="#collapse_'+v.id+'" data-tipo="tv" data-chave="card-tv-txt-'+k+'" data-id="'+v.id+'" aria-expanded="false" aria-controls="collapseOne" class="collapsed fts_detalhes"> '+data_formatada+' - '+v.nome_programa+
                                        '<i class="nc-icon nc-minimal-down"></i>'+
                                        '</a>'+
                                    '</div>'+
                                    '<div id="collapse_'+v.id+'" class="collapse" role="tabpanel" aria-labelledby="heading1" style="">'+
                                        '<div class="box-destaque-busca destaque-card-tv-txt-'+k+'"></div><div class="card-body card-busca card-tv-txt-'+k+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '</div>');

                                });
                            }                            
                        },
                        error: function(){
                            $("#accordion_impresso .card").remove();
                            $(".msg-alerta").html('<span class="text-danger">Erro ao executar expressão de busca</span>');
                        },
                        complete: function(){
                            
                        }
                    });
                }
            }
           
        });

        $('body').on('click', '.fts_detalhes', function() {

            var id = $(this).data("id");
            var tipo = $(this).data("tipo");
            var chave = "."+$(this).data("chave");
            var chave_destaque = ".destaque-"+$(this).data("chave");
            var expressao = $("#expressao").val();
            
            $.ajax({url: host+'/monitoramento/filtrar/conteudo',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "expressao": expressao,
                            "id": id,
                            "tipo": tipo
                    },
                    beforeSend: function() {
                        $(chave).loader('show');
                    },
                    success: function(data) {                      

                        $(chave).html(data[0].texto);   
                        $(chave_destaque).empty();    
                        
                        var marks = [];                 
                        
                        const divContent = document.querySelector(chave);

                        if (divContent) {
                        
                            const childElements = divContent.querySelectorAll('mark');
                            const output = document.querySelector(chave_destaque);

                            childElements.forEach(element => {

                                if(!marks.includes(element.innerHTML.trim())){
                                    marks.push(element.innerHTML.trim());

                                    $(chave_destaque).append('<span class="destaque-busca">'+element.innerHTML.trim()+'</span>');
                                }
                            });
                        } 
                    },
                    error: function(){
                        $(".msg-alerta").html('<span class="text-danger">Erro ao buscar conteúdo</span>');
                    },
                    complete: function(){
                        $(chave).loader('hide');
                    }
            });

        });

    });

    
</script>
@endsection