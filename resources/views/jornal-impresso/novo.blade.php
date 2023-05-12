@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_jornal_impresso_cadastrar', 'url' => ['jornal-impresso/inserir']]) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="card-title ml-3"><i class="fa fa-newspaper-o"></i> Jornal Impresso
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar</h4>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('jornal-impresso/listar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-table"></i> Jornal Impresso</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="row mr-1 ml-1">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código">
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label>Nome <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Estado </label>
                            <select class="form-control" name="estado" id="estado">
                                <option value="">Selecione</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->cd_estado }}">{{ $estado->nm_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cidade </label>
                            <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
                                <option value="">Selecione o estado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center mb-2">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('impresso') }}" class="btn btn-danger ml-2"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection
@section('script')
    <script>
        $(document).ready(function(){

            $('#estado').select2({
                placeholder: 'Selecione',
                allowClear: true
            });

            $('#cidade').select2({
                placeholder: 'Selecione',
                allowClear: true
            });

        })

        $(document).on('change', '#estado', function() {

            var host =  $('meta[name="base-url"]').attr('content');
            $('#cidade').find('option').remove().end();

            if($(this).val() == '') {
                $('#cidade').attr('disabled', true);
                $('#cidade').append('<option value="">Selecione</option>').val('');
                return;
            }

            $('#cidade').append('<option value="">Carregando...</option>').val('');

            $.ajax({
                url: host+'/api/estado/getCidades',
                type: 'GET',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "estado": $(this).val(),
                },
                beforeSend: function() {
                    $('.content').loader('show');
                },
                success: function(data) {
                    if(!data) {
                        Swal.fire({
                            text: 'Não foi possível buscar as cidades. Por favor, tente novamente mais tarde',
                            type: "warning",
                            icon: "warning",
                        });
                        return;
                    }
                    $('#cidade').attr('disabled', false);
                    $('#cidade').find('option').remove().end();

                    data.forEach(element => {
                        let option = new Option(element.nm_cidade, element.cd_cidade);
                        $('#cidade').append(option);
                    });
                    $('#cidade').val('');
                    $('#cidade').select2('destroy');
                    $('#cidade').select2({placeholder: 'Selecione', allowClear: true});
                },
                complete: function(){
                    $('.content').loader('hide');
                }
            });
        })
    </script>
@endsection
