@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-file-text-o ml-3"></i> Pautas 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('pautas') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa  fa-table"></i> Pautas</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm-pauta', 'class' => 'form-horizontal', 'url' => ['pauta']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Cliente <span class="text-danger">Obrigatório</span></label>
                                        <select class="form-control select2" name="cliente_id" id="cliente_id" required>
                                            <option value="">Selecione um cliente</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{!! $cliente->id !!}">
                                                    {!! $cliente->pessoa->nome !!}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="sinopse">Texto <span class="text-danger">Obrigatório</span></label>
                                    <div class="form-group">
                                        <textarea class="form-control" name="descricao" id="descricao" rows="5" required></textarea>
                                    </div>
                                </div>
                            </div>     
                        </div>
                        <div class="text-center ml-2 mr-2">
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                            <a href="{{ url()->previous() }}" class="btn btn-danger ml-2"><i class="fa fa-times"></i> Cancelar</a>
                        </div>
                    {!! Form::close() !!}
                </div>
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
    </script>
@endsection