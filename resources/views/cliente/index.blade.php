@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="nc-icon nc-briefcase-24"></i> Clientes
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Dashboard
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('cliente.create') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['clientes']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" id="btn-find" class="btn btn-primary mt-4"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="row mr-1 ml-1">
                <div class="col-md-12">
                    <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Usuário</th>
                            <th class="text-center">Clipagem</th>
                            <th class="text-center">Situação</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Nome</th>
                            <th>Usuário</th>
                            <th class="text-center">Clipagem</th>
                            <th class="text-center">Situação</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($clientes as $cliente)
                            <tr>
                                <td>
                                    {{ $cliente->nome }} 
                                    @if($cliente->areas->count())
                                        <i title="Cliente possui áreas vinculadas" class="fa fa-tags text-primary"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($cliente->fl_area_restrita)
                                        {!! ($cliente->usuario) ? $cliente->usuario->nome : '<span class="text-danger">Usuário não cadastrado</span>' !!}
                                    @else
                                        <span class="text-info">Sem acesso a Área Restrita</span>
                                    @endif
                                </td>
                                <td class="text-center" style="font-size: 25px;">
                                    @if($cliente->fl_impresso)
                                        <i class="fa fa-newspaper-o text-primary"></i>
                                    @else
                                        <i class="fa fa-newspaper-o"></i>
                                    @endif
                                    @if($cliente->fl_web)
                                        <i class="fa fa-globe text-primary"></i>
                                    @else
                                        <i class="fa fa-globe"></i>
                                    @endif
                                    @if($cliente->fl_radio)
                                        <i class="fa fa-volume-up text-primary"></i>
                                    @else
                                        <i class="fa fa-volume-up"></i>
                                    @endif
                                    @if($cliente->fl_tv)
                                        <i class="fa fa-tvo text-primary"></i>
                                    @else
                                        <i class="fa fa-tv"></i>
                                    @endif
                                </td>
                                <td class="disabled-sorting text-center">{!! ($cliente->fl_ativo) ? '<span class="badge badge-pill badge-success">ATIVO</span>' : '<span class="badge badge-pill badge-danger">INATIVO</span>' !!}</td>
                                <td class="text-center">
                                    <a title="Editar" href="{{ route('cliente.edit',$cliente->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="col-md-12 text-center">
                    {{ $clientes->onEachSide(1)->appends(['nome' => $nome])->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
