@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('jornal-impresso/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
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
                                <th>Nome</th>
                                <th>Cidade</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Cidade</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($jornais as $jornal)
                                <tr>
                                    <td>{{ $jornal->codigo }}</td>
                                    <td>{{ $jornal->nome }}</td>
                                    <td>{!! $jornal->cidade->nm_cidade ?? '' !!}</td>
                                    <td class="text-center">
                                        <a title="Editar" href="{{ url('jornal-impresso/'.$jornal->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <a title="Excluir" href="{{ url('jornal-impresso/'.$jornal->id.'/remover') }}" class="btn btn-danger btn-link btn-icon"><i class="fa fa-trash fa-2x"></i></a>
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
