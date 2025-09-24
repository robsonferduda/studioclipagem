@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-hashtag"></i> Mídias Sociais
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Monitoramentos
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('midias-sociais/monitoramentos/novo') }}" class="btn btn-primary pull-right" style="margin-right: 12px;">
                        <i class="fa fa-plus"></i> Novo Monitoramento
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            
            <!-- Filtros -->
            <div class="row">
                <div class="col-md-12">
                    {!! Form::open(['id' => 'frm_filtro_monitoramentos', 'class' => 'form-horizontal', 'url' => ['midias-sociais/monitoramentos']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-filter"></i> Cliente</label>
                                        <select class="form-control select2" name="cliente_id" id="cliente_id">
                                            <option value="">Todos os clientes</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ (request('cliente_id') == $cliente->id) ? 'selected' : '' }}>
                                                    {{ $cliente->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo de Mídia</label>
                                        <select class="form-control" name="tipo_midia" id="tipo_midia">
                                            <option value="">Todas</option>
                                            <option value="twitter">Twitter</option>
                                            <option value="linkedin">LinkedIn</option>
                                            <option value="facebook">Facebook</option>
                                            <option value="instagram">Instagram</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="">Todos</option>
                                            <option value="ativo">Ativo</option>
                                            <option value="inativo">Inativo</option>
                                            <option value="pausado">Pausado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Nome do Monitoramento</label>
                                        <input type="text" class="form-control" name="nome" id="nome" placeholder="Digite o nome...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary mt-4 w-100"><i class="fa fa-search"></i> Filtrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>

            <!-- Listagem de Monitoramentos -->
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    @if(count($monitoramentos) > 0)
                        <p>Foram encontrados <strong>{{ count($monitoramentos) }}</strong> monitoramentos</p>
                    @else
                        <p>Nenhum monitoramento encontrado</p>
                    @endif
                    
                    @forelse($monitoramentos as $monitoramento)
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="d-flex align-items-start">
                                                    <div class="mr-3 mt-1">
                                                        <i class="fa fa-hashtag text-primary" style="font-size: 30px;"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1">
                                                            {{ $monitoramento->nome }}
                                                            <span class="badge badge-pill badge-{{ $monitoramento->status == 'ativo' ? 'success' : ($monitoramento->status == 'pausado' ? 'warning' : 'danger') }}">
                                                                {{ strtoupper($monitoramento->status) }}
                                                            </span>
                                                        </h5>
                                                        <p class="text-muted mb-1">
                                                            <strong>Cliente:</strong> {{ $monitoramento->cliente->nome }}
                                                        </p>
                                                        <p class="text-muted mb-1">
                                                            <strong>Mídias:</strong>
                                                            @foreach($monitoramento->tipo_midia_array as $tipo)
                                                                <span class="badge badge-info">{{ ucfirst($tipo) }}</span>
                                                            @endforeach
                                                        </p>
                                                        <p class="text-muted mb-1">
                                                            <strong>Palavras-chave:</strong> {{ is_array($monitoramento->palavras_chave) ? implode(', ', $monitoramento->palavras_chave) : $monitoramento->palavras_chave }}
                                                        </p>
                                                        <p class="text-muted mb-0">
                                                            <small>
                                                                Criado em {{ $monitoramento->data_criacao->format('d/m/Y \à\s H:i') }} | 
                                                                Última coleta: {{ $monitoramento->ultima_coleta ? $monitoramento->ultima_coleta->format('d/m/Y \à\s H:i') : 'Nunca' }}
                                                            </small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-right">
                                                    <div class="mb-2">
                                                        <span class="badge badge-light">{{ $monitoramento->posts_count }} posts coletados</span>
                                                    </div>
                                                    <div>
                                                        <a title="Ver Posts" href="{{ url('midias-sociais/posts?monitoramento_id='.$monitoramento->id) }}" class="btn btn-info btn-link btn-icon">
                                                            <i class="fa fa-list fa-2x"></i>
                                                        </a>
                                                        <a title="Editar" href="{{ url('midias-sociais/monitoramentos/'.$monitoramento->id.'/editar') }}" class="btn btn-primary btn-link btn-icon">
                                                            <i class="fa fa-edit fa-2x"></i>
                                                        </a>
                                                        <a title="Pausar/Ativar" href="#" class="btn btn-warning btn-link btn-icon btn-toggle-status" data-id="{{ $monitoramento->id }}">
                                                            <i class="fa fa-{{ $monitoramento->status == 'ativo' ? 'pause' : 'play' }} fa-2x"></i>
                                                        </a>
                                                        <a title="Excluir" href="#" class="btn btn-danger btn-link btn-icon btn-excluir" data-id="{{ $monitoramento->id }}">
                                                            <i class="fa fa-trash fa-2x"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="row">
                            <div class="col-lg-12 mt-0">
                                <div class="alert alert-info text-center">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Nenhum monitoramento encontrado</strong><br>
                                    Crie seu primeiro monitoramento para começar a coletar dados das redes sociais.
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        var host = $('meta[name="base-url"]').attr('content');
        var token = $('meta[name="csrf-token"]').attr('content');
        
        // Toggle status do monitoramento
        $(document).on('click', '.btn-toggle-status', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var $btn = $(this);
            
            Swal.fire({
                title: 'Alterar Status',
                text: 'Deseja pausar/ativar este monitoramento?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: host + '/midias-sociais/monitoramentos/' + id + '/toggle-status',
                        type: 'POST',
                        data: {
                            "_token": token
                        },
                        beforeSend: function() {
                            $btn.prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function() {
                            Swal.fire('Erro', 'Não foi possível alterar o status', 'error');
                        },
                        complete: function() {
                            $btn.prop('disabled', false);
                        }
                    });
                }
            });
        });
        
        // Excluir monitoramento
        $(document).on('click', '.btn-excluir', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            
            Swal.fire({
                title: 'Excluir Monitoramento',
                text: 'Esta ação não poderá ser desfeita. Todos os posts coletados também serão removidos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: host + '/midias-sociais/monitoramentos/' + id + '/excluir',
                        type: 'DELETE',
                        data: {
                            "_token": token
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Excluído!', 'Monitoramento removido com sucesso.', 'success')
                                    .then(() => location.reload());
                            }
                        },
                        error: function() {
                            Swal.fire('Erro', 'Não foi possível excluir o monitoramento', 'error');
                        }
                    });
                }
            });
        });
        
        // Select2 para campos de seleção
        $('.select2').select2({
            placeholder: 'Selecione...',
            allowClear: true
        });
    });
</script>
@endsection
