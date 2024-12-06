@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('emissoras/'.$tipo.'/novo') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            
                <div class="col-md-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['emissoras/'.$tipo]]) !!}
                        <div class="form-group w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control select2" name="cd_estado" id="cd_estado">
                                            <option value="">Selecione um estado</option>
                                            @foreach($estados as $estado)
                                                <option value="{{ $estado->cd_estado }}" {{ ($cd_estado == $estado->cd_estado) ? 'selected' : '' }}>{{ $estado->nm_estado }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                            <option value="">Selecione uma cidade</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Emissora</label>
                                        <input type="text" class="form-control" name="descricao" id="descricao" placeholder="Emissora" value="{{ $descricao }}">
                                    </div>
                                </div>
                                <div class="col-md-2 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mt-4"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>    
                        </div>
                    {!! Form::close() !!} 
                </div>
            
            <div class="col-md-12">
                <p><strong>Total de registros</strong>: {{ $emissoras->total() }} </p>
                @if($emissoras->total())
                    <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Emissora</th>
                                <th>URL</th>
                                <th class="disabled-sorting text-center">Gravação</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Estado</th>
                                <th>Cidade</th>
                                <th>Emissora</th>
                                <th>URL</th>
                                <th class="disabled-sorting text-center">Gravação</th>
                                <th class="disabled-sorting text-center">Ações</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($emissoras as $emissora)
                                <tr>
                                    <td>{{ ($emissora->estado) ? $emissora->estado->sg_estado : 'Não Informado' }}</td>
                                    <td>{{ ($emissora->cidade) ? $emissora->cidade->nm_cidade : 'Não Informado' }}</td>
                                    <td>{{ $emissora->nome_emissora }}</td>
                                    <td>{{ $emissora->url_stream }}</td>
                                    <td class="center">
                                        <a href="{{ url('emissora/'.$emissora->id.'/transcricao/atualiza') }}">{!! ($emissora->gravar) ? '<span class="badge badge-pill badge-success">SIM</span>' : '<span class="badge badge-pill badge-danger">NÃO</span>' !!}</a>
                                    </td>
                                    <td class="center">
                                        <a title="Editar" href="{{ route('emissora.edit',$emissora->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        @if(count($emissora->horarios))
                                            <a title="Horários de Coleta" href="{{ url('radio/emissora/'.$emissora->id.'/horarios') }}" class="btn btn-warning btn-link btn-icon"><i class="nc-icon nc-time-alarm font-25"></i></a>
                                        @else
                                            <a title="Horários de Coleta" href="{{ url('radio/emissora/'.$emissora->id.'/horarios') }}" class="btn btn-default btn-link btn-icon"><i class="nc-icon nc-time-alarm font-25"></i></a>
                                        @endif
                                        
                                        
                                        <form class="form-delete" style="display: inline;" action="{{ route('emissora.destroy',$emissora->id) }}" method="POST">
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
                    {{ $emissoras->onEachSide(1)->appends(['descricao' => $descricao, 'codigo' => $codigo])->links('vendor.pagination.bootstrap-4') }}
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

        });

        $(function() {
            var estado = $("#estado").val();
            
            if(estado){
                $("#estado").trigger('change');
            }
        });
    </script>
@endsection