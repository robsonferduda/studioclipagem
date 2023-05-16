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
                                @if(count($cliente->clienteArea) > 0)
                                    @foreach($cliente->clienteArea as $expressao)
                                        <tr class="linha-expressao">
                                            <td>
                                                <select class="form-control select-area" name="area[]">
                                                    <option value="">Selecione</option>
                                                    @foreach($areas as $area)
                                                        <option value="{{ $area->id }}" {{ ($area->id == $expressao->area_id) ? 'selected' : '' }} >{{ $area->descricao }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control input-expressao p-" name="expressao[]" placeholder="Expressao" value="{{ $expressao->expressao }}" />
                                            </td>
                                            <td>
                                                <select class="form-control select-status" name="status[]">
                                                    <option value="true"  {{ ($area->id == $expressao->ativo) === true ? 'selected' : '' }}>Ativo</option>
                                                    <option value="false"  {{ ($area->id == $expressao->ativo) === false ? 'selected' : '' }}>Inativo</option>
                                                </select>
                                                </div>
                                            </td>
                                            <td>
                                                <a title="Remover" class="btn btn-danger btn-link btn-icon btn-remover-expressao"><i class="fa fa-trash"></i></a>
                                                <input type="hidden" name="id[]" value="{{ $expressao->id }}" />
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="linha-expressao">
                                        <td>
                                            <select class="form-control select-area" name="area[]">
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
                                            <select class="form-control select-status" name="status[]">
                                                <option value="true">Ativo</option>
                                                <option value="false">Inativo</option>
                                            </select>
                                        </td>
                                        <td>
                                            <a title="Remover" class="btn btn-danger btn-link btn-icon btn-remover-expressao"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endif
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
</div>
@endsection
@section('script')
    <script>
        Componente = {
            init: function() {
                $('.select-area').select2({
                    placeholder: 'Selecione',
                    allowClear: true
                })

                $('.select-status').select2({
                    placeholder: 'Selecione',
                    minimumResultsForSearch: Infinity
                })
            },
            remove: function(element, table) {
                if($(table).find('tr').length > 1) {
                    $(element).parents('tr').remove();
                    return;
                }

                $(element).parents('tr').find('input').val('');
            }
        }

        $(document).on('click', '.btn-adicionar', function() {
            let element = '#tbody-endereco-eletronico';
            let clone = $(element).find('tr').eq(0).clone();
            $(clone).find('input, select').val('');
            $(element).prepend(clone);
        });

        $(document).on('click', '.btn-adicionar-expressao', function() {
            let element = '#tbody-expressoes';
            $(element).find('select').select2('destroy');

            let clone = $(element).find('tr').eq(0).clone();
            $(clone).find('input, select').val('');
            $(clone).find('.select-area').val('');
            $(clone).find('.select-status').val('true');
            $(element).prepend(clone);
            $(element).find('tr').eq(0).find('input-expressao').focus();
            Componente.init();
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
            Componente.remove(this, '#tbody-endereco-eletronico');
        });

        $(document).on('click', '.btn-remover-expressao', function() {
            Componente.remove(this, '#tbody-expressoes');
        });

        $(document).ready(function() {
            Componente.init();

            let host =  $('meta[name="base-url"]').attr('content');

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
    </script>
@endsection
