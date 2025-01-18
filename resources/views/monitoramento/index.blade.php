@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="nc-icon nc-sound-wave ml-2"></i> Monitoramento 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('/') }}" class="btn btn-primary pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('monitoramento/novo') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['monitoramento']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cli)
                                                <option value="{{ $cli->id }}" {{ ($cli->id == $cliente) ? 'selected' : '' }}>{{ $cli->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Monitoramento</label>
                                    <div class="form-group">
                                        <select class="form-control" name="midia" id="midia">
                                            <option value="">Selecione uma mídia</option>
                                            <option value="fl_impresso" {{ ($midia === 'fl_impresso') ? 'selected' : '' }}>Impresso</option>
                                            <option value="fl_radio" {{ ($midia === 'fl_radio') ? 'selected' : '' }}>Rádio</option>
                                            <option value="fl_tv" {{ ($midia === 'fl_tv') ? 'selected' : '' }}>TV</option>
                                            <option value="fl_web" {{ ($midia === 'fl_web') ? 'selected' : '' }}>Web</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Situação</label>
                                    <div class="form-group">
                                        <select class="form-control" name="situacao" id="situacao">
                                            <option value="">Selecione uma situação</option>
                                            <option value="1" {{ ($situacao === "1") ? 'selected' : '' }}>Ativo</option>
                                            <option value="0" {{ ($situacao === "0") ? 'selected' : '' }}>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" id="btn-find" class="btn btn-primary w-100" style="margin-top: 24px;"><i class="fa fa-search"></i> </button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div> 
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {{ $monitoramentos->onEachSide(1)->appends(['cliente' => $cliente, 'situacao' => $situacao, 'midia' => $midia])->links('vendor.pagination.bootstrap-4') }}
                </div>
                @foreach ($monitoramentos as $key => $monitoramento)
                        
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card card-stats ml-3 mr-3" style="border: 1px solid #f1f1f1;">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-md-12">                                           
                                                <p>
                                                    <h6>
                                                        {{ ($monitoramento->cliente) ? $monitoramento->cliente->nome : 'Cliente não informado' }} - 
                                                        <span class="text-muted">{{ ($monitoramento->nome) ? $monitoramento->nome : 'Nome não informado' }} </span>
                                                        <span class="pull-right">
                                                            @if($monitoramento->fl_ativo)
                                                            <i class="fa fa-circle text-success mr-1"></i><a href="{{ url('monitoramento/'.$monitoramento->id.'/atualizar-status') }}">Ativo </a>
                                                            @else
                                                                <i class="fa fa-circle text-danger mr-1"></i><a href="{{ url('monitoramento/'.$monitoramento->id.'/atualizar-status') }}">Inativo </a>
                                                            @endif
                                                        </span>
                                                    </h6>
                                                    @if($monitoramento->fl_web)
                                                        <span class="badge badge-danger">
                                                            WEB
                                                        </span>
                                                    @endif
                                                    @if($monitoramento->fl_tv)
                                                        <span class="badge badge-warning">
                                                            TV
                                                        </span>
                                                    @endif
                                                    @if($monitoramento->fl_radio)
                                                        <span class="badge badge-primary">
                                                            RÁDIO
                                                        </span>
                                                    @endif
                                                    @if($monitoramento->fl_impresso)
                                                        <span class="badge badge-success">
                                                            IMPRESSO
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="card-title"></p>
                                                <p class="text-bold">{{ $monitoramento->expressao }}</p>                                            
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats">
                                            <i class="fa fa-refresh"></i>Última atualização em {{ \Carbon\Carbon::parse($monitoramento->updated_at)->format('d/m/Y H:i:s') }}
                                            <div class="pull-right">
                                                <a title="Excluir" href="{{ url('monitoramento/'.$monitoramento->id.'/excluir') }}" class="btn btn-danger btn-fill btn-icon btn-sm btn-excluir" style="border-radius: 30px;">
                                                    <i class="fa fa-times fa-3x text-white"></i>
                                                </a>
                                                <a title="Editar" href="{{ url('monitoramento/'.$monitoramento->id.'/editar') }}" class="btn btn-primary btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                                    <i class="fa fa-edit fa-3x text-white"></i>
                                                </a>
                                                <a title="Histórico" href="{{ url('monitoramento/'.$monitoramento->id.'/historico') }}" class="btn btn-success btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                                    <i class="fa fa-clock-o fa-3x text-white"></i>
                                                </a>
                                                <a title="Notícias" href="{{ url('monitoramento/'.$monitoramento->id.'/todas-noticias') }}" class="btn btn-warning btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                                    <i class="nc-icon nc-sound-wave text-white"></i>
                                                </a>
                                                <a title="Executar" href="{{ url('monitoramento/'.$monitoramento->id.'/executar') }}" class="btn btn-warning btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                                    <i class="fa fa-bolt fa-3x text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                @endforeach    
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {{ $monitoramentos->onEachSide(1)->appends(['cliente' => $cliente, 'situacao' => $situacao, 'midia' => $midia])->links('vendor.pagination.bootstrap-4') }}
                </div>                    
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

        $("#buscar_monitoramento").change(function(){

            var cliente = $(this).val();

            $.ajax({url: host+'/monitoramento/cliente/'+cliente,
                    type: 'GET',
                    beforeSend: function() {
                        
                    },
                    success: function(data) {

                        $("#accordion").empty();

                        $.each(data, function(k, v) {
                        
                            $("#accordion").append('<div class="card card-plain">'+
                                    '<div class="card-header" role="tab" id="headingOne">'+
                                        '<a data-toggle="collapse" data-parent="#accordion" href="#collapse_'+k+'" aria-expanded="false" aria-controls="collapse_'+k+'" class="collapsed">'+
                                        'Monitoramento'+
                                       ' <i class="nc-icon nc-minimal-down"></i>'+
                                       ' </a>'+
                                    '</div>'+
                                    '<div id="collapse_'+k+'" class="collapse" role="tabpanel" aria-labelledby="headingOne" style="">'+
                                        '<div class="card-body">'+
                                        ' tic synth nesciunt you probably haven heard of them accusamus labore sustainable VHS.'+
                                        '</div>'+
                                    '</div>'+
                                '</div>');

                        });
                            
                    },
                    complete: function(){
                        
                    }
            });

        });

    });
</script>
@endsection