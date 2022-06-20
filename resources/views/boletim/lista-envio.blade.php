@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-9">
                    <h4 class="card-title ml-2">
                        <i class="fa fa-file-o"></i> Boletim 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $boletim->titulo }}
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Lista de Envio
                    </h4>
                </div>
                <div class="col-md-3">
                    <a href="{{ url('boletins') }}" class="btn btn-primary pull-right"><i class="fa fa-file-o"></i> Boletins</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            {!! Form::open(['id' => 'frm_user_create', 'url' => ['boletim/enviar/lista']]) !!}
                <input type="hidden" name="id" value="{{ $boletim->id }}">
                <div class="row px-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <label class="form-check-label d-block mb-3">
                                <input class="form-check-input" type="checkbox" name="todos" id="todos" value="todos" checked="checked">
                                    TODOS
                                <span class="form-check-sign"></span>
                            </label>
                            @foreach($lista_email as $key => $email)
                                <label class="form-check-label d-block mb-3 txt-black">
                                    <input class="form-check-input" type="checkbox" name="emails[]" value="{{ $email['email'] }}" checked="checked">
                                        {{ $email['nome'] }} ({{ $email['email'] }})
                                    <span class="form-check-sign"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>     
                    <div class="col-md-6">
                        <div class="alert alert-default alert-with-icon" data-notify="container">
                            <span data-notify="icon" class="ti-pie-chart">
                                <i class="fa fa-send"></i>
                            </span>
                            <span data-notify="message">
                                <strong class="d-block">Atenção!</strong>
                                O boletim será enviado para todos os emails desta lista. 
                                Caso deseje remover algum dos emails da lista de envio atual, basta desmarcar o endereço. 
                                No caso de uma remoção definitiva, entre em contato com o suporte.
                            </span>
                        </div>
                        <button type="submit" class="btn btn-success btn-send-mail"><i class="fa fa-send"></i> Enviar</button>
                    </div> 
                </div>  
            {!! Form::close() !!}  
        </div>
    </div>
</div> 
@endsection