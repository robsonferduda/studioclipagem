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
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Programas
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('radio/emissoras/programas/novo') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12 px-0">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['radio/emissoras/programas']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Emissora</label>
                                        <select class="form-control select2" name="emissora_id" id="emissora_id">
                                            <option value="">Selecione uma emissora</option>
                                            @foreach ($emissoras as $emissora)
                                                <option value="{{ $emissora->id }}" {{ ($emissora->id == $emissora_search) ? 'selected' : '' }}>{{ $emissora->nome_emissora }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Programa</label>
                                        <input type="text" class="form-control" name="nome" id="nome" placeholder="Programa" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <a href="{{ url('radio/emissoras/programas') }}" class="btn btn-warning btn-limpar"><i class="fa fa-refresh"></i> Limpar</a>
                                    <button type="submit" id="btn-find" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!} 
            </div>
            <div class="col-md-12">
                {{ $programas->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
                <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="">Emissora</th>
                            <th class="">Programa</th>
                            <th class="center">Início</th>
                            <th class="center">Término</th>
                            <th>Valor</th>
                            <th class="disabled-sorting text-center">Ações</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Emissora</th>
                            <th>Programa</th>
                            <th class="center">Início</th>
                            <th class="center">Término</th>
                            <th>Valor</th>
                            <th class="disabled-sorting text-center">Ações</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @forelse($programas as $programa)
                            <tr>
                                <td>{{ ($programa->emissora) ? $programa->emissora->nome_emissora : 'Não informado' }}</td>
                                <td>{{ $programa->nome_programa }}</td>
                                <td class="center">{{ date('H:i', strtotime($programa->hora_inicio)) }}</td>
                                <td class="center">{{ date('H:i', strtotime($programa->hora_fim)) }}</td>
                                <td>{{ ($programa->valor_segundo) ? "R$ ".$programa->valor_segundo : '--' }}</td>
                                <td class="text-center">
                                    <a title="Editar" href="{{ route('programa.edit',$programa->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                    <form class="form-delete" style="display: inline;" action="{{ route('programa.destroy',$programa->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button title="Excluir" type="submit" class="btn btn-danger btn-link btn-icon button-remove" title="Delete">
                                            <i class="fa fa-times fa-2x"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhum programa encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $programas->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}                
            </div>
        </div>
    </div>
</div> 
@endsection