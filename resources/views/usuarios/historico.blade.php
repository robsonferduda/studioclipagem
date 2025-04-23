@extends('layouts.app')
@section('content')
    <div class="col-md-12">
        
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2">
                            <i class="nc-icon nc-circle-10"></i> Usuários
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Online
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('usuarios') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Usuários</a>
                    </div>
                </div>
            </div>
            <div class="card-body ml-3 mr-3">

        
                <div class="row mt-3 mb-5">
                    <div class="col-md-12">
                        <h6 class="card-title"><i class="fa fa-history text-info"></i> Atividades Recentes</h6>
                        <ul class="list-group">
                            @if ($atividades->isEmpty())
                                <li class="list-group-item text-danger" style="border: none; padding: 5px 5px; border-radius: 5px;">Nenhuma atividade registrada recentemente.</li>
                            @else
                                @php
                                    $zebra = false;
                                @endphp
                                @foreach($atividades as $log)
                                    <li class="list-group-item mb-3" style="border: none; padding: 5px 5px; background: {{ ($zebra) ? 'white' : 'white' }} ; border-radius: 5px; border: 1px solid #eee;">
                                        <span class="badge badge-pill badge-default pull-right mt-1 mr-2" style="background-color: {{ $log->evento->color }}; border-color: {{ $log->evento->color }};">{{ ucfirst($log->evento->nome) }}</span>
                                        <div>
                                            <strong>Usuário</strong>: {{ $log->user->name }}
                                        </div>
                                        <div>
                                            @if($log->evento->chave == 'activity')
                                                <span><strong>URL</strong>: {{ $log->url }}</span>
                                            @else
                                                <span><strong>Modelo/Tabela</strong>: {{ $log->auditable_type }}</span>  
                                            @endif
                                        </div>
                                        <div>
                                            <span><strong>IP</strong>: {{ $log->ip_address }}</span>
                                        </div>
                                        <div>
                                            <span><strong>Navegador</strong>: {{ $log->user_agent }}</span>
                                            <span class="pull-right text-mutted">Atividade executada {{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </li>
                                    @php
                                        $zebra = !$zebra;
                                    @endphp
                                @endforeach
                            @endif
                        </ul>

                    </div>
                </div>     
            </div>
        </div>
    </div>
@endsection