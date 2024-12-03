@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Jornal Web
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('buscar-web') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias Web</a>
                    <a href="{{ url('fonte-web/create') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                    <!--<a href="{{ url('fonte-web/importacao') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Importar</a>-->
                    <button class="btn btn-warning pull-right" style="margin-right: 12px;" type="button" name="refresh" title="Refresh"  data-toggle="modal" data-target="#exampleModal"><i class="fa fa-edit"></i> Editar Seleção</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="form-group m-3 w-70">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select class="form-control select2" name="cd_estado" id="cd_estado">
                                        <option value="">Selecione um estado</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{ $estado->cd_estado }}">{{ $estado->nm_estado }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cidade</label>
                                    <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                        <option value="">Selecione uma cidade</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" value="">
                                </div>
                            </div>    
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Nome" value="">
                                </div>
                            </div>           
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                @foreach($situacoes as $situacao)
                                    <span data-valor="{{ $situacao->id_situacao }}" class="badge badge-default filtro-situacao" style="background: {{ $situacao->ds_color }} !important; border-color: {{ $situacao->ds_color }} !important;">{{ $situacao->ds_situacao }} ({{ $situacao->total }})</span>
                                @endforeach
                            </div>
                        </div>     
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12 conteudo">                        
                    <table id="bootstrap-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Selecionar</th>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Nome</th>
                                <th>URL</th>
                                <th>Situação</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Selecionar</th>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Nome</th>
                                <th>URL</th>
                                <th>Situação</th>
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
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-edit"></i> Editar Seleção</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control select2" name="cd_estado" id="cd_estado">
                            <option value="">Selecione um estado</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->cd_estado }}">{{ $estado->nm_estado }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cidade</label>
                        <select class="form-control select2" name="cd_cidade" id="cd_cidade">
                            <option value="">Selecione uma cidade</option>
                            @foreach ($cidades as $cidade)
                                <option value="{{ $cidade->cd_cidade }}">{{ $cidade->nm_cidade }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
        </div>
        <div class="center">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
          <button type="button" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
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
        var id = "";

        var table = $('#bootstrap-table').DataTable({
                "processing": true,
                "paginate": true,
                "serverSide": true,
                "ordering": false,
                "bFilter": true,
                "ajax":{
                    "url": "{{ url('fonte-web/listar') }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function (d) {
                        d._token   = "{{csrf_token()}}";
                        d.situacao = situacao;
                        d.estado   = estado;
                        d.cidade   = cidade;
                        d.nome     = nome;
                        d.id       = id;
                    }
                },
                "columns": [
                    { data: "id" },
                    { data: "estado" },
                    { data: "cidade" },
                    { data: "nome" },
                    { data: "url" },
                    { data: "situacao" },
                    { data: "acoes" },
                ],
                'columnDefs': [
                    {
                        'targets': 0,
                        'className': 'item',
                        'checkboxes': true,
                        'ordering': false,
                        'sortable': false
                    }
                ],
                "stateSave": true
            });
        
        $(document).on('click', '.filtro-situacao', function() {     
            situacao = $(this).data("valor");
            table.draw();
        });

        $(document).on('change', '#cd_estado', function() {     
            estado = $(this).val();
            table.draw();
        });

        $(document).on('change', '#cidade', function() {     
            cidade = $(this).val();
            table.draw();
        });

        $(document).on('input', '#nome', function() {     
            nome = $(this).val();
            table.draw();
        });

        $(document).on('input', '#codigo', function() {     
            id = $(this).val();
            table.draw();
        });

        $(document).on('click', '.btn-selecao', function() {     
            
            rows_selected = table.column(0).checkboxes.selected();

            $.each(rows_selected, function(index, rowId){
                alert(rowId)
            });

        }) ;       

    });
</script>
@endsection