@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Processamento 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-newspaper-o"></i> Dashboard</a>
                    <a href="{{ url('jornal-impresso/upload') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-upload"></i> Upload</a>
                    <a href="{{ url('jornal-impresso/processar') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="fa fa-cogs"></i> Processar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Data Envio</th>
                        <th>Início Processamento</th>
                        <th>Fim Processamento</th>
                        <th>Data Arquivo</th>
                        <th>Fonte</th>
                        <th>Arquivo</th>
                        <th>Tamanho</th>
                        <th class="text-center">Situação</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Data Envio</th>
                        <th>Início Processamento</th>
                        <th>Fim Processamento</th>
                        <th>Data Arquivo</th>
                        <th>Fonte</th>
                        <th>Arquivo</th>
                        <th class="text-center">Tamanho</th>
                        <th class="text-center">Situação</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($fila as $arquivo)
                        <tr>
                            <td>{{ Carbon\Carbon::parse($arquivo->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ ($arquivo->start_at) ? Carbon\Carbon::parse($arquivo->start_at)->format('d/m/Y H:i:s') : 'Aguardando Processamento' }}</td>
                            <td>{{ ($arquivo->start_at and $arquivo->fl_processado) ? Carbon\Carbon::parse($arquivo->updated_at)->format('d/m/Y H:i:s') : 'Aguardando Processamento' }}</td>
                            <td>{{ Carbon\Carbon::parse($arquivo->dt_arquivo)->format('d/m/Y') }}</td>
                            <td>{!! ($arquivo->fonte->nome) ? $arquivo->fonte->nome : '<a href="../jornal-impresso/'.$arquivo->fonte->id.'/editar">Fonte Desconhecida</a>' !!}</td>
                            <td>
                                @if($arquivo->fl_processado)
                                    <a href="{{ url('jornal-impresso/processados/'.$arquivo->ds_arquivo) }}" target="_blank">{{ $arquivo->ds_arquivo }}</a>
                                @else
                                    <a href="{{ url('jornal-impresso/pendentes/'.$arquivo->ds_arquivo) }}" target="_blank">{{ $arquivo->ds_arquivo }}</a>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($arquivo->tamanho, 2) }} MB</td>
                            <td class="text-center">
                                @if($arquivo->start_at and !$arquivo->fl_processado)
                                    {!! '<span class="badge badge-pill badge-warning">ANDAMENTO</span>' !!}
                                @else
                                    {!! ($arquivo->fl_processado) ? '<span class="badge badge-pill badge-success">PROCESSADO </span>' : '<span class="badge badge-pill badge-danger">PENDENTE</span>' !!}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection