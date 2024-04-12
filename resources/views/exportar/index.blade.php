@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-upload ml-3"></i> Exportação 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Exportar Dados
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
                    {!! Form::open(['id' => 'frm-pautas', 'class' => 'form-horizontal', 'url' => ['exportar']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-sm-6 col-md-2">
                                    <div class="form-group">
                                        <label>Data Inicial <span class="text-danger">Obrigatório</span></label>
                                        <input type="text" class="form-control datepicker" name="dt_noticia" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-10">
                                    <div class="form-group">
                                        <label>Cliente <span class="text-danger">Obrigatório</span></label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{!! $cliente->id_unico !!}">{!! $cliente->pessoa->nome !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check mt-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" name="check_jornal" value="true" checked="checked">
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
                                                <input class="form-check-input" type="checkbox" name="check_radio" value="true" checked="checked">
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
                                                <input class="form-check-input" type="checkbox" name="check_tv" value="true" checked="checked">
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
                                                <input class="form-check-input" type="checkbox" name="check_web" value="true" checked="checked">
                                                Clipagem de Web
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-12 center">
                                    <button type="submit" id="btn-find" class="btn btn-primary mt-4"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
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