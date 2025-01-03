@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-tv"></i> TV
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Programas
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('tv/videos') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-tv"></i> Vídeos TV</a>
                    <a href="{{ url('tv/emissoras') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-tv"></i> Emissoras</a>
                    <a href="{{ url('tv/emissoras/programas/novo') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo Programa</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['tv/emissoras/programas']]) !!}
                    <div class="form-group w-70">
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select class="form-control select2" name="cd_estado" id="cd_estado">
                                        <option value="">Selecione um estado</option>
                                        @foreach($estados as $estado)
                                            <option value="{{ $estado->cd_estado }}" {{ (Session::get('filtro_estado') == $estado->cd_estado) ? 'selected' : '' }}>{{ $estado->nm_estado }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label>Cidade</label>
                                    <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                        <option value="">Selecione uma cidade</option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label>Emissora</label>
                                    <input type="text" class="form-control" name="descricao" id="descricao" placeholder="Emissora" value="{{ Session::get('filtro_nome') }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Gravação</label>
                                    <select class="form-control select2" name="fl_gravacao" id="fl_gravacao">
                                        <option value="">Selecione uma situação</option>
                                        <option value="gravando" {{ (Session::get('filtro_gravar') === 1) ? 'selected' : '' }}>Gravando</option>
                                        <option value="nao-gravando" {{ (Session::get('filtro_gravar') === 2) ? 'selected' : '' }}>Não Gravando</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 checkbox-radios mb-0">
                                <a href="{{ url('tv/emissoras/programas/limpar') }}" class="btn btn-warning btn-limpar"><i class="fa fa-refresh"></i> Limpar</a>
                                <button type="submit" id="btn-find" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>                                   
                        </div>    
                    </div>
                {!! Form::close() !!} 
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12 conteudo">                        
                    <table id="bootstrap-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Emissora</th>
                                <th>Programa</th>
                                <th>Tipo</th>
                                <th>URL</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Emissora</th>
                                <th>Programa</th>
                                <th>Tipo</th>
                                <th>URL</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr></tr>
                        </tbody>
                    </table>                   
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
        var token = $('meta[name="csrf-token"]').attr('content');
        var estado = 0;
        var cidade = 0;
        var nome = "";
        var situacao = "";

        var table = $('#bootstrap-table').DataTable({
                "processing": true,
                "paginate": true,
                "serverSide": true,
                "ordering": false,
                "bFilter": true,
                "ajax":{
                    "url": "{{ url('tv/emissoras/programas') }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function (d) {
                        d._token   = "{{csrf_token()}}";
                    }
                },
                "columns": [
                    { data: "estado" },
                    { data: "cidade" },
                    { data: "emissora" },
                    { data: "nome" },
                    { data: "tipo" },
                    { data: "url" },
                    { data: "acoes" },
                ],
                'columnDefs': [
                    {
                        /*'targets': 0,
                        'className': 'item',
                        'checkboxes': true,*/
                        'ordering': false,
                        'sortable': false
                    }
                ],
                "stateSave": true
            });
    });
</script>
@endsection