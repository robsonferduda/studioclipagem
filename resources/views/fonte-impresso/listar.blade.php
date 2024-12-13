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
                <div class="col-lg-12 col-sm-12">
                    <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Nome</th>
                                <th>Retorno de Mídia</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Nome</th>
                                <th>Retorno de Mídia</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($jornais as $jornal)
                                <tr>
                                    <td>{!! ($jornal->codigo) ? $jornal->codigo : '<span class="text-danger">Não Informado</span>' !!}</td>
                                    <td>{{ ($jornal->tipoColeta) ? $jornal->tipoColeta->ds_tipo_coleta : '' }}</td>
                                    <td>{!! ($jornal->estado) ? $jornal->estado->nm_estado : '<span class="text-danger">Não Informado</span>' !!}</td>
                                    <td>{!! $jornal->cidade->nm_cidade ?? '<span class="text-danger">Não Informado</span>' !!}</td>
                                    <td>
                                        {{ $jornal->nome }}
                                        @if($jornal->tipoColeta->id == 1)
                                            <p class="mb-0"><a href="{{ $jornal->url }}" target="_BLANK">{{ $jornal->url }}</a></p>
                                        @endif
                                    </td>
                                    <td>
                                        {!! ($jornal->retorno_midia) ? "R$ ".$jornal->retorno_midia : '<span class="text-danger">Não informado</span>' !!}
                                    </td>
                                    <td class="text-center">
                                        <a title="Editar" href="{{ url('fonte-impresso/'.$jornal->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <a title="Excluir" href="{{ url('fonte-impresso/'.$jornal->id.'/excluir') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
