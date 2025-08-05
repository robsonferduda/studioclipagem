@extends('layouts.app')
@section('style')
<style>
    .top-40 {
        margin-top: 40px!important;
    }
</style>
@endsection
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_cliente_edit', 'url' => ['cliente', $cliente->id], 'method' => 'patch', 'files' => true]) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-3">
                            <i class="nc-icon nc-briefcase-24"></i> Clientes
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Editar
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('cliente') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Clientes</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mr-1 ml-1">
                    <div class="col-md-12">
                        @include('layouts.mensagens')
                    </div>
                    <div class="col-md-12">
                        <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $cliente->id }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nome <span class="text-danger">Obrigatório</span></label>
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required value="{{ $cliente->nome }}">
                                </div>
                            </div>
                            <div class="col-md-12">

                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_ativo) ? 'checked' : '' }} type="checkbox" name="fl_ativo" value="true">
                                            ATIVO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>

                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_print) ? 'checked' : '' }} type="checkbox" name="fl_print" value="true">
                                            NOTÍCIAS COM PRINT
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>

                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_sentimento) ? 'checked' : '' }} type="checkbox" name="fl_sentimento" value="true">
                                            MOSTRAR SENTIMENTO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>   

                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_retorno_midia) ? 'checked' : '' }} type="checkbox" name="fl_retorno_midia" value="true">
                                            RETORNO DE MÍDIA
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>  

                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_texto_logo) ? 'checked' : '' }} type="checkbox" name="fl_texto_logo" value="true">
                                            TEXTO LOGO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>                         
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <p class="mb-0 mt-3"><i class="nc-icon nc-sound-wave"></i> Clipagem de Mídia</p>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_impresso) ? 'checked' : '' }} type="checkbox" name="fl_impresso" value="true">
                                        IMPRESSO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_web) ? 'checked' : '' }} type="checkbox" name="fl_web" value="true">
                                        WEB
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_radio) ? 'checked' : '' }} type="checkbox" name="fl_radio" value="true">
                                        RÁDIO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_tv) ? 'checked' : '' }} type="checkbox" name="fl_tv" value="true">
                                        TV
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <p class="mb-0 mt-3"> <i class="fa fa-file-pdf-o"></i> Relatórios</p>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_relatorio_completo) ? 'checked' : '' }} type="checkbox" name="fl_relatorio_completo" value="true">
                                            RELATÓRIO COMPLETO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_relatorio_consolidado) ? 'checked' : '' }} type="checkbox" name="fl_relatorio_consolidado" value="true">
                                            RELATÓRIO CONSOLIDADO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_link_relatorio) ? 'checked' : '' }} type="checkbox" name="fl_link_relatorio" value="true">
                                            RELATÓRIO COM LINKS
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <p class="mb-0 mt-3"><i class="fa fa-envelope"></i> Endereços Eletrônicos</p>
                                <div class="form-group">
                                    <textarea class="form-control" name="emails" id="emails" rows="3">{{ $cliente->emails }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <p class="mb-0">
                            <i class="fa fa-lock fa-1x"></i> Usuários
                            <button type="button" class="btn btn-sm btn-primary btn-icon btn-add-usuario" style="border-radius: 50%; height: 1.5rem;
                            min-width: 1.5rem;
                            width: 1.5rem;"><i class="fa fa-plus fa-2x"></i></button>
                        </p>
                        <div class="row mt-o">
                            <div class="col-md-12">
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ ($cliente->fl_area_restrita) ? 'checked' : '' }} type="checkbox" name="fl_area_restrita" value="true">
                                            ÁREA RESTRITA
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @if(count($cliente->usuarios))
                                    @foreach($cliente->usuarios as $usuario)                                        
                                        <span 
                                            data-id="{{ $usuario->id }}" 
                                            class="btn-usuario">{{ $usuario->email }}
                                            <a title="Remover" href="{{ url('usuarios/excluir', $usuario->id) }}" data-id="{{ $usuario->id }}" class="btn-excluir">
                                                <i class="fa fa-trash fa-2x text-danger ml-1 mr-2"></i>
                                            </a>
                                            <a title="Editar" data-id="{{ $usuario->id }}" data-usuario="{{ $usuario->email }}" class="btn-editar btn-editar-usuario">
                                                <i class="fa fa-edit fa-2x text-info ml-1 mr-3"></i>
                                            </a>
                                        </span>
                                    @endforeach
                                @else
                                    <p class="text-danger">Nenhum usuário cadastrado</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr/>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label>Logo</label>
                            <input type="file" class="form-control" name="logo" id="logo">
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    @if($cliente->logo)
                                        <img src="{{ asset('img/clientes/logo/'.$cliente->logo) }}" alt="{{ $cliente->logo }}" class="img-thumbnail">
                                    @else
                                        <span class="text-danger">Nenhuma mídia cadastrada</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label>Logo Expandida </label>
                            <input type="file" class="form-control" name="logo_expandida" id="logo_expandida">
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    @if($cliente->logo_expandida)
                                        <img src="{{ asset('img/clientes/logo_expandida/'.$cliente->logo_expandida) }}" alt="{{ $cliente->logo_expandida }}" class="img-thumbnail">
                                    @else
                                        <span class="text-danger">Nenhuma mídia cadastrada</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-4">
                        <p class="mb-1"><i class="fa fa-tags"></i> Áreas do Cliente</p>
                        {!! Form::open(['id' => 'frm_area_edit', 'url' => ['cliente', $cliente->id], 'method' => 'patch', 'files' => true]) !!}
                            <div class="row">
                                <input type="hidden" name="id_cliente_area" id="id_cliente_area">

                                <div class="col-md-3">
                                    <label>Área</label>
                                    <select class="form-control" name="area" id="area">
                                        <option value="">Selecione</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label>Situação</label>
                                    <select class="form-control" name="situacao" id="situacao">
                                        <option value="">Selecione</option>
                                        <option value="true">Ativo</option>
                                        <option value="false">Inativo</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Expressão</label>
                                    <input type="text" class="form-control" name="expressao" id="expressao">
                                </div>

                                <div class="col-md-1">
                                    <label>Ordem</label>
                                    <input type="number" class="form-control" name="ordem" id="ordem" min="1" step="1">
                                </div>

                                <div class="col-md-2 mt-3">                                    
                                    <button type="button" class="btn btn-success btn-sm btn-block btn-add-area"><i class="fa fa-save"></i> Salvar</button>
                                </div>
                            </div>
                        {!! Form::close() !!}

                        <table class="table table-bordered table-striped mt-3">
                            <thead class="text-primary">
                                <tr>
                                    <th class="center">Ordem</th>
                                    <th>Área</th>
                                    <th>Expressão</th>
                                    <th class="center">Situação</th>
                                    <th class="center">Ordem</th>
                                    <th class="center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-areas">
                                @forelse ($cliente->areas->sortBy('ordem') as $index => $area_cliente)
                                    <tr data-id="{{ $area_cliente->id }}">
                                        <td class="handle center" style="cursor: move;">☰</td>
                                        <td>{{ $area_cliente->area->descricao }}</td>
                                        <td>{{ $area_cliente->expressao }}</td>
                                        <td class="center">
                                            <a href="javascript:void(0);" 
                                               class="btn-alternar-situacao" 
                                               data-id="{{ $area_cliente->id }}" 
                                               data-ativo="{{ $area_cliente->ativo }}">
                                               {!! $area_cliente->ativo 
                                                    ? '<span class="badge badge-pill badge-success">ATIVO</span>' 
                                                    : '<span class="badge badge-pill badge-danger">INATIVO</span>' !!}
                                            </a>
                                        </td>
                                        <td class="ordem-numero center">{{ $area_cliente->ordem ?? '-' }}</td>
                                        <td class="td-actions center">
                                            <button type="button" class="btn btn-link btn-icon text-info btn-editar-area" 
                                                title="Editar"
                                                data-id="{{ $area_cliente->id }}"
                                                data-area="{{ $area_cliente->area_id }}"
                                                data-situacao="{{ $area_cliente->ativo ? 'true' : 'false' }}"
                                                data-expressao="{{ $area_cliente->expressao }}"
                                                data-ordem="{{ $area_cliente->ordem }}">
                                                <i class="fa fa-edit fa-lg text-info"></i>
                                            </button>
                                            <a href="{{ url('cliente/area/'.$area_cliente->id.'/remover') }}" class="btn btn-link btn-icon text-danger btn-excluir" title="Excluir">
                                                <i class="fa fa-trash fa-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">Nenhuma área vinculada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

               
           
            </div>
            <div class="card-footer text-center mb-3">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('cliente') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}    
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-envelope"></i> Adicionar Endereço Eletrônico</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Digite o endereço</label>
                            <input type="mail" class="form-control" name="email" id="email">
                        </div>
                    </div>
                </div>
                <div class="center">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar-email"><i class="fa fa-save"></i> Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addUsuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-user"></i> Adicionar Usuário</h6>
        </div>
        <div class="modal-body">
           
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="id_usuario" id="id_usuario" value="">
                            <input type="hidden" class="form-control" name="nome_usuario" id="nome_usuario" value="{{ $cliente->nome }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Usuário <span class="text-info">Email ou usuário</span></label>
                            <input type="text" class="form-control" name="usuario" id="usuario" required>
                        </div>
                    </div>
                    <div class="col-md-12 box-senha">
                        <div class="form-group">
                            <div class="form-check mt-1">
                                <label class="form-check-label mt-2">
                                    <input class="form-check-input" type="checkbox" name="alterar_senha" id="alterar_senha" value="true">
                                    ALTERAR SENHA
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Senha</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-key-25"></i>
                                    </span>
                                </div>
                                <input id="password" type="password" class="form-control" name="password" id="password" required autocomplete="current-password">
                                <div class="view-eye">
                                    <i class="fa fa-eye view-password" data-target="password"></i>  
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check mt-1">
                                <label class="form-check-label mt-2">
                                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="true">
                                    ATIVO
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="center">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar-usuario-modal"><i class="fa fa-save"></i> Salvar</button>
                </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>

    $(function () {
        $("#sortable-areas").sortable({
            handle: '.handle',
            helper: fixWidthHelper,
            update: function () {
                let ordem = [];
                let host =  $('meta[name="base-url"]').attr('content');
                let cliente = $("#cliente_id").val();

                $('#sortable-areas tr').each(function (index) {
                    const id = $(this).data('id');
                    ordem.push({
                        id: id,
                        ordem: index + 1
                    });

                    // Atualiza o número da ordem no TD correspondente
                    $(this).find('td.ordem-numero').text(index + 1);
                });

                // Envia a ordem atualizada via AJAX
                $.ajax({
                    url: host+'/cliente/'+cliente+'/areas/reordenar',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ordem: ordem
                    },
                    success: function () {
                        // Feedback opcional
                        showToast("Ordem salva com sucesso!");
                    }
                });
            }
        }).disableSelection();

        // Mantém largura das colunas ao arrastar
        function fixWidthHelper(e, ui) {
            ui.children().each(function () {
                $(this).width($(this).width());
            });
            return ui;
        }

        // Função para mostrar notificação simples
        function showToast(msg) {
            let toast = $('<div class="alert alert-success" style="position:fixed; top:20px; right:20px; z-index:9999;">' + msg + '</div>');
            $('body').append(toast);
            setTimeout(() => toast.fadeOut(300, () => toast.remove()), 1500);
        }
    });


       

        $(document).ready(function() {
            
            let host =  $('meta[name="base-url"]').attr('content');

            $(document).on('click', '.btn-alternar-situacao', function () {
                const $el = $(this);
                const id = $el.data('id');

                $.ajax({
                    url: host+`/cliente/area/${id}/toggle-situacao`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            // Atualiza o badge HTML
                            $el.html(response.badge);

                            // Atualiza o data-ativo
                            $el.data('ativo', response.ativo);

                            // Feedback suave
                            showToast('Situação atualizada.');
                        }
                    },
                    error: function () {
                        alert('Erro ao atualizar situação.');
                    }
                });
            });

            function showToast(msg) {
                let toast = $('<div class="alert alert-success" style="position:fixed; top:20px; right:20px; z-index:9999;">' + msg + '</div>');
                $('body').append(toast);
                setTimeout(() => toast.fadeOut(300, () => toast.remove()), 1500);
            }

            $(document).on('click', '.btn-editar-area', function () {
                $('#id_cliente_area').val($(this).data('id'));
                $('#area').val($(this).data('area'));
                $('#situacao').val(String($(this).data('situacao')));
                $('#expressao').val($(this).data('expressao'));
                $('#ordem').val($(this).data('ordem'));

                // Scrola até o formulário (opcional)
                $('html, body').animate({
                    scrollTop: $('#frm_area_edit').offset().top - 50
                }, 300);
            });

            $(".btn-salvar-usuario-modal").click(function(){

                var cliente = $("#cliente_id").val();
                var usuario = $("#usuario").val();
                var nome = $("#nome_usuario").val();
                var senha = $("#password").val();
                var ativo = $("#ativo").is(":checked");
                var fl_senha = $("#alterar_senha").is(":checked");
                var id = $("#id_usuario").val();

                if(!usuario){

                    Swal.fire({
                        html: 'Informe um usuário.',
                        type: "warning",
                        icon: "warning",
                        confirmButtonText: '<i class="fa fa-check"></i> Entendi',
                    });

                }else{

                    $.ajax({
                        url: host+'/usuario/cliente/cadastrar',
                        type: 'POST',
                        data: {
                                "_token": $('meta[name="csrf-token"]').attr('content'),
                                "usuario": usuario,
                                "nome": nome,
                                "senha": senha,
                                "fl_senha": fl_senha,
                                "ativo": ativo,
                                "id": id,
                                "cliente": cliente
                        },
                        success: function(response) {
                            location.reload();                    
                        },
                        error: function(response){
                            
                        }
                    });
                }
            });

            $(".btn-add-usuario").click(function(){
                $("#id_usuario").val(0);
                $(".box-senha").css('display','none');
                $("#addUsuario").modal('show');
            });

            $(".btn-editar-usuario").click(function(){

                var id = $(this).data("id");
                var usuario = $(this).data("usuario");

                $("#id_usuario").val(id);
                $("#usuario").val(usuario);

                if(ativo){
                    $("#ativo").prop('checked', true);
                }

                $(".box-senha").css('display','block');

                $("#addUsuario").modal('show');

            });

            $(".btn-add-area").click(function(){

                var id = $("#id_cliente_area").val();
                var area = $("#area").val();
                var ordem = $("#ordem").val();
                var situacao = $("#situacao").val();
                var expressao = $("#expressao").val();
                var cliente = $("#cliente_id").val();

                if(!area || !situacao){

                    Swal.fire({
                        html: 'Para cadastrar uma área, o campo <strong>Áreas</strong> e <strong>Situação</strong> é obrigatório.',
                        type: "warning",
                        icon: "warning",
                        confirmButtonText: '<i class="fa fa-check"></i> Entendi',
                    });

                }else{

                    $.ajax({
                        url: host+'/areas/cliente/cadastrar',
                        type: 'POST',
                        data: {
                                "_token": $('meta[name="csrf-token"]').attr('content'),
                                "area": area,
                                "id": id,
                                "situacao": situacao,
                                "expressao": expressao,
                                "ordem": ordem,
                                "cliente": cliente
                        },
                        success: function(response) {
                            location.reload();                       
                        },
                        error: function(response){
                            
                        }
                    });

                }

            });

            $(".btn-editar-area").click(function(){

                var id = $(this).data("id");
                var area = $(this).data("area");
                var situacao = ($(this).data("situacao")) ? "true" : "false";
                var expressao = $(this).data("expressao");

                $("#area").val(area);
                $("#situacao").val(situacao);
                $("#expressao").val(expressao);
                $("#id_cliente_area").val(id);

            });

        });
    </script>
@endsection