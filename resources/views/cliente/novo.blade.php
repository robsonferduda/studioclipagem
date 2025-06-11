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
                                            <input class="form-check-input" type="checkbox" name="fl_ativo" value="true">
                                            ATIVO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <label class="form-check-label mt-2">
                                            <input class="form-check-input" {{ (old('fl_print')) ? 'checked' : '' }} type="checkbox" name="fl_print" value="true">
                                            NOTÍCIAS COM PRINT
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>    

                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" type="checkbox" name="fl_sentimento_cli" value="true">
                                            MOSTRAR SENTIMENTO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>                         
                               
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <p class="mb-1"><i class="nc-icon nc-sound-wave"></i> Clipagem de Mídia</p>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ old('fl_impresso') ? 'checked' : '' }} type="checkbox" name="fl_impresso" value="true">
                                        IMPRESSO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{  old('fl_web') ? 'checked' : '' }} type="checkbox" name="fl_web" value="true">
                                        WEB
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{  old('fl_radio') ? 'checked' : '' }} type="checkbox" name="fl_radio" value="true">
                                        RÁDIO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{  old('fl_tv') ? 'checked' : '' }} type="checkbox" name="fl_tv" value="true">
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
                                <p class="mb-1"> <i class="fa fa-file-pdf-o"></i> Relatórios</p>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ old('fl_relatorio_completo') ? 'checked' : '' }} type="checkbox" name="fl_relatorio_completo" value="true">
                                            RELATÓRIO COMPLETO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ old('fl_relatorio_consolidado') ? 'checked' : '' }} type="checkbox" name="fl_relatorio_consolidado" value="true">
                                            RELATÓRIO CONSOLIDADO
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                                <div class="form-check float-left mr-3">
                                    <label class="form-check-label mt-2">
                                        <input class="form-check-input" {{ old('fl_link_relatorio') ? 'checked' : '' }} type="checkbox" name="fl_link_relatorio" value="true">
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
                                <p class="mb-2"><i class="fa fa-envelope"></i> Endereços Eletrônicos</p>
                                <div class="form-group">
                                    <textarea class="form-control" name="emails" id="emails" rows="3">{{ old('emails') }}</textarea>
                                </div>
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
                    <div class="col-md-12 mt-2">
                        <p>O cadastro de <strong>Usuários</strong> e <strong>Áreas</strong> será habilitado após o cadastro do cliente</p>
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
