@extends('layouts.app')
@section('content')
    <div class="row">   
        <div class="col-md-4">
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
                                        <p class="card-category">JORNAL</p>
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
                                        <p class="card-category">WEB</p>
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
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title"><i class="fa fa-line-chart" aria-hidden="true"></i> Mais Acessados</h6>
                        </div>
                        <div class="card-content">
                            @if($totais['web'])
                                <ul class="list-unstyled team-members ml-3 mr-3">
                                    @foreach ($top_sites as $site)
                                        <li style="border-bottom: 1px solid #ebebeb; margin-bottom: 3px;">
                                            <div class="row">                                            
                                                <div class="col-md-9">
                                                    {{ $site->nome }}
                                                    <br>
                                                    <span class="text-muted"><small>{{ $site->url }}</small></span>
                                                </div>   
                                                <div class="col-md-2 text-right">
                                                    <p class="mt-2">{{ $site->total }}</p>
                                                </div>             
                                            </div>
                                        </li>
                                    @endforeach                                
                                </ul>
                            @else
                                <p class="mr-2 ml-3"><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title"><i class="fa fa-ban" aria-hidden="true"></i> Sem Coleta</h6>
                        </div>
                        <div class="card-content">
                            @if($totais['web'])
                                <ul class="list-unstyled team-members ml-3 mr-3">
                                    @foreach ($sem_coleta as $site)
                                        <li style="border-bottom: 1px solid #ebebeb; margin-bottom: 3px;">
                                            <div class="row">                                            
                                                <div class="col-md-9">
                                                    {{ $site->nome }}
                                                    <br>
                                                    <span class="text-muted"><small>{{ $site->url }}</small></span>
                                                </div>   
                                                <div class="col-md-2 text-right">
                                                   
                                                </div>             
                                            </div>
                                        </li>
                                    @endforeach                                
                                </ul>
                            @else
                                <p class="mr-2 ml-3"><i class="fa fa-hourglass-start mr-1"></i>Nenhuma coleta realizada no dia de hoje</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="col-md-8">
            <div class="card card-timeline card-plain">
                <h6>{{ \Carbon\Carbon::parse(Session::get('data_atual'))->format('d/m/Y') }}</h6>
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
                                <h6>REGISTRO DE MONITORAMENTO DIÁRIO<span class="badge badge-pill badge-primary pull-right">{{ $execucoes->count() }} EXECUÇÕES</span></h6>
                            </div>
                            <div class="timeline-body">
                                @if($execucoes->count())
                                    <table id="bootstrap-table" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Início</th>
                                                <th>Expressão</th>
                                                <th>Duração</th>
                                                <th class="center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($execucoes as $execucao)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($execucao->created_at)->format('d/m/Y H:i:s') }}</td>
                                                    <td>{{ $execucao->monitoramento->expressao }}</td>
                                                    <td>
                                                        @if(\Carbon\Carbon::create($execucao->updated_at)->diffInMinutes(\Carbon\Carbon::create($execucao->created_at)))
                                                            {{ \Carbon\Carbon::create($execucao->updated_at)->diffInMinutes(\Carbon\Carbon::create($execucao->created_at)) }} minutos
                                                        @else
                                                            {{ \Carbon\Carbon::create($execucao->updated_at)->diffInSeconds(\Carbon\Carbon::create($execucao->created_at)) }} segundos
                                                        @endif
                                                    </td>
                                                    <td class="center"><a href="{{ url('monitoramento/'.$execucao->id.'/noticias') }}">{{ $execucao->total_vinculado }}</a></td>
                                                </tr>   
                                            @endforeach                                    
                                        </tbody>
                                    </table>
                                    <a href="{{ url('monitoramento/listar') }}">Ver Mais</a>
                                @else
                                    <p><i class="fa fa-hourglass-start mr-1"></i>Nenhum monitoramento realizado no dia de hoje</p>
                                @endif
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

        var host =  $('meta[name="base-url"]').attr('content');

        $(".btn-refresh").click(function(){
            
            Swal.fire({
                input: 'text',
                title: "Alterar Data",
                text: "Informe a data que deseja visualizar",              
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                confirmButtonText: '<i class="fa fa-refresh"></i> Atualizar Data',
                cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
                preConfirm: () => {
                    if ($(".swal2-input").val()) {
                        return true;
                    } else {
                        Swal.showValidationMessage('Campo obrigatório')   
                    }
                },
                didOpen: () => {
                    $('.swal2-input').mask('00/00/0000',{ "placeholder": "dd/mm/YYYY" });
                }
            }).then(function(result) {
                if (result.isConfirmed) {

                    var data = $(".swal2-input").val();

                    if(data){

                        $.ajax({
                            url: host+'/alterar-data',
                            type: 'POST',
                            data: {
                                    "_token": $('meta[name="csrf-token"]').attr('content'),
                                    "data": data
                            },
                            success: function(response) {
                                window.location.reload();                                
                            },
                            error: function(response){
                                console.log(response);
                            }
                        });
                    }else{
                        return false;
                    }
                }
            });

        });
    });
</script>
@endsection