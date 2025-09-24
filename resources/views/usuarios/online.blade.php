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

                        <h6 class="card-title"> <i class="fa fa-wifi text-success"></i> Usuários Online</h6>
                        

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

                <div class="row mt-1 mb-5">
                    <div class="col-md-12 mb-2">
                        {!! Form::open(['id' => 'frm_fonte_impressa', 'class' => 'form-inline float-left', 'url' => ['online']]) !!}
                            <input type="date" id="dt_inicial" name="dt_inicial" class="form-control mr-2" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('Y-m-d') }}">
                            <input type="date" id="dt_final" name="dt_final" class="form-control mr-2" value="{{ \Carbon\Carbon::parse($dt_final)->format('Y-m-d') }}">
                            <select class="form-control mr-2" name="usuario" id="usuario">
                                <option value="">Selecione um colaborador</option>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}" {{ ($usuario and $usuario == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                        {!! Form::close() !!} 
                    </div>
                    <div class="col-md-12">
                        <h6 class="card-title"><i class="fa fa-history text-info"></i> Atividades Recentes</h6>
                        <ul class="list-group">
                            @if ($recentActivities->isEmpty())
                                <li class="list-group-item" style="border: none; padding: 5px 5px; border-radius: 5px;">Nenhuma atividade registrada recentemente.</li>
                            @else
                                @php
                                    $zebra = false;
                                @endphp
                                @foreach ($recentActivities as $log)
                                    <li class="list-group-item mb-3" style="border: none; padding: 5px 5px; background: {{ ($zebra) ? 'white' : 'white' }} ; border-radius: 5px; border: 1px solid #eee;">
                                        <span class="badge badge-pill badge-default pull-right mt-1 mr-2" style="background-color: {{ $log->evento->color }}; border-color: {{ $log->evento->color }};">{{ ucfirst($log->evento->nome) }}</span>
                                        <div>
                                            <strong>Usuário</strong>: {{ ($log->user) ? $log->user->name : 'Sistema' }}
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
                                        </div>
                                        <div>
                                            <span><strong>Data/Hora</strong>: {{ \Carbon\Carbon::parse( $log->created_at)->format('d/m/Y H:i:s') }}</span>
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