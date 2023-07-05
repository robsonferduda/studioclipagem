@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-hashtag"></i> Tags
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('tags/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
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
                            <tr class="m-0">
                                <th class="w-10 text-center">Código</th>
                                <th class="w-50">Nome</th>
                                <th class="w-10 disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="w-10 text-center">Código</th>
                                <th class="w-50">Nome</th>
                                <th class="w-10 disabled-sorting text-center">Ações</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($tags as $tag)
                                <tr>
                                    <td class="w-10 text-center">{{ $tag->id }}</td>
                                    <td class="w-50">{!! $tag->nome ?? '' !!}</td>
                                    <td class="w-10 text-center">
                                        <a title="Editar" href="{{ url('tags/'.$tag->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <a title="Excluir" href="{{ url('tags/'.$tag->id.'/remover') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
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