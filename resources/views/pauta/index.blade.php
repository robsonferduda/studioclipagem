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
            <div class="col-md-12">
                <div class="content table-full-width">
                    <table class="table table-striped">
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
                                    <td>{!! $pauta->cliente->pessoa->nome !!}</td>
                                    <td>{!! $pauta->descricao !!}</td>
                                    <td class="center">
                                        <a title="Editar" href="{{ url('radio/noticias/'.$pauta->id.'/editar') }}" class="btn btn-warning btn-link btn-icon"><i class="fa fa-check fa-2x"></i></a>
                                        <a title="Editar" href="{{ url('radio/noticias/'.$pauta->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <a title="Excluir" href="{{ url('radio/noticias/'.$pauta->id.'/remover') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
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
@section('script')
    <script>
        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');

        });
    </script>
@endsection