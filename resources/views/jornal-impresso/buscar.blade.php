@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Buscar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-database"></i> Fontes Impressos</a>
                    <a href="{{ url('jornal-impresso/upload') }}" class="btn btn-warning pull-right mr-3"><i class="fa fa-upload"></i> Upload</a>
                    <a href="{{ url('noticia/impresso/cadastrar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['jornal-impresso/buscar']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo de Data</label>
                                        <select class="form-control select2" name="tipo_data" id="tipo_data">
                                            <option value="created_at">Data de Cadastro</option>
                                            <option value="created_at">Data do Clipping</option>
                                        </select>
                                    </div>
                                </div>
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
                            </div>
                            <div class="row">

                                <div class="col-md-12 col-sm-12 mt-1">
                                    <p class="mb-0">
                                        <i class="fa fa-filter fa-1x"></i> Filtrar Fontes
                                        <button type="button" class="btn btn-sm btn-primary btn-icon btn-email" style="border-radius: 50%; height: 1.5rem;
                                        min-width: 1.5rem;
                                        width: 1.5rem;" data-toggle="modal" data-target="#modalFontes"><i class="fa fa-check fa-2x"></i></button>
                                    </p>
                                    <p id="selecionadasTexto" class="mt-1">Fontes selecionadas: <span id="loadingSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></p>
                                    <input type="hidden" name="selecionadas[]" id="selecionadas">
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

                    @if(count($impressos))
                        <h6 class="px-3">Mostrando {{ $impressos->count() }} de {{ $impressos->total() }} arquivos coletados</h6>
                        {{ $impressos->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }} 
                    @endif  

                    @foreach ($impressos as $key => $pagina)
                        <div class="card">
                            <div class="card-body">                           
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-12 mb-1">
                                        <a href="{{ url('jornal-impresso/web/pagina/download/'.$pagina->id) }}">
                                            <img class="lazy-load" data-src="{{ Storage::disk('s3')->temporaryUrl($pagina->path_pagina_s3, '+2 minutes') }}" alt="Página {{ $pagina->n_pagina }}">
                                        </a>
                                    </div>
                                    <div class="col-lg-10 col-sm-10 mb-1"> 
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 mb-1"> 

                                                <h6><a href="{{ url('fonte-impresso/'.$pagina->edicao->id_jornal_online.'/editar') }}" target="_BLANK">{{ ($pagina->edicao->fonte) ? $pagina->edicao->fonte->nome : 'Não identificada' }}</a></h6>  
                                                <h6 style="color: #FF5722;">
                                                    {{ ($pagina->edicao->fonte and $pagina->edicao->fonte->estado) ? $pagina->edicao->fonte->estado->nm_estado : '' }}
                                                    {{ ($pagina->edicao->fonte and $pagina->edicao->fonte->cidade) ? '/ '.$pagina->edicao->fonte->cidade->nm_cidade : '' }}
                                                </h6>  
                                                <h6 class="text-muted mb-1">{{ \Carbon\Carbon::parse($pagina->dt_pub)->format('d/m/Y') }} - {{ ($pagina->edicao->fonte) ? $pagina->edicao->fonte->nome : 'Não identificada' }}</h6> 


                                                <h6 class="conteudo-fonte-{{ $pagina->id }}">{{ ($pagina->edicao->fonte) ? $pagina->edicao->fonte->nome : 'Não identificada' }} - {{ \Carbon\Carbon::parse($pagina->dt_clipagem)->format('d/m/Y') }}</h6>
                                                <h6 class="text-muted conteudo-estado-{{ $pagina->id }}">
                                                    {{ ($pagina->edicao->fonte and $pagina->edicao->fonte->estado) ? $pagina->edicao->fonte->estado->nm_estado : '' }}
                                                    {{ ($pagina->edicao->fonte and $pagina->edicao->fonte->cidade) ? '/ '.$pagina->edicao->fonte->cidade->nm_cidade : '' }}
                                                </h6>
                                                <p class="paginas-{{ $pagina->id }}">Página <strong>{{ $pagina->n_pagina }}</strong>/<strong>{{ count($pagina->edicao->paginas) }}</strong></p>  
                                                <div class="panel panel-success">
                                                    <div class="conteudo-noticia mb-1">
                                                        {!! ($pagina->texto_extraido) ?  Str::limit($pagina->texto_extraido, 800, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                                    </div>
                                                    <div class="panel-body conteudo-{{ $pagina->id }}">
                                                        {!! ($pagina->texto_extraido) ?  $pagina->texto_extraido  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                                    </div>
                                                </div>        
                                            </div>  
                                            <div class="col-lg-12 col-md-12 col-sm-12 mb-1 rodape-noticia"> 
                                                <button class="btn btn-primary btn-visualizar-noticia" data-id="{{ $pagina->id }}"><i class="fa fa-eye"></i> Visualizar</button> 
                                                <a href="{{ url('jornal-impresso/noticia/extrair/web',$pagina->id) }}" class="btn btn-success btn-extrair-noticia"><i class="fa fa-database"></i> Extrair Notícia</a>  
                                            </div>
                                        </div>    
                                    </div>
                                </div>                               
                            </div>                            
                        </div>
                    @endforeach
                    @if(count($impressos))
                        {{ $impressos->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }} 
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade" id="showNoticia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="padding: 15px !important;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-newspaper-o"></i><span></span> Dodos da Notícia</h6>
        </div>
        <div class="modal-body" style="padding: 15px;">
            <div class="row">
                <div class="col-md-12 modal-cabecalho">
                    <h6 class="modal-fonte mt-0 mb-1"></h6>
                    <h6 class="text-muted modal-estado mt-0 mb-1"></h6>
                    <p class="modal-pagina mt-0 mb-2"></p>
                </div>
                <hr/>
                <div class="col-md-12 modal-conteudo">
                    
                </div>
            </div>
            <div class="center">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
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
        var host =  $('meta[name="base-url"]').attr('content');
        var token = $('meta[name="csrf-token"]').attr('content');
        
        let emissoras = [];
        let selecionadas = [];

        document.querySelectorAll(".form-check-input").forEach(function(checkbox) {
            checkbox.addEventListener("click", function() {
                document.querySelectorAll(".form-check-input").forEach(function(cb) {
                    cb.checked = false;
                });
                checkbox.checked = true;
                carregarEmissoras();
            });
        });

        async function carregarEmissoras() {

            try {

                let tipo_midia = "impresso";


                // Mostrar o spinner de carregamento
                document.getElementById('loadingSpinner').style.display = 'inline-block';

                const response = await fetch(host+'/jornal-impresso/emissoras');
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

            } catch (error) {
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
        
</script>
    <script>

        document.addEventListener("DOMContentLoaded", function() {
            var lazyImages = [].slice.call(document.querySelectorAll("img.lazy-load"));

            if ("IntersectionObserver" in window) {
                let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            let lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.classList.remove("lazy-load");
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            } else {
                // Fallback for browsers that do not support IntersectionObserver
                lazyImages.forEach(function(lazyImage) {
                    lazyImage.src = lazyImage.dataset.src;
                });
            }
        });

        $(document).ready(function(){

            var host =  $('meta[name="base-url"]').attr('content');

            var demo2 = $('.demo1').bootstrapDualListbox({
                nonSelectedListLabel: 'Disponíveis',
                selectedListLabel: 'Selecionadas',
            
            });

            $(".btn-visualizar-noticia").click(function(){

                var id = $(this).data("id");
                var chave = ".conteudo-"+id;
                var pagina = ".paginas-"+id;
                var estado = ".conteudo-estado-"+id;
                var fonte = ".conteudo-fonte-"+id;

                $(".modal-fonte").html($(fonte).text());
                $(".modal-estado").html($(estado).text());
                $(".modal-pagina").html($(pagina).text());
                $(".modal-conteudo").html($(chave).text().replace(/\n/g, "<br />"));

                $("#showNoticia").modal("show");

            });
        
        });
    </script>
@endsection