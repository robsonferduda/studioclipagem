@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2"><i class="fa fa-shield"></i> Auditoria</h4>
                </div>
                <div class="col-md-6">
                   
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                <table id="kt_server_side_datatable" aria-describedby="kt_datatable_info" class="table table-striped table-bordered dataTable" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Nível</th>
                            <th>Usuário</th>
                            <th class="text-center">Operação</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    {{--    @foreach($audits as $audit)
                            <tr>
                                <td>{{ date('d/m/Y H:i:s', strtotime($audit->created_at)) }}</td>
                                <td>
                                    @if($audit->user and $audit->user->roles())
                                        @forelse($audit->user->roles()->get() as $role)
                                            <span class="badge badge-{{ $role->display_color }}">{{ $role->display_name }}</span>
                                        @empty
                                            Nenhum perfil associado
                                        @endforelse
                                    @endif
                                </td>
                                <td>{{ ($audit->user) ? $audit->user->name : 'Usuário não identificado' }}</td>
                                <td class="text-center">{{ ($audit->user) ? $audit->event : 'Evento não identificado' }}</td>
                                <td class="text-center">
                                    <a href="{{ url('auditoria/detalhes', $audit->id) }}"><i class="fa fa-eye"></i> Ver</a>
                                </td>
                            </tr>
                        @endforeach --}}
                    </tbody>
                </table>   
            </div>        
        </div>
    </div>
</div> 
@endsection
{{-- Styles Section --}}
@section('style')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection

{{-- Scripts Section --}}
@section('script')
    
    <script type="text/javascript">
    $(document).ready(function() {
        var table = $('#kt_server_side_datatable').DataTable( {
            "processing": true,
            "serverSide": true,
            "order": [[ 0, "desc" ]],
            "bFilter": false,
            "ajax":{
                "url": "{{ url('auditoria') }}",
                "dataType": "json",
                "type": "GET"                
            },
            "columns": [            
                { data: "data" },
                { 
                    "data": "nivel", 
                    render: function ( data, type, row ) {

                        let td = '';
                        $.each(data, function( index, value ) {
                            td += '<span class="badge badge-'+value.display_color+'">'+value.display_name+'</span>';
                        });

                        return td;
                    }
                },
                { data: "usuario" },
                { data: "operacao" },               
                {data: "acoes", orderable: false, searchable: false},
            ]    
        } );
    } );
    </script>
    {{-- vendors --}}
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
@endsection
