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
                    <a href="{{ url('tv/emissoras/novo') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12 conteudo">                        
                    <table id="bootstrap-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Emissora</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>URL</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Emissora</th>
                                <th>Nome</th>
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