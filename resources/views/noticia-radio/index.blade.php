@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('radio/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                @foreach($noticias as $noticia)
                    <div class="card ml-2 mr-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <p>{{ $noticia->cliente->pessoa->nome }}</p>
                                    <h6>{!! $noticia->emissora->ds_emissora ?? '' !!}</h6>
                                    <p>{!! $noticia->programa->nome ?? '' !!}</p>
                                    <p>{!! !empty($noticia->dt_noticia) ? date('d/m/Y', strtotime($noticia->dt_noticia)) : '' !!}</p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    
                                    <audio controls>
                                        <source src="{{ asset($noticia->arquivo) }}" type="audio/ogg">
                                        <source src="{{ asset($noticia->arquivo) }}" type="audio/mpeg">
                                      Your browser does not support the audio element.
                                      </audio>
                                    <div style="position: absolute; bottom: 5px; right: 5px;">
                                        <a title="Editar" href="{{ url('radio/noticias/'.$noticia->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                        <a title="Excluir" href="{{ url('radio/noticias/'.$noticia->id.'/remover') }}" class="btn btn-danger btn-link btn-icon"><i class="fa fa-trash fa-2x"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>            
            
            <div class="col-md-12">
                <!--
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Emissora</th>
                            <th>Programa</th>
                            <th>Data</th>
                            <th class="disabled-sorting text-center">Ações</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Cliente</th>
                            <th>Emissora</th>
                            <th>Programa</th>
                            <th>Data</th>
                            <th class="disabled-sorting text-center">Ações</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($noticias as $noticia)
                            <tr>
                                <td>{{ $noticia->cliente->pessoa->nome }}</td>
                                <td>{!! $noticia->emissora->ds_emissora ?? '' !!}</td>
                                <td>{!! $noticia->programa->nome ?? '' !!}</td>
                                <td>{!! !empty($noticia->dt_noticia) ? date('d/m/Y', strtotime($noticia->dt_noticia)) : '' !!}</td>
                                <td class="text-center">
                                    <a title="Editar" href="{{ url('radio/noticias/'.$noticia->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                    <a title="Excluir" href="{{ url('radio/noticias/'.$noticia->id.'/remover') }}" class="btn btn-danger btn-link btn-icon"><i class="fa fa-trash fa-2x"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                -->
            </div>
        </div>
    </div>
</div>
@endsection
