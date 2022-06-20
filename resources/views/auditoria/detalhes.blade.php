@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-shield"></i> Auditoria 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Detalhes 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('auditoria') }}" class="btn btn-info pull-right"><i class="fa fa-shield"></i> Auditoria</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="pl-0">Item</th>
                            <th class="text-left">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="pl-0">Data do Registro</td>
                            <td class="pr-0 text-left">
                                {{ date('d/m/Y H:i', strtotime($auditoria->created_at)) }}
                            </td>   
                        </tr>
                        <tr>
                            <td class="pl-0">Usuário</td>
                            <td class="pr-0 text-left">
                                {{ ($auditoria->user) ? $auditoria->user->name : 'Não identificado' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-0">URL</td>
                            <td class="pr-0 text-left">
                                {{ $auditoria->url }}
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-0">Navegador</td>
                            <td class="pr-0 text-left">
                                {{ $auditoria->user_agent }}
                            </td>
                        </tr>
                        @if($auditoria->new_values)
                            <tr>
                                <td class="pl-0">Dados Antigos</td>
                                <td class="pr-0 text-left">
                                    @if($auditoria->old_values)
                                        @foreach($auditoria->old_values as $key => $d)
                                            <div class="mb-2"><span class="label label-inline">{{ $key }}</span> => {{ $d }}</div>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-0">Dados Novos</td>
                                <td class="pr-0 text-left">
                                    @if($auditoria->new_values)
                                        @foreach($auditoria->new_values as $key => $d)
                                            <div class="mb-2"><span class="label label-inline">{{ $key }}</span> => {{ $d }}</div>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>        
        </div>
    </div>
</div> 
@endsection