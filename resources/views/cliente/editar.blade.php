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
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required value="{{ $cliente->pessoa->nome }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>CPF/CNPJ </label>
                                    <input type="text" class="form-control" name="cpf_cnpj" id="cpf_cnpj" placeholder="CPF/CNPJ" value="{{ $cliente->pessoa->cpf_cnpj }}">
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
                                            @if($cliente->logo)
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
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th><a title="Adicionar" class="btn btn-primary btn-link btn-icon btn-adicionar"><i class="fa fa-plus"></i></a></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-endereco-eletronico">
                                @if(count($emails))
                                    @foreach($emails as $email)
                                        <tr class="linha-email">
                                            <td><input type="text" class="form-control" name="email[]" placeholder="Email" value="{{ $email->endereco }}" /></td>
                                            <td><a title="Remover" class="btn btn-danger btn-link btn-icon btn-remover"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="linha-email">
                                        <td><input type="text" class="form-control" name="email[]" placeholder="Email" value="" /></td>
                                        <td><a title="Remover" class="btn btn-danger btn-link btn-icon btn-remover"><i class="fa fa-trash"></i></a></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                <div class="col-md-12">
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
@endsection
@section('script')
    <script>
       

        $(document).ready(function() {
            
            let host =  $('meta[name="base-url"]').attr('content');

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