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
    {!! Form::open(['id' => 'frm_cliente', 'url' => ['cliente'], 'files' => true]) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-3">
                            <i class="nc-icon nc-briefcase-24"></i> Clientes 
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nome <span class="text-danger">Obrigatório</span></label>
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required value="{{ old('nome')}}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" {{ (old('fl_print')) ? 'checked' : '' }} type="checkbox" name="fl_print" value="true">
                                            NOTÍCIAS COM PRINT
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                          
                                <div class="form-group">
                                    <div class="form-check">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" type="checkbox" name="fl_ativo" value="true">
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
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <label>Logo Expandida </label>
                                    <input type="file" class="form-control" name="logo_expandida" id="logo_expandida">
                                </div>
                            </div>
                        </div>
                    </div>
                 
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Emails</th>
                                    <th><a title="Adicionar" class="btn btn-primary btn-link btn-icon btn-adicionar"><i class="fa fa-plus"></i></a></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-endereco-eletronico">
                                <tr class="linha-email">
                                    <td><input type="text" class="form-control" name="email[]" placeholder="Email" value="" /></td>
                                    <td><a title="Remover" class="btn btn-danger btn-link btn-icon btn-remover"><i class="fa fa-trash"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <hr/>
                        <p><i class="fa fa-tags"></i> Áreas do Cliente</p>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Área</th>
                                    <th>Expressão</th>
                                    <th>Status</th>
                                    <th><a title="Adicionar" class="btn btn-primary btn-link btn-icon btn-adicionar-expressao"><i class="fa fa-plus"></i></a></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-expressoes">
                                <tr class="linha-expressao">
                                    <td>
                                        <select class="form-control" name="area[]">
                                            <option value="">Selecione</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}">{{ $area->descricao }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-expressao" name="expressao[]" placeholder="Expressao" />
                                    </td>
                                    <td>
                                        <select class="form-control" name="status[]">
                                            <option value="true">Ativo</option>
                                            <option value="false">Inativo</option>
                                        </select>
                                    </td>
                                    <td>
                                        <a title="Remover" class="btn btn-danger btn-link btn-icon btn-remover-expressao"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center mb-3">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('cliente') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
    {!! Form::open(['id' => 'frm_cliente', 'url' => ['cliente'], 'files' => true]) !!}

    {!! Form::close() !!}
</div>
@endsection
@section('script')
    <script>
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

        $(document).on('click', '.btn-remover', function() {
            Componente.remove(this, '#tbody-endereco-eletronico');
        });

        $(document).on('click', '.btn-remover-expressao', function() {
            Componente.remove(this, '#tbody-expressoes');
        });

        $(document).ready(function() {
            Componente.init();

            var host =  $('meta[name="base-url"]').attr('content');

                var options = {
                onKeyPress: function (cpf, ev, el, op) {
                    var masks = ['000.000.000-000', '00.000.000/0000-00'];
                    $('#cpf_cnpj').mask((cpf.length > 14) ? masks[1] : masks[0], op);
                }
            }

            $('#cpf_cnpj').val().length > 13 ?
                $('#cpf_cnpj').mask('00.000.000/0000-00', options) :
                $('#cpf_cnpj').mask('000.000.000-00#', options)
            ;
        });
    </script>
@endsection
