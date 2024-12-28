@extends('layouts.app')
@section('content')
<div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2">
                            <i class="fa fa-tv"></i> TV 
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ empty($emissora->id) ? 'Novo' :'Editar' }}
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('tv/emissoras') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-tv"></i> Emissoras</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="col-md-12">
                    @if(empty($emissora->id))
                        {!! Form::open(['id' => 'frm_user_create', 'url' => ['emissora/tv/adicionar']]) !!}
                    @else
                        {!! Form::open(['id' => 'frm_noticia_radio_editar', 'url' => ['emissora/tv/atualizar'], 'method' => 'post']) !!}
                    @endif
                                       
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nome <span class="text-danger">Obrigatório</span></label>
                                <input type="text" class="form-control" name="nome_emissora" id="nome_emissora" placeholder="Nome" value="{{ ($emissora) ? $emissora->nome_emissora : old('nome_emissora') }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>URL</label>
                                <input type="text" class="form-control" name="url_stream" id="url_stream" placeholder="URL" value="{{ ($emissora) ? $emissora->url_stream : old('url_stream') }}">
                            </div>
                        </div>
                    </div>    
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                    <a href="{{ url()->previous() }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                </div>
            </div>
        {!! Form::close() !!} 
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');

            $("#cd_estado").trigger('change');
            
        });
    </script>
@endsection