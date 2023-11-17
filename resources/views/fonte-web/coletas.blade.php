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
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Coletas
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('buscar-web') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias Web</a>
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Fontes Web</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-6 col-sm-12">
                    <h5><i class="fa fa-database" aria-hidden="true"></i> Dados Knewin</h5>
                    <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Notícia</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Data</th>
                                <th>Notícia</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @forelse($noticias_knewin as $noticia)
                                <tr>
                                    <td>{{ $noticia->data_cadastro }}</td>
                                    <td>{{ $noticia->titulo }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">Nenhuma notícia coletada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <h5><i class="fa fa-database" aria-hidden="true"></i> Dados Studio Clipagem</h5>
                    <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Notícia</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Data</th>
                                <th>Notícia</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($noticias as $noticia)
                                <tr>
                                    <td>{{ $noticia->dt_clipagem }}</td>
                                    <td>{{ $noticia->titulo }}</td>
                                </tr>
                            @endforeach
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

       
      
    });
</script>
@endsection
