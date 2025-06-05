@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-pdf-o ml-3"></i> Relatórios 
                    </h4>
                </div>
                <div class="col-md-4">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                @if(count($relatorios))
                        <table id="bootstrap-table" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data de Requisição</th>
                                    <th>Data de Término</th>
                                    <th>Arquivo</th>
                                    <th>Responsável</th>
                                    <th class="center">Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($relatorios as $relatorio)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($relatorio->created_at)->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ ($relatorio->dt_finalizacao) ? \Carbon\Carbon::parse($relatorio->dt_finalizacao)->format('d/m/Y H:i:s') : 'Processamento Pendente' }}</td>
                                        <td>
                                            @if($relatorio->situacao == 1)
                                                <a href="{{ url('relatorios/clipping', $relatorio->ds_nome) }}">{{ $relatorio->ds_nome }}</a>
                                            @else
                                                <span>Arquivo não disponível</span>
                                            @endif
                                        </td>
                                        <td>{{ ($relatorio->usuario) ? $relatorio->usuario->name : 'Sistema' }}</td>
                                        <td class="center">
                                            {!! ($relatorio->situacao == 1) ? '<span class="badge badge-pill badge-success">PROCESSADO</span>' : '<span class="badge badge-pill badge-danger">ERRO</span>' !!}
                                        </td>
                                    </tr>   
                                @endforeach                                   
                            </tbody>
                        </table> 
                    @else
                        <p>Nenhum relatório gerado</p>
                    @endif
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

    });
</script>
@endsection