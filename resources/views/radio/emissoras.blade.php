@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up"></i> Rádio 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras 
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
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['radio/emissoras']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label>Código</label>
                                        <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" value="">
                                    </div>
                                </div>
                                <div class="col-md-9 col-sm-12">
                                    <div class="form-group">
                                        <label>Emissora</label>
                                        <input type="text" class="form-control" name="descricao" id="descricao" placeholder="Emissora" value="">
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
            <div class="col-md-12">
                {{ $emissoras->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
                <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Cidade</th>
                            <th>Código</th>
                            <th>Emissora</th>
                            <th class="disabled-sorting text-center">Transcrição</th>
                            <th class="disabled-sorting text-center">Ações</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Estado</th>
                            <th>Cidade</th>
                            <th>Código</th>
                            <th>Emissora</th>
                            <th class="disabled-sorting text-center">Transcrição</th>
                            <th class="disabled-sorting text-center">Ações</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($emissoras as $emissora)
                            <tr>
                                <td>{{ $emissora->estado->sg_estado }}</td>
                                <td>{{ $emissora->cidade->nm_cidade }}</td>
                                <td>{{ $emissora->codigo }}</td>
                                <td>{{ $emissora->ds_emissora }}</td>
                                <td class="center">
                                    <a href="{{ url('radio/emissora/'.$emissora->id.'/transcricao/atualiza') }}">{!! ($emissora->fl_transcricao) ? '<span class="badge badge-pill badge-success">ATIVA</span>' : '<span class="badge badge-pill badge-danger">INATIVA</span>' !!}</a>
                                </td>
                                <td>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $emissoras->onEachSide(1)->links('vendor.pagination.bootstrap-4') }} 
            </div>
        </div>
    </div>
</div> 
@endsection