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
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.mensagens')
                    </div>
                </div>
                <div class="row mr-1 ml-1">
                    <div class="col-md-12">
                        <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $cliente->id }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nome <span class="text-danger">Obrigatório</span></label>
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required value="{{ $cliente->nome }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" {{ ($cliente->fl_print) ? 'checked' : '' }} type="checkbox" name="fl_print" value="true">
                                            NOTÍCIAS COM PRINT
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" {{ ($cliente->ativo) ? 'checked' : '' }} type="checkbox" name="ativo" value="true">
                                            ATIVO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
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
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p class="mb-0">
                            <i class="fa fa-envelope"></i> Endereços Eletrônicos 
                            <button type="button" class="btn btn-sm btn-primary btn-icon btn-email" style="border-radius: 50%; height: 1.5rem;
                            min-width: 1.5rem;
                            width: 1.5rem;" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus fa-2x"></i></button>
                        </p>
                        <div class="row">
                            <div class="col-md-12">
                                @if(count($emails))
                                    @foreach($emails as $email)                                        
                                        <span data-id="{{ $email->id }}" class="btn-excluir-email">{{ $email->endereco }}<a title="Remover" class=""><i class="fa fa-trash text-danger ml-1 mr-3"></i></a></span>
                                    @endforeach
                                @else
                                    <p class="text-danger">Nenhum endereço eletrônico cadastrado</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p class="mb-0">
                            <i class="fa fa-lock fa-1x"></i> Usuários
                            <button type="button" class="btn btn-sm btn-primary btn-icon btn-email" style="border-radius: 50%; height: 1.5rem;
                            min-width: 1.5rem;
                            width: 1.5rem;" data-toggle="modal" data-target="#addUsuario"><i class="fa fa-plus fa-2x"></i></button>
                        </p>
                        <div class="row">
                            <div class="col-md-12">
                                @if(count($emails))
                                    @foreach($emails as $email)                                        
                                        <span data-id="{{ $email->id }}" class="btn-usuario">{{ $email->endereco }}<a title="Remover" href="{{ url('usuarios/excluir', $email->id) }}" class="btn-excluir"><i class="fa fa-trash text-danger ml-1 mr-3"></i></a></span>
                                    @endforeach
                                @else
                                    <p class="text-danger">Nenhum endereço eletrônico cadastrado</p>
                                @endif
                            </div>
                        </div>
                    </div>
                <div class="col-md-12 mt-4">
                    <p><i class="fa fa-tags"></i> Áreas do Cliente</p>
                    {!! Form::open(['id' => 'frm_cliente_edit', 'url' => ['cliente', $cliente->id], 'method' => 'patch', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Áreas</label>
                                        <select class="form-control select2" name="area" id="area">
                                            <option value="">Selecione</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}" >{{ $area->descricao }}</option>
                                        @endforeach
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Situação</label>
                                        <select class="form-control" name="situacao" id="situacao">
                                            <option value="">Selecione</option>
                                            <option value="true">Ativo</option>
                                            <option value="false">Inativo</option>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label>Expressão</label>
                                    <input type="text" class="form-control" name="expressao" id="expressao">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mt-3">
                                    <button type="button" class="btn btn-success btn-add-area"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                    <div class="row">
                        @foreach ($cliente->areas as $area_cliente)
                            <div class="col-lg-12 col-sm-12">
                                <div class="card">
                                    <div class="card-content px-3">
                                        <div class="row">
                                        
                                            <div class="col-lg-9 col-md-9 col-sm-12">
                                                <p><strong>{{ $area_cliente->area->descricao }}</strong></p>
                                                <p>{{ $area_cliente->expressao }}</p>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                <div class="pull-right">
                                                    @if($area_cliente->ativo)                                                        
                                                        <span class="badge badge-success">Ativo</span>
                                                    @else
                                                        <span class="badge badge-danger">Inativo</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                        <label>Nome</label>
                        <input type="mail" class="form-control" name="email" id="email">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="mail" class="form-control" name="email" id="email">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" class="form-control" name="password" id="password">
                        <div class="view-eye">
                            <i class="fa fa-eye view-password" data-target="password"></i>  
                        </div> 
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="form-check mt-1">
                            <label class="form-check-label mt-2">
                                <input class="form-check-input" {{ ($cliente->ativo) ? 'checked' : '' }} type="checkbox" name="ativo" value="true">
                                ATIVO
                                <span class="form-check-sign"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="center">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                <button type="button" class="btn btn-success btn-salvar-usuario"><i class="fa fa-save"></i> Salvar</button>
            </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
    <script>
       

        $(document).ready(function() {
            
            let host =  $('meta[name="base-url"]').attr('content');

            $(".btn-salvar-usuario").click(function(){

                var cliente = $("#cliente_id").val();
                var email = $("#email").val();

                if(!email){

                    Swal.fire({
                        html: 'Informe um email válido.',
                        type: "warning",
                        icon: "warning",
                        confirmButtonText: '<i class="fa fa-check"></i> Entendi',
                    });

                }else{

                    $.ajax({
                        url: host+'/email/cliente/cadastrar',
                        type: 'POST',
                        data: {
                                "_token": $('meta[name="csrf-token"]').attr('content'),
                                "email": email,
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

            $(".btn-salvar-email").click(function(){

                var cliente = $("#cliente_id").val();
                var email = $("#email").val();

                if(!email){

                    Swal.fire({
                        html: 'Informe um email válido.',
                        type: "warning",
                        icon: "warning",
                        confirmButtonText: '<i class="fa fa-check"></i> Entendi',
                    });

                }else{

                    $.ajax({
                        url: host+'/email/cliente/cadastrar',
                        type: 'POST',
                        data: {
                                "_token": $('meta[name="csrf-token"]').attr('content'),
                                "email": email,
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

            $(".btn-add-area").click(function(){

                var area = $("#area").val();
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
                                "situacao": situacao,
                                "expressao": expressao,
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

            let options = {
                onKeyPress: function (cpf, ev, el, op) {
                    let masks = ['000.000.000-000', '00.000.000/0000-00'];
                    $('#cpf_cnpj').mask((cpf.length > 14) ? masks[1] : masks[0], op);
                }
            }

            $('#cpf_cnpj').val().length > 13 ?
                $('#cpf_cnpj').mask('00.000.000/0000-00', options) :
                $('#cpf_cnpj').mask('000.000.000-00#', options)
            ;
        });

        $(document).on('change', '#cpf_cnpj', function() {

            
            if($.inArray($(this).val().length, [14,18]) === -1) {
                return;
            }

            $.ajax({
                url: '/api/cliente/validaCpf',
                type: 'POST',
                data: {
                    "cpf_cnpj": $(this).val(),
                    "cliente_id": window.location.pathname.replace(/\D/g, '')
                },
                beforeSend: function() {
                    $('.content').loader('show');
                },
                success: function(data) {
                    if(data.success) {
                        return;
                    }
                    $('#cpf_cnpj').val('');
                    Swal.fire({
                        text: data.msg,
                        type: "warning",
                        icon: "warning",
                    });
                },
                complete: function(){
                    $('.content').loader('hide');
                }
            });
        });
    </script>
@endsection