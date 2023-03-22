@extends('layouts.app')
@section('content')
    <div class="row">   
        <div class="col-md-6">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-3 col-md-3">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-newspaper-o text-info"></i>
                                    </div>
                                </div>
                                <div class="col-9 col-md-9">
                                    <div class="numbers">
                                        <p class="card-category">JORNAL IMPRESSO</p>
                                        <p class="card-title"><a href="{{ url('impresso') }}">{{ $totais['impresso'] }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-3 col-md-3">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-globe text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-9 col-md-9">
                                    <div class="numbers">
                                        <p class="card-category">JORNAL WEB</p>
                                        <p class="card-title"><a href="{{ url('jornal-web') }}">{{ $totais['web'] }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-3 col-md-3">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-volume-up text-success"></i>
                                    </div>
                                </div>
                                <div class="col-9 col-md-9">
                                    <div class="numbers">
                                        <p class="card-category">RÁDIO</p>
                                        <p class="card-title"><a href="{{ url('radio') }}">{{ $totais['radio'] }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-stats">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-3 col-md-3">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-tv text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-9 col-md-9">
                                    <div class="numbers">
                                        <p class="card-category">TELEVISÃO</p>
                                        <p class="card-title"><a href="{{ url('tv') }}">{{ $totais['tv'] }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div> 
        <div class="col-md-6">
            <div class="card card-timeline card-plain">
                <h6>{{ \Carbon\Carbon::parse(Session::get('data_atual'))->format('d/m/Y') }} <a href=""><i class="fa fa-refresh"></i></a></h6>
                <div class="card-content">
                  <ul class="timeline timeline-simple">
                     <li class="timeline-inverted">
                        <div class="timeline-badge success">
                           <i class="fa fa-tags fa-2x mt-1"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6>CATEGORIAS <span class="badge badge-pill badge-success pull-right">{{ $total_sem_area }} NOTÍCIAS</span></h6>
                            </div>
                            <div class="timeline-body">
                                @if($coletas->count())
                                    <p>Existem {{ $total_sem_area }} notícias sem identificação de categoria.</p>
                                @else
                                    <p><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                                @endif
                            </div>
                        </div>
                     </li>
                    <li class="timeline-inverted">
                        <div class="timeline-badge warning">
                           <i class="fa fa-globe fa-2x mt-0"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6>COLETAS EXECUTADAS <span class="badge badge-pill badge-warning pull-right">{{ $coletas->count() }} coletas</span></h6>
                            </div>
                            <div class="timeline-body">
                                @if($coletas->count())
                                    <table id="bootstrap-table" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Data da Início</th>
                                                <th>Data da Término</th>
                                                <th>Duração</th>
                                                <th class="center">Total Coletado</th>
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
                                    <p><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                                @endif
                            </div>
                        </div>
                    </li>
                    <li class="timeline-inverted">
                        <div class="timeline-badge info">
                           <i class="nc-icon nc-sound-wave font-20"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6>REGISTRO DE MONITORAMENTO DIÁRIO<span class="badge badge-pill badge-primary pull-right">23 EXECUÇÕES</span></h6>
                            </div>
                            <div class="timeline-body">
                                <table id="bootstrap-table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>CÓDIGO</th>
                                            <th>NOME</th>
                                            <th class="center">VER</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monitoramentos as $monitoramento)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($monitoramento->created_at)->format('d/m/Y H:i:s') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($monitoramento->updated_at)->format('d/m/Y H:i:s') }}</td>
                                                <td>{{ \Carbon\Carbon::create($monitoramento->updated_at)->diffInMinutes(\Carbon\Carbon::create($monitoramento->created_at)) }} minutos</td>
                                                <td class="center">{{ $monitoramento->total_vinculado }} </td>
                                            </tr>   
                                        @endforeach                                    
                                    </tbody>
                                 </table>
                                <hr/>
                                <button type="button" class="btn btn-sm btn-primary pull-right">
                                    <span class="btn-label"><i class="fa fa-check"></i></span> Gerar Boletins
                                </button>
                            </div>
                        </div>
                    </li>
                  </ul>
               </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        
    });
</script>
@endsection