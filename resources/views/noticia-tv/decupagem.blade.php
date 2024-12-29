@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Decupagem 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('tv/noticias') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
                    <a href="{{ url('tv/decupar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-file-word-o"></i> Decupar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Data Envio</th>
                        <th>Arquivo</th>
                        <th class="text-center">Notícias</th>
                        <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Data Envio</th>
                        <th>Arquivo</th>
                        <th class="text-center">Notícias</th>
                        <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($arquivos as $decupagem)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($decupagem->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td><a href="{{ asset('noticias-tv/decupagem/'.$decupagem->arquivo) }}">{{ $decupagem->arquivo }}</a></td>
                            <td class="text-center">{{ $decupagem->noticiasTV->count() }}</td>
                            <td class="text-center">
                               
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var token = $('meta[name="csrf-token"]').attr('content');
            var host =  $('meta[name="base-url"]').attr('content');

        });
    </script>
@endsection