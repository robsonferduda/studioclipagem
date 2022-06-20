@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2"><i class="nc-icon nc-briefcase-24"></i> Clientes</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('client/create') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Novo</a>
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
                      <th>Nome</th>
                      <th>Email</th>
                      <th class="disabled-sorting text-center">Conexões</th>
                      <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th class="disabled-sorting text-center">Conexões</th>
                        <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($clientes as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->email }}</td>
                            <td class="text-center">
                                @if(count($c->fbAccounts))
                                    <a title="Contas do Facebook" href="{{ url('client/accounts/facebook',$c->id) }}" class="btn btn-primary btn-link btn-icon btn-social btn-facebook"><i  class="fa fa-plug font-25"></i></a>
                                @endif
                                <a href="https://studiosocial.app/login/facebook/client/{{ $c->id }}" class="btn btn-social btn-facebook">
                                    <i class="fa fa-facebook fa-fw"></i> CONECTAR Facebook
                                </a>
                            </td>
                            <td class="text-center">
                                <a title="Facebook" href="{{ url('monitoramento/media/facebook') }}" class="btn btn-info btn-link btn-icon"><i class="fa fa-facebook font-25"></i></a>                                
                                <a title="Termos do Cliente" href="{{ url('terms/client',$c->id) }}" class="btn btn-info btn-link btn-icon"><i class="fa fa-font font-25"></i></a>
                                <a title="Hashtags do Cliente" href="{{ url('client/hashtags',$c->id) }}" class="btn btn-success btn-link btn-icon"><i class="fa fa-hashtag font-25"></i></a>
                                <a title="Páginas dos Concorrentes" href="{{ url('cliente/paginas-associadas',$c->id) }}" class="btn btn-info btn-link btn-icon"><i class="fa fa-at font-25"></i></a>
                                <a title="Emails de Comunicação" href="{{ url('client/emails',$c->id) }}" class="btn btn-danger btn-link btn-icon"><i class="fa fa-envelope font-25"></i></a>
                                <a title="Dados do Usuário" href="{{ url('client',$c->id) }}" class="btn btn-warning btn-link btn-icon"><i class="nc-icon nc-circle-10 font-25"></i></a>
                                <a title="Editar" href="{{ route('client.edit',$c->id) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <form class="form-delete" style="display: inline;" action="{{ route('client.destroy',$c->id) }}" method="POST">
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
@endsection