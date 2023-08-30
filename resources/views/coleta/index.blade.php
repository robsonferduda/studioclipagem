@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-database ml-2"></i> Coletas 
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
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['coletas']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-1 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_coleta" required="true" value="{{ $dt_coleta }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 mt-3">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div>  
                <div class="col-lg-12 col-sm-12 px-4">
                    @if(count($coletas))
                        <table id="bootstrap-table" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Início</th>
                                    <th>Término</th>
                                    <th>Duração</th>
                                    <th class="center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coletas as $coleta)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($coleta->created_at)->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($coleta->updated_at)->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ \Carbon\Carbon::create($coleta->updated_at)->diffInMinutes(\Carbon\Carbon::create($coleta->created_at)) }} minutos</td>
                                        <td class="center">{{ $coleta->total_coletas }} </td>
                                    </tr>   
                                @endforeach                                   
                            </tbody>
                        </table> 
                    @else
                        <p>Nenhuma coleta realizada no dia {{ $dt_coleta }}</p>
                    @endif
                </div>                          
            </div>
        </div>
    </div>
</div> 
@endsection