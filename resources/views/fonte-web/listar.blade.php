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
                    <a href="{{ url('buscar-web') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias Web</a>
                    <a href="{{ url('fonte-web/create') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['buscar-web']]) !!}
                    <div class="form-group m-3 w-70">
                        <div class="row">
                            <div class="col-md-4">
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
                                    <select class="form-control select2" name="cd_cidade" id="cd_cidade">
                                        <option value="">Selecione uma cidade</option>
                                        @foreach ($cidades as $cidade)
                                            <option value="{{ $cidade->cd_cidade }}">{{ $cidade->nm_cidade }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" value="">
                                </div>
                            </div>             
                        </div>
                        <div class="row">
                            <div class="col-md-12 checkbox-radios mb-0">
                                <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>     
                    </div>
                    {!! Form::close() !!} 
                </div>
            </div>
            <div>
                <div class="col-lg-12 col-sm-12">
                    <div class="fixed-table-toolbar">
                        <div class="bars pull-left">
                           <div class="toolbar">
                           </div>
                        </div>
                        <div class="columns columns-right pull-right">
                           <button class="btn btn-primary" type="button" name="refresh" title="Refresh"  data-toggle="modal" data-target="#exampleModal"><i class="fa fa-edit"></i> Editar Seleção</button>
                        </div>
                        <div class="pull-left search"><input class="form-control" type="text" placeholder="Search"></div>
                     </div>
                     {{ $fontes->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}   
                    <table id="bootstrap-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><input style="-webkit-appearance: auto;" class="" type="checkbox" name="is_active" value="true"></th>
                                <th>Estado</th>
                                <th>Regional</th>
                                <th>Cidade</th>
                                <th>Nome</th>
                                <th>URL</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Estado</th>
                                <th>Regional</th>
                                <th>Cidade</th>
                                <th>Nome</th>
                                <th>URL</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($fontes as $site)
                                <tr>
                                    <td class="bs-checkbox"><input style="-webkit-appearance: auto;" data-index="1" name="btSelectItem" type="checkbox"></td>
                                    <td>{!! $site->estado->nm_estado ?? '' !!}</td>
                                    <td>{!! ($site->cidade and $site->cidade->regional) ? $site->cidade->regional->descricao : '' !!}</td>
                                    <td>{!! $site->cidade->nm_cidade ?? '' !!}</td>
                                    <td>{{ $site->nome }}</td>
                                    <td>{{ $site->url }}</td>
                                    <td class="text-center" style="width: 300px;">
                                        <a title="Coletas" href="{{ url('fonte-web/coletas', $site->id) }}" class="btn btn-info btn-link btn-icon"> <i class="fa fa-area-chart fa-2x "></i></a>
                                        <a title="Estatísticas" href="{{ url('fonte-web/estatisticas', $site->id) }}" class="btn btn-warning btn-link btn-icon"> <i class="fa fa-bar-chart fa-2x"></i></a>
                                        <a title="Editar" href="{{ route('fonte-web.edit', $site->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <form class="form-delete" style="display: inline;" action="{{ route('fonte-web.destroy',$site->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button title="Excluir" type="submit" class="btn btn-danger btn-link btn-icon button-remove" title="Delete">
                                                <i class="fa fa-times fa-2x"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
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
