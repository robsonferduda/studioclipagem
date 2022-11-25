@extends('layouts.app')
@section('content')
    <div class="row">   
        <div class="col-md-6">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6">
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
                                        <p class="card-category">IMPRESSO</p>
                                        <p class="card-title"><a href="{{ url('clientes') }}">{{ $totais['impresso'] }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-3 col-md-3 col-sm-6">
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
                                        <p class="card-category">WEB</p>
                                        <p class="card-title"><a href="{{ url('clientes') }}">{{ $totais['web'] }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-3 col-md-3 col-sm-6">
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
                                        <p class="card-title"><a href="{{ url('clientes') }}">{{ $totais['radio'] }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-lg-3 col-md-3 col-sm-6">
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
                                        <p class="card-category">TV</p>
                                        <p class="card-title"><a href="{{ url('clientes') }}">{{ $totais['tv'] }}</a></p>
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
                <h6>{{ date('d/m/Y') }} <a href=""><i class="fa fa-refresh"></i></a></h6>
                <div class="card-content">
                  <ul class="timeline timeline-simple">
                     <li class="timeline-inverted">
                        <div class="timeline-badge success">
                           <i class="fa fa-tags fa-2x mt-1"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6>IDENTIFICAR ÁREAS <span class="badge badge-pill badge-success pull-right">{{ $totais['impresso'] }} NOTÍCIAS</span></h6>
                            </div>
                            <div class="timeline-body">
                                <p>Existem {{ $totais['impresso'] }} notícias sem identificação das áreas.</p>
                            </div>
                        </div>
                     </li>
                     <li class="timeline-inverted">
                        <div class="timeline-badge info">
                           <i class="fa fa-cogs font-20 mt-1"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6>GERAR BOLETINS <span class="badge badge-pill badge-primary pull-right">23 CLIENTES</span></h6>
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
                                        <tr data-index="0">
                                            <td></td>
                                            <td>7</td>
                                            <td class="center">
                                                <a href="{{ url('noticias/cliente/4') }}"><i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>                                      
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