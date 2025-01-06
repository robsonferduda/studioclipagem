@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-text-o ml-3"></i> Pautas 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listagem de Pautas
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('pauta/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Pauta</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm-pautas', 'class' => 'form-horizontal', 'url' => ['pautas']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach($clientes as $cliente)
                                                <option {{ ($cliente->id == $id_cliente) ? 'selected' : '' }} value="{!! $cliente->id !!}">{!! $cliente->nome !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Pauta <span class="text-info">Digite a pauta ou parte dela</span></label>
                                        <input type="text" class="form-control" value="{{ $descricao }}" name="descricao" id="descricao" placeholder="Pauta">
                                    </div>
                                </div>
                                <div class="col-md-12 center">
                                    <button type="submit" id="btn-find" class="btn btn-primary mt-4"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if(count($pautas))
                        <div class="content table-full-width">
                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Pauta</th>
                                        <th class="center">Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pautas as $pauta)
                                        <tr>
                                            <td>{!! $pauta->cliente->nome !!}</td>
                                            <td>{!! $pauta->descricao !!}</td>
                                            <td class="center">
                                                <a title="Vincular" href="{{ url('pauta/'.$pauta->id.'/vincular') }}" class="btn btn-warning btn-link btn-icon"><i class="fa fa-check fa-2x"></i></a>
                                                <a title="Editar" href="{{ url('pauta/'.$pauta->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                                <a title="Excluir" href="{{ url('pauta/'.$pauta->id.'/remover') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="center">
                            <p class="text-info">Nenhuma pauta corresponde aos termos de busca</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');

        });
    </script>
@endsection