@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-newspaper-o"></i> Dashboard</a>
                    <a href="{{ url('fonte-impresso/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_fonte_impressa', 'class' => 'form-horizontal', 'url' => ['fonte-impresso/listar']]) !!}
                    <div class="form-group m-3 w-70">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select class="form-control select2" name="cd_estado" id="cd_estado">
                                        <option value="">Selecione um estado</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{ $estado->cd_estado }}">{{ $estado->nm_estado }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cidade</label>
                                    <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                        <option value="">Selecione uma cidade</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" value="">
                                </div>
                            </div>    
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Nome" value="">
                                </div>
                            </div>     
                            <div class="col-md-12 checkbox-radios mb-0">
                                <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                            </div>    
                        </div>
                    </div>
                    {!! Form::close() !!} 
                </div>
            </div>
            <div class="row">

                <div class="col-lg-12 col-sm-12 conteudo">      
                    @if($fontes->count())
                    <h6 class="px-3">Mostrando {{ $fontes->count() }} de {{ $fontes->total() }} fontes</h6>
                @endif

                <table id="fontes_impressas" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Coleta</th>
                            <th>Estado</th>
                            <th>Cidade</th>
                            <th>Nome</th>
                            <th>CAPA</th>
                            <th>CAPA FDS</th>
                            <th>CONTRACAPA</th>
                            <th>DEMAIS</th>
                            <th>DEMAIS FDS</th>
                            <th class="disabled-sorting text-center">Ações</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Coleta</th>
                            <th>Estado</th>
                            <th>Cidade</th>
                            <th>Nome</th>
                            <th>CAPA</th>
                            <th>CAPA FDS</th>
                            <th>CONTRACAPA</th>
                            <th>DEMAIS</th>
                            <th>DEMAIS FDS</th>
                            <th class="disabled-sorting text-center">Ações</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($fontes as $fonte)
                            <tr>
                                <td>{!! ($fonte->codigo) ? $fonte->codigo : '<span class="text-danger">Não Informado</span>' !!}</td>
                                <td>{!! ($fonte->tipoImpresso) ? $fonte->tipoImpresso->ds_tipo_impresso : '<span class="text-danger">Não Informado</span>' !!}</td>
                                <td>{!! ($fonte->tipoColeta) ? $fonte->tipoColeta->ds_tipo_coleta : '<span class="text-danger">Não Informado</span>' !!}</td>
                                <td>{!! ($fonte->estado) ? $fonte->estado->nm_estado : '<span class="text-danger">Não Informado</span>' !!}</td>
                                <td>{!! $fonte->cidade->nm_cidade ?? '<span class="text-danger">Não Informado</span>' !!}</td>
                                <td>
                                    {{ $fonte->nome }}
                                    @if($fonte->tipoColeta and $fonte->tipoColeta->id == 1)
                                        <p class="mb-0"><a href="{{ $fonte->url }}" target="_BLANK">{{ $fonte->url }}</a></p>
                                    @endif
                                </td>
                                <td>R$ {!! $fonte->valor_cm_capa_semana  !!}</td>
                                <td>R$ {!! $fonte->valor_cm_capa_fim_semana !!}</td>
                                <td>R$ {!! $fonte->valor_cm_contracapa !!}</td>
                                <td>R$ {!! $fonte->valor_cm_demais_semana !!}</td>
                                <td>R$ {!! $fonte->valor_cm_demais_fim_semana !!}</td>
                                <td class="text-center acoes-2">
                                    <a title="Editar" href="{{ url('fonte-impresso/'.$fonte->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                    <a title="Excluir" href="{{ url('fonte-impresso/'.$fonte->id.'/excluir') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $fontes->onEachSide(1)->appends([''])->links('vendor.pagination.bootstrap-4') }} 
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        $('#fontes_impressas_off').DataTable({
            "pagingType": "full_numbers",
            "bPaginate": false,
            "searching": false, 
            "bFiltered": false,
            "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"]
            ],
            responsive: true,
            language: {
            search: "_INPUT_",
            searchPlaceholder: "Filtrar",
            }
        });

    });
</script>
@endsection