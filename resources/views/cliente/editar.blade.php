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
    {!! Form::open(['id' => 'frm_cliente_edit', 'url' => ['cliente', $cliente->id], 'method' => 'patch']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2"><i class="nc-icon nc-circle-10"></i> Clientes > Editar</h4>
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
                <div class="row">
                    <div class="col-md-6 top-40">
                        <div class="flex-wrap">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nome <span class="text-danger">Obrigatório</span></label>
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required value="{{ $cliente->pessoa->nome }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>CPF/CNPJ </label>
                                    <input type="text" class="form-control" name="cpf_cnpj" id="cpf_cnpj" placeholder="CPF/CNPJ" value="{{ $cliente->pessoa->cpf_cnpj }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check mt-3">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" {{ ($cliente->ativo) ? 'checked' : '' }} type="checkbox" name="ativo" value="true">
                                            ATIVO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th><a title="Adicionar" class="btn btn-primary btn-link btn-icon btn-adicionar"><i class="fa fa-plus"></i></a></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-endereco-eletronico">
                                @if($emails)
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
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('cliente') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection
@section('script')
    <script>
        $(document).on('click', '.btn-adicionar', function() {
            let clone = $('#tbody-endereco-eletronico').find('tr').eq(0).clone();
            $(clone).find('input').val('');
            $('#tbody-endereco-eletronico').prepend(clone);
            $('#tbody-endereco-eletronico').find('tr').eq(0).find('input').focus();
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

        $(document).on('click', '.btn-remover', function() {
            if($('.btn-remover').length > 1) {
                $(this).parents('tr').remove();
                return;
            }

            $(this).parents('tr').find('input').val('');
        });

        $(document).ready(function() {

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
