@extends('layouts.app')
@section('content')
    <div class="row">    
        <div class="col-md-6">
            <div class="card card-timeline card-plain">
                <h6>{{ date('d/m/Y') }}</h6>
                <div class="card-content">
                  <ul class="timeline timeline-simple">
                     <li class="timeline-inverted">
                        <div class="timeline-badge danger">
                           <i class="fa fa-comments fa-2x"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6>Veículos Não Identificados <span class="badge badge-pill badge-danger pull-right">3 VEÍCULOS</span></h6>
                            </div>
                            <div class="timeline-body">
                                <p></p>
                            </div>
                        </div>
                     </li>
                     <li class="timeline-inverted">
                        <div class="timeline-badge success">
                           <i class="fa fa-tags fa-2x mt-1"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6>IDENTIFICAR ÁREAS <span class="badge badge-pill badge-success pull-right">13 NOTÍCIAS</span></h6>
                            </div>
                            <div class="timeline-body">
                                <p>Existem xx notícias sem identificação das áreas.</p>
                                <p>Todos os clientes possuem áreas cadastradas.</p>
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