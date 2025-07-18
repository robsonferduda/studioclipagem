@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Web
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
                    {!! Form::open(['id' => 'frm_fonte_impressa', 'class' => 'form-horizontal', 'url' => ['fonte-web/listar']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control select2" name="cd_estado" id="cd_estado">
                                            <option value="">Selecione um estado</option>
                                            @foreach ($estados as $estado)
                                                <option value="{{ $estado->cd_estado }}" {{ (Session::get('filtro_estado') and Session::get('filtro_estado') == $estado->cd_estado ) ? 'selected' : '' }}>{{ $estado->nm_estado }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                            <option value="">Selecione uma cidade</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Prioridade</label>
                                        <select class="form-control" name="id_prioridade" id="id_prioridade">
                                            <option value="">Selecione uma prioridade</option>
                                            <option value="6" {{ (Session::get('filtro_prioridade') and Session::get('filtro_prioridade') == 6 ) ? 'selected' : '' }}>Prioridade 0</option>
                                            <option value="1" {{ (Session::get('filtro_prioridade') and Session::get('filtro_prioridade') == 1 ) ? 'selected' : '' }}>Prioridade 1</option>
                                            <option value="2" {{ (Session::get('filtro_prioridade') and Session::get('filtro_prioridade') == 2 ) ? 'selected' : '' }}>Prioridade 2</option>
                                            <option value="3" {{ (Session::get('filtro_prioridade') and Session::get('filtro_prioridade') == 3 ) ? 'selected' : '' }}>Prioridade 3</option>
                                            <option value="4" {{ (Session::get('filtro_prioridade') and Session::get('filtro_prioridade') == 4 ) ? 'selected' : '' }}>Prioridade 4</option>
                                            <option value="5" {{ (Session::get('filtro_prioridade') and Session::get('filtro_prioridade') == 5 ) ? 'selected' : '' }}>Prioridade 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" value="{{ (Session::get('filtro_nome')) ? Session::get('filtro_nome') : '' }}">
                                    </div>
                                </div>    
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>URL <span class="text-info">Qualquer termo</span></label>
                                        <input type="text" class="form-control" name="url" id="url" placeholder="URL" value="{{ (Session::get('filtro_url')) ? Session::get('filtro_url') : '' }}">
                                    </div>
                                </div>  
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Código</label>
                                        <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" value="{{ (Session::get('filtro_codigo')) ? Session::get('filtro_codigo') : '' }}">
                                    </div>
                                </div>   
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <a href="{{ url('fonte-web/limpar') }}" class="btn btn-warning btn-limpar mb-3"><i class="fa fa-refresh"></i> Limpar</a>
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>            
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-0">
                                    <span data-valor="-1" class="badge badge-default filtro-situacao" style="background: #66615b !important; border-color: #66615b !important;">Todas</span>
                                    @foreach($situacoes as $situacao)
                                        <span data-valor="{{ $situacao->id_situacao }}" class="badge badge-default filtro-situacao" style="background: {{ $situacao->ds_color }} !important; border-color: {{ $situacao->ds_color }} !important;">{{ $situacao->ds_situacao }} ({{ $situacao->total }})</span>
                                    @endforeach
                                </div>
                            </div>   
                        {!! Form::close() !!}   
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">   
                    <div class="col-lg-12 col-sm-12 conteudo">      
                        @if($fontes->count())
                        <h6 class="px-3">Mostrando {{ $fontes->count() }} de {{ $fontes->total() }} fontes</h6>
                    @endif

                    {{ $fontes->onEachSide(1)->appends([''])->links('vendor.pagination.bootstrap-4') }} 

                    <table id="bootstrap-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><label style="display: inline; color: black; font-weight: 600; font-size: 14px;"><input style="width: 30%;" type="checkbox" class="dt-checkboxes"></label></th>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Nome</th>
                                <th>URL</th>
                                <th>Valor cm/col</th>
                                <th>Situação</th>
                                <th>Prioridade</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Nome</th>
                                <th>URL</th>
                                <th>Valor cm/col</th>
                                <th>Situação</th>
                                <th>Prioridade</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($fontes as $fonte)
                                <tr>
                                    <td style="min-width: 100px;">
                                        <label style="display: inline; color: black; font-weight: 600; font-size: 14px;"><input style="width: 30%;" type="checkbox" class="dt-checkboxes">{{ $fonte->id }}</label>
                                    </td>
                                    <td>
                                        {!! ($fonte->estado) ? $fonte->estado->nm_estado: 'Não informado' !!}
                                    </td>
                                    <td>
                                        {!! ($fonte->cidade) ? $fonte->cidade->nm_cidade: 'Não informado' !!}
                                    </td>
                                    <td>
                                        {{ $fonte->nome }}
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $fonte->url }}</p>
                                        @if($fonte->id_situacao > 0)
                                            <span class="text-info">Última tentativa de coleta em {{ ($fonte->crawlead_at) ? \Carbon\Carbon::parse($fonte->crawlead_at)->format('d/m/Y H:i:s') : '' }}</span>
                                        @else
                                            <span class="text-danger">Nenhuma tentativa de coleta realizada</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($fonte->nu_valor, 2, ".","") }}
                                    </td>
                                    <td>
                                        <span class="badge badge-default" style="background: {{ ($fonte->situacao) ? $fonte->situacao->ds_color : 'black' }} !important; border-color:  {{ ($fonte->situacao) ? $fonte->situacao->ds_color : '' }} !important;">{{ ($fonte->situacao) ? $fonte->situacao->ds_situacao : 'Não Informado' }}</span>
                                    </td>
                                    <td>
                                        <span data-fonte="{{ $fonte->id }}" data-id="{{ ($fonte->prioridade) ? $fonte->prioridade->id : '' }}" class="badge badge-default btn-prioridade" style="background: {{ ($fonte->prioridade) ? $fonte->prioridade->ds_color : '' }} !important; border-color: {{ ($fonte->prioridade) ? $fonte->prioridade->ds_color : '' }} !important;">Prioridade {{ ($fonte->prioridade) ? $fonte->prioridade->id : '' }}</span>                  
                                    </td>
                                    <td class="acoes-3">
                                        <div class="text-center">
                                            <a title="Estatísticas" href="{{ url('fonte-web/estatisticas', $fonte->id) }}" class="btn btn-warning btn-link btn-icon"> <i class="fa fa-bar-chart fa-2x"></i></a>
                                            <a title="Editar" href="{{ url('fonte-web/editar', $fonte->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                            <a title="Excluir" href="{{ url('fonte-web/excluir', $fonte->id) }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $fontes->onEachSide(1)->appends([''])->links('vendor.pagination.bootstrap-4') }} 
                    </div>
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

        $("#cd_estado").trigger("change");

        $(document).on('click', '.btn-prioridade', function() {   

            fonte = $(this).data("fonte");
            prioridade = $(this).data("id");
            
            $.ajax({
                url: '../fonte-web/prioridade/atualizar',
                type: 'POST',
                data: { "_token": token,
                        "fonte": fonte,
                        "prioridade":prioridade
                                },
                success: function(result) {
                    location.reload();  
                },
                error: function(response){

                },
                complete: function(response) {
                   
                }
            });  

            table.draw();
        });
        
        $(document).on('click', '.filtro-situacao', function() {     
            
            situacao = $(this).data("valor");
            estado = $("#cd_estado").val();
            
            $.ajax({
                url: '../fonte-web/filtrar-situacao',
                type: 'POST',
                data: { "_token": token,
                        "situacao": situacao
                },
                success: function(result) {
                    window.location.reload();   
                },
                error: function(response){

                },
                complete: function(response) {
                   
                }
            });  

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