@extends('layouts.app')
@section('content')
    <div class="row">   
        <div class="col-md-12">  
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title"><i class="fa fa-line-chart" aria-hidden="true"></i> Maiores Coletas</h6>
                        </div>
                        <div class="card-content">
                            @if($top_sites)
                                <ul class="list-unstyled team-members ml-3 mr-3">
                                    @foreach ($top_sites as $key => $site)
                                        @if($site->total == 0) @php break 1; @endphp @endif
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

                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title"><i class="fa fa-ban" aria-hidden="true"></i> Sem Coleta</h6>
                        </div>
                        <div class="card-content">
                            @if($sem_coleta)
                                <ul class="list-unstyled team-members ml-3 mr-3">
                                    @foreach ($sem_coleta as $key => $site)
                                       
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
            </div>
        </div> 
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');
    });
</script>
@endsection