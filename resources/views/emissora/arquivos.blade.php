@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-volume-up"></i> Rádio
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Coletas
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
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia/radio/coletas']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>                        
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Emissora <span class="text-danger">Obrigatório</span></label>
                                        <select class="form-control select2" name="emissora" id="emissora">
                                            <option value="">Selecione uma emissora</option>
                                            @foreach ($emissoras as $emissora)
                                                <option value="{{ $emissora->id }}" {!! ($emissora_search == $emissora->id) ? "selected" : '' !!}>
                                                    {{ $emissora->nome_emissora }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Programa</label>
                                        <select class="form-control selector-select2" name="programa" id="programa" disabled>
                                            <option value="">Selecione um programa</option>
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
                                <div class="col-md-12">
                                    <div class="form-check float-left mr-3">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" {{ ($fl_audios) ? 'checked' : '' }} name="fl_audios" value="true">
                                                MOSTRAR ÁUDIOS
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                                @if($erro)
                                    <div class="col-md-12 col-sm-12">
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Erro!</strong> {{ $erro }}
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}

                    @if(count($dados) > 0)
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} Páginas</h6>

                        {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                            'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                            'emissora' => $emissora_search,
                                                            'programa' => $programa_search,
                                                            'expressao' => $expressao])
                                                            ->links('vendor.pagination.bootstrap-4') }}
                    @endif

                    @foreach ($dados as $key => $audio)
                        <div class="card card-audio" id="card-audio-{{ $audio->id }}">
                            <div class="card-body">                    
                                <div class="row">
                                    @if($fl_audios)
                                        <div class="col-lg-12 col-md-12 col-sm-12 mb-1">                                    
                                            @if(Storage::disk('s3')->temporaryUrl($audio->path_s3, '+30 minutes'))
                                                <audio width="100%" controls style="width: 100%;">
                                                    <source src="{{ Storage::disk('s3')->temporaryUrl($audio->path_s3, '+30 minutes') }}" type="audio/mpeg">
                                                    Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.
                                                </audio>
                                            @else
        
                                            @endif
                                        </div>
                                    @endif
                                    <div class="col-lg-12 col-sm-12 mb-1"> 
                                        <h6><a href="{{ url('emissora/'.$audio->id_fonte.'/edit') }}" target="_BLANK">{{ ($audio->nome_fonte) ? $audio->nome_fonte : '' }}</a></h6>  
                                        <h6 style="color: #FF5722;">{{ ($audio->nm_estado) ? $audio->nm_estado : '' }}{{ ($audio->nm_cidade) ? "/".$audio->nm_cidade : '' }}</h6>  
                                        <h6 class="text-muted mb-1">
                                            {{ ($audio->nome_fonte) ? $audio->nome_fonte : '' }} - 
                                            {{ \Carbon\Carbon::parse($audio->data_hora_inicio)->format('d/m/Y') }} - 
                                            De {{ \Carbon\Carbon::parse($audio->data_hora_inicio)->format('H:i:s') }} às {{ \Carbon\Carbon::parse($audio->data_hora_fim)->format('H:i:s') }}
                                        </h6> 
                                        <p class="mb-2"><strong>Retorno de Mídia</strong>: {!! ($audio->valor_retorno) ? "R$ ".$audio->valor_retorno : '<span class="text-danger">Não calculado</span>' !!}</p>
                                        <div class="panel panel-success">
                                            <div class="conteudo-noticia mb-1">
                                                <span class="transcricao-limitada" id="transcricao-limitada-{{ $audio->id }}">
                                                    {!! ($audio->transcricao) ? Str::limit($audio->transcricao, 1000, " ...") : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                                    @if($audio->transcricao && strlen($audio->transcricao) > 1000)
                                                        <a href="javascript:void(0);" class="text-primary ver-mais" data-id="{{ $audio->id }}">[ver mais]</a>
                                                    @endif
                                                </span>
                                                <span class="transcricao-completa d-none" id="transcricao-completa-{{ $audio->id }}">
                                                    {!! $audio->transcricao !!}
                                                    <a href="javascript:void(0);" class="text-primary ver-menos" data-id="{{ $audio->id }}">[ver menos]</a>
                                                </span>
                                            </div>
                                            <div class="panel-body conteudo-{{ $audio->id }}">
                                                {!! ($audio->transcricao) ?  $audio->transcricao  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>                                            
                                        </div>               
                                    </div>
                                </div>     

                            </div>
                        </div>
                    @endforeach
                    @if(count($dados) > 0)
                        {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                                'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                                'emissora' => $emissora_search,
                                                                'programa' => $programa_search,
                                                                'expressao' => $expressao])
                                                                ->links('vendor.pagination.bootstrap-4') }}
                    @endif
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
                        <button type="button" class="btn btn-primary mb-3 pull-left" id="selecionarTodos">
                            Selecionar Filtrados
                            <span id="spinnerSelecionarTodos" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>
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
                    <button type="button" class="btn btn-primary" id="btn-selecionar"><i class="fa fa-check"></i> Finalizar Seleção</button>
                </div>
          </div>
        </div>
      </div>
    </div>
@endsection
@section('script')    
    <script>
      
        $(document).ready(function(){ 

            var host =  $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');

            // Destacar card ao clicar
            $('.card-audio').click(function(){
                // Remove destaque de todos
                $('.card-audio').removeClass('card-destaque');
                // Adiciona destaque ao clicado
                $(this).addClass('card-destaque');
            });

            var demo2 = $('.demo1').bootstrapDualListbox({
                nonSelectedListLabel: 'Disponíveis',
                selectedListLabel: 'Selecionadas',               
            });

            $('.ver-mais').click(function(){
                var id = $(this).data('id');
                $('#transcricao-limitada-' + id).addClass('d-none');
                $('#transcricao-completa-' + id).removeClass('d-none');
            });

            $('.ver-menos').click(function(){
                var id = $(this).data('id');
                $('#transcricao-completa-' + id).addClass('d-none');
                $('#transcricao-limitada-' + id).removeClass('d-none');
                // Rolagem suave para o início do texto limitado
                $('html, body').animate({
                    scrollTop: $('#transcricao-limitada-' + id).offset().top - 450 // ajuste o -100 conforme seu layout
                }, 400);
            });   

            let emissoras = [];
            let selecionadas = [];

            carregarEmissoras();
            
            async function carregarEmissoras() {

                const id_monitoramento = 0;

                try {

                    let tipo_midia = "radio";

                    // Mostrar o spinner de carregamento
                    document.getElementById('loadingSpinner').style.display = 'inline-block';

                    const response = await fetch(host+'/monitoramento/'+tipo_midia+'/emissoras/'+id_monitoramento);
                    emissoras = await response.json();

                    // Adiciona os registros com fl_filtro = true à variável selecionados
                    emissoras.forEach(e => {
                        if (e.fl_filtro === true && !selecionadas.includes(e.id)) {
                            selecionadas.push(e.id);
                        }
                    });

                    document.getElementById('selecionadas').value = selecionadas;

                    atualizarSelecionadasTexto();
                    carregarTabela();

                }catch (error) {
                    console.error('Erro ao carregar emissoras:', error);
                }finally {
                                    
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

                if (e.fl_filtro === true && !selecionadas.includes(e.id)) {
                    selecionadas.push(e.id);
                }

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
            // Mostrar o spinner de carregamento
            document.getElementById('spinnerSelecionarTodos').style.display = 'inline-block';

            let checkboxes = document.querySelectorAll('.checkbox-emissora');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });

            // Ocultar o spinner de carregamento após a operação
            document.getElementById('spinnerSelecionarTodos').style.display = 'none';
        });

        document.getElementById('limparSelecao').addEventListener('click', function() {
            
            // Limpar o array selecionadas
            selecionadas = [];

            // Desmarcar todos os checkboxes na tabela
            let checkboxes = document.querySelectorAll('.checkbox-emissora');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // Atualizar o texto de fontes selecionadas
            atualizarSelecionadasTexto();
        });

        document.getElementById('btn-selecionar').addEventListener('click', function() {

            document.getElementById('selecionadas').value = selecionadas;
            $('#modalFontes').modal('hide');
            
        });

        document.getElementById('filtro_uf').addEventListener('change', carregarTabela);
        document.getElementById('filtro_nome').addEventListener('input', carregarTabela);
        
        document.addEventListener('DOMContentLoaded', () => {
            carregarUFs();
            carregarEmissoras();
        });
        });
    </script>
@endsection