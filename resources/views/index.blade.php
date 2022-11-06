@extends('layouts.app')
@section('content')
    <div class="row">   
        <div class="col-md-6">
            <div class="card ">
                <div class="card-header">
                   <h5 class="p-0 m-0">Notícias do Dia</h5>
                   <p>Listagem de todas as notícias de 26/07/2022</p>
                </div>
                <div class="card-content px-2">
                    <ul class="list-group">
                        @foreach($noticias as $noticia)
                            <li class="list-group-item" data-toggle="modal" data-target="#issue">
                            <div class="media">
                                <div class="media-body">
                                    <p><strong>{{ $noticia->titulo }}</strong> <span class="number pull-right"># {{ $noticia->id }}</span></p>
                                    <h6>
                                        <span class="badge badge-pill badge-danger">VEÍCULOS</span>
                                        <span class="badge badge-pill badge-success ml-2">CLIENTE</span>
                                    </h6>
                                    <p>{{ $noticia->sinopse }}</p>
                                </div>
                            </div>
                            </li>         
                        @endforeach          
                     </ul>
                </div>
                <div class="card-footer">
                   
                </div>
            </div>
        </div> 
        <div class="col-md-6">
            <div class="card card-timeline card-plain">
                <h6>{{ date('d/m/Y') }} <a href=""><i class="fa fa-refresh"></i></a></h6>
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
                                <p>Identifique os veículos antes de continuar para incluir todas as notícias</p>
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