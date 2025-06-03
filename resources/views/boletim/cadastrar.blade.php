@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-o ml-3"></i> Boletim 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('boletins') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Boletins</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>           
            <div class="col-lg-12 col-sm-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['boletim']]) !!}
                    <div class="row mb-0">
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Data Boletim</label>
                                <input type="text" class="form-control data-event" name="dt_boletim" id="dt_boletim" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Cliente</label>
                                <select class="form-control select2" name="id_cliente" id="id_cliente" required="true">
                                    <option value="">Selecione um cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{!! $cliente->id !!}">{!! $cliente->nome !!}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <label>Título</label>
                                <input type="text" class="form-control" name="titulo" id="titulo" required="true" value="Boletim Digital - Studio Clipagem - {{ date('d/m/Y') }}">
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="mt-2">Tipos de Mídia <small class="text-info">Utilizado para filtrar os tipos de mídia na hora da montagem do boletim</small></h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="fl_impresso" id="fl_impresso" value="true">
                                        Clipagem de Jornal
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="fl_radio" id="fl_radio" value="true">
                                        Clipagem de Rádio
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="fl_tv" id="fl_tv" value="true">
                                        Clipagem de TV
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="fl_web" id="fl_web" value="true">
                                        Clipagem de Web
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-success" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                            <a href="{{ url('boletins') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                        </div>
                    </div>
                {!! Form::close() !!} 
            </div>      
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var host  = $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');
            
        });
    </script>
@endsection