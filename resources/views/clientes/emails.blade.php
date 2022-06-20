@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-9">
                    <h4 class="card-title ml-2">
                        <i class="nc-icon nc-briefcase-24"></i> Clientes
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $client->name }} 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emails de Comunicação 
                    </h4>
                </div>
                <div class="col-md-3">
                    <a href="{{ url('clientes') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Clientes</a>
                    <button class="btn btn-primary pull-right mr-2" data-toggle="modal" data-target="#addEmail"><i class="fa fa-envelope"></i> Cadastrar</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
            <p><i class="nc-icon nc-alert-circle-i"></i> Somente os endereços com situação <strong>Ativo</strong> receberão notificações. Clique sobre a <strong>Situação</strong> para alterar seu valor.</p>
                <table class="table">
                    <thead class="">
                        <tr>
                            <th>Email</th>
                            <th class="center">Situação</th>
                            <th class="center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->emails->sortBy('ds_email') as $email)
                            <tr>
                                <td>{{ $email->ds_email }}</td>
                                <td class="text-center">
                                    <a href="{{ url('email/situacao', $email->id) }}">{!! ($email->status) ? '<span class="badge badge-pill badge-success">ATIVO</span>' : '<span class="badge badge-pill badge-danger">INATIVO</span>' !!}</a>
                                </td>
                                <td class="center">
                                    <form class="form-delete" style="display: inline;" action="{{ route('email.destroy',$email->id) }}" method="POST">
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
                                <td class="center" colspan="3">Nenhum email cadastrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>  
            </div>         
        </div>
    </div>
</div> 
<div class="modal fade modal-primary" id="addEmail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title" id="myModalLabel"><i class="fa fa-envelope"></i> <strong> Cadastrar Email</strong></h5>
            </div>
            {!! Form::open(['id' => 'frm_add_email', 'url' => ['email']]) !!}
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <div class="modal-body">
                    <div class="card-body">            
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">Obrigatório</span></label>
                                    <input type="text" class="form-control" name="ds_email" id="ds_email" required="true">
                                </div>
                            </div>
                        </div>         
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                    <button data-dismiss="modal" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            {!! Form::close() !!} 
        </div>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('#frm_add_email').validate();
    });
</script>
@endsection