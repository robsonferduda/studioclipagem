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
                            <div class="col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <select class="form-control" name="tipo_programa" id="tipo_programa">
                                        <option value="">Selecione um tipo</option>
                                        @foreach($tipos as $tipo)
                                            <option value="{{ $tipo->id }}" {{ (Session::get('filtro_tipo') == $tipo->id) ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-12">
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
           
                <div class="col-lg-12 col-sm-12 conteudo">  
                    <input type="hidden" name="cd_cidade_selecionada" id="cd_cidade_selecionada" value="{{ Session::get('filtro_cidade') }}">
                    @if(count($programas))
                        <p class="mb-0">Mostrando <strong>{{ $programas->count() }}</strong> de <strong>{{ $programas->total() }}</strong> programas</p>
                    @endif
                    <p class="mt-0 mb-1 text-info">Clique sobre o ícone de <strong>Gravação</strong> para pausar/continuar a gravação</p>
                    @if($programas->total())                      
                        <table id="bootstrap-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th>Cidade</th>
                                    <th>Emissora</th>
                                    <th>Programa</th>
                                    <th>Tipo</th>
                                    <th>Situação</th>
                                    <th>Valor</th>
                                    <th class="disabled-sorting text-center">Gravação</th>
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
                                    <th>Situação</th>
                                    <th>Valor</th>
                                    <th class="disabled-sorting text-center">Gravação</th>
                                    <th class="disabled-sorting text-center">Ações</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($programas as $programa)
                                <tr>
                                    <td>{{ ($programa->estado) ? $programa->estado->sg_estado : 'Não Informado' }}</td>
                                    <td>{{ ($programa->cidade) ? $programa->cidade->nm_cidade : 'Não Informado' }}</td>
                                    <td>{{ ($programa->emissora) ? $programa->emissora->nome_emissora : 'Não Informado'  }}</td>
                                    <td>{{ $programa->nome_programa }}</td>
                                    <td>
                                        @if($programa->tipo)
                                            <span class="badge badge-primary" style="background: '.$programa->tipo->ds_color.'; border-color: '.$programa->tipo->ds_color.';"> {{ $programa->tipo->nome }}</span>
                                        @else
                                            <span class="text-danger">Não informado</span>
                                        @endif
                                        <p class="mb-0">{{ $programa->url }}</p>
                                    </td>
                                    <td class="text-center">
                                        @if($programa->id_situacao == 1)
                                            <span class="badge badge-pill badge-success">Normal</span>
                                        @else
                                            <span class="badge badge-pill badge-danger">Erro</span>
                                        @endif
                                    </td>
                                    <td class="right">{{ number_format($programa->nu_valor, 2, ".","") }}</td>
                                    <td class="center">
                                        <a href="{{ url('tv/emissora/programa/'.$programa->id.'/gravacao/atualiza') }}">{!! ($programa->gravar) ? '<span class="badge badge-pill badge-success">SIM</span>' : '<span class="badge badge-pill badge-danger">NÃO</span>' !!}</a>
                                    </td>
                                    <td class="center acoes-3">
                                        
                                        @if(count($programa->horarios))
                                            <a title="Horários de Coleta" href="{{ url('tv/emissora/programas/'.$programa->id.'/horarios') }}" class="btn btn-warning btn-link btn-icon"><i class="nc-icon nc-time-alarm font-25"></i></a>
                                        @else
                                            <a title="Horários de Coleta" href="{{ url('tv/emissora/programas/'.$programa->id.'/horarios') }}" class="btn btn-default btn-link btn-icon"><i class="nc-icon nc-time-alarm font-25"></i></a>
                                        @endif                                        
                                        <a title="Editar" href="{{ url('tv/emissoras/programas/editar',$programa->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <a title="Excluir" href="" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-times fa-2x"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table> 
                        {{ $programas->onEachSide(1)->appends(['gravar' => $gravar, 'cd_estado' => $cd_estado, 'cd_cidade' => $cd_cidade, 'descricao' => $descricao])->links('vendor.pagination.bootstrap-4') }}
                    @else
                        <p>Não existem registros para os termos de busca selecionados.</p>
                    @endif                  
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
       
        $("#cd_estado").trigger('change');

        /*
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
                        'targets': 0,
                        'className': 'item',
                        'checkboxes': true,
                        'ordering': false,
                        'sortable': false
                    }
                ],
                "stateSave": true
            }); */
    });
</script>
@endsection