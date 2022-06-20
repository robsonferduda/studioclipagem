@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2"><i class="fa fa-cog"></i> Configurações </h4>
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
                <table class="table">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Opções</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cliente Padrão</td>
                            <td>Seleciona o cliente padrão ao logar no sistema</td>
                            <td><span class="cliente_atual">{{ $cliente->name }}</span></td>
                            <td><a title="Editar" class="btn btn-primary btn-link btn-icon config_cliente"><i class="fa fa-edit fa-2x"></i></a></td>
                        </tr>
                        <tr>
                            <td>Período Padrão</td>
                            <td>Período padrão em dias para a geração de relatórios</td>
                            <td><span class="periodo_atual">{{ $periodo }}</span> {{ ($periodo) > 1 ? 'dias' : 'dia' }}</td>
                            <td><a title="Editar" class="btn btn-primary btn-link btn-icon config_periodo"><i class="fa fa-edit fa-2x"></i></a></td>
                        </tr>
                        <tr>
                            <td>Postagens - Filtro por Regras</td>
                            <td>Define se as listas e totais de postagens são filtrados por regras ou não possuem filtros</td>
                            <td colspan="2">
                                <span class="flag_regras" data-value="{{ $flag_regras }}">
                                    @if($flag_regras)
                                        <span class="badge badge-pill badge-info">POSTAGENS FILTRADAS POR REGRAS</span>
                                    @else
                                        <span class="badge badge-pill badge-danger">POSTAGENS NÃO FILTRADAS</span>
                                    @endif
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>  
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