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

                <div class="row mb-0">
                    <div class="col-md-12">

                        <h6 class="card-title"> <i class="fa fa-wifi"></i> Usuários Online</h6>
                        

                            <ul class="list-group">
                                @if ($online->isEmpty())
                                    <li class="list-group-item" style="border: none; padding: 5px 0px;">Nenhum usuário online no momento.</li>
                                @else
                                    @foreach ($online as $user)
                                        <li class="list-group-item" style="border: none; padding: 5px 0px;">
                                            <span class="badge badge-pill badge-success mr-2">ONLINE</span>
                                            <strong>{{ $user->name }}</strong>
                                            <span class="pull-right">Última Atividade: {{ \Carbon\Carbon::parse( $user->last_active_at)->format('d/m/Y H:i:s') }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                       
                    </div>
                </div>

                <div class="row mt-3 mb-5">
                    <div class="col-md-12">
                        <h6 class="card-title">Atividades Recentes</h6>
                        <ul class="list-group">
                            @if ($recentActivities->isEmpty())
                                <li class="list-group-item">Nenhuma atividade registrada recentemente.</li>
                            @else
                                @foreach ($recentActivities as $log)
                                    <li class="list-group-item">
                                        <span class="pull-right">Ultima atividade {{ $log->created_at->diffForHumans() }}</span>
                                        <div>
                                            <strong>Usuário</strong>: {{ $log->user->name }}
                                        </div>
                                        <div>
                                            <span><strong>Evento</strong>: {{ ucfirst($log->event) }}</span>
                                        </div>
                                        <div>
                                            <span><strong>IP</strong>: {{ $log->ip_address }}</span>
                                        </div>
                                        <div>
                                            <span><strong>Navegador</strong>: {{ $log->user_agent }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>

                    </div>
                </div>     
            </div>
        </div>
    </div>
@endsection