@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Jornal Web
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Inconsistências
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ count($dados) }} correções
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('buscar-web') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias Web</a>
                    <a href="{{ url('fonte-web/create') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            
            <div class="row">
                <div class="col col-lg-12 col-sm-12">                        
                    <table id="bootstrap-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fonte</th>
                                <th>URL</th>
                                <th>Situação</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dados as $key => $fonte)
                                <tr>
                                    <td class="text-center">{{ $fonte->id_knewin }}</td>
                                    <td>{{ $fonte->nome }}</td>
                                    <td>{{ $fonte->url }}</td>
                                    <td>
                                        <span class="badge badge-default" style="background: {{ $fonte->situacao->ds_color }} !important; border-color: {{ $fonte->situacao->ds_color }} !important;">{{ $fonte->situacao->ds_situacao }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a title="Editar" href="{{ url('fonte-web/editar/'.$fonte->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <a title="Excluir" href="{{ url('fonte-web/excluir/'.$fonte->id) }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Código</th>
                                <th>Fonte</th>
                                <th>URL</th>
                                <th>Situação</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr></tr>
                        </tbody>
                    </table>
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {



    });
</script>
@endsection