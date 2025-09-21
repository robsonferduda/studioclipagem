@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_monitoramento_editar', 'url' => ['midias-sociais/monitoramentos/'.$monitoramento->id], 'method' => 'patch']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-3">
                            <i class="fa fa-hashtag"></i> Mídias Sociais
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Editar Monitoramento
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('midias-sociais/monitoramentos') }}" class="btn btn-primary pull-right" style="margin-right: 12px;">
                            <i class="fa fa-list"></i> Voltar para Lista
                        </a>
                        <a href="{{ url('midias-sociais/posts?monitoramento_id='.$monitoramento->id) }}" class="btn btn-info pull-right mr-1">
                            <i class="fa fa-list"></i> Ver Posts
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.mensagens')
                    </div>
                </div>
                
                <!-- Estatísticas Rápidas -->
                <div class="row mr-1 ml-1">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h4 class="mb-0">{{ $estatisticas['total_posts'] ?? 0 }}</h4>
                                    <small>Posts Coletados</small>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="mb-0">{{ $estatisticas['posts_hoje'] ?? 0 }}</h4>
                                    <small>Posts Hoje</small>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="mb-0">{{ $estatisticas['ultima_coleta'] ? \Carbon\Carbon::parse($estatisticas['ultima_coleta'])->format('d/m/Y') : 'Nunca' }}</h4>
                                    <small>Última Coleta</small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge badge-{{ $monitoramento->status == 'ativo' ? 'success' : ($monitoramento->status == 'pausado' ? 'warning' : 'danger') }} p-2">
                                        {{ strtoupper($monitoramento->status) }}
                                    </span>
                                    <br><small>Status Atual</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informações Básicas -->
                <div class="row mr-1 ml-1">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fa fa-info-circle"></i> Informações Básicas</h6>
                        <hr>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome do Monitoramento <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="nome" id="nome" 
                                value="{{ $monitoramento->nome }}" required>
                            <small class="form-text text-muted">Um nome descritivo para identificar este monitoramento</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control select2" name="cliente_id" id="cliente_id" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ $cliente->id == $monitoramento->cliente_id ? 'selected' : '' }}>
                                        {{ $cliente->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descrição (opcional)</label>
                            <textarea class="form-control" name="descricao" id="descricao" rows="3">{{ $monitoramento->descricao }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Seleção de Mídias -->
                <div class="row mr-1 ml-1">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fa fa-share-alt"></i> Redes Sociais</h6>
                        <hr>
                    </div>
                    
                    <div class="col-md-12">
                        <p class="text-muted mb-3">Selecione as redes sociais que deseja monitorar:</p>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="tipos_midia[]" value="twitter" 
                                            {{ in_array('twitter', $monitoramento->tipo_midia_array ?? []) ? 'checked' : '' }}>
                                        <span class="form-check-sign"></span>
                                        <i class="fa fa-twitter text-info"></i> Twitter
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="tipos_midia[]" value="linkedin"
                                            {{ in_array('linkedin', $monitoramento->tipo_midia_array ?? []) ? 'checked' : '' }}>
                                        <span class="form-check-sign"></span>
                                        <i class="fa fa-linkedin text-primary"></i> LinkedIn
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="tipos_midia[]" value="facebook"
                                            {{ in_array('facebook', $monitoramento->tipo_midia_array ?? []) ? 'checked' : '' }}>
                                        <span class="form-check-sign"></span>
                                        <i class="fa fa-facebook text-primary"></i> Facebook
                                        <small class="badge badge-warning ml-2">Em Desenvolvimento</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="tipos_midia[]" value="instagram"
                                            {{ in_array('instagram', $monitoramento->tipo_midia_array ?? []) ? 'checked' : '' }}>
                                        <span class="form-check-sign"></span>
                                        <i class="fa fa-instagram text-danger"></i> Instagram
                                        <small class="badge badge-warning ml-2">Em Desenvolvimento</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Palavras-chave -->
                <div class="row mr-1 ml-1 mt-3">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fa fa-key"></i> Palavras-chave</h6>
                        <hr>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Lista de Palavras-chave <span class="text-danger">Obrigatório</span></label>
                            <textarea class="form-control" name="palavras_chave" id="palavras_chave" rows="4" required>{{ is_array($monitoramento->palavras_chave) ? implode(', ', $monitoramento->palavras_chave) : $monitoramento->palavras_chave }}</textarea>
                            <small class="form-text text-muted">
                                Separe cada palavra-chave por vírgula. O sistema buscará posts que contenham pelo menos uma dessas palavras.
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Idiomas</label>
                            <select class="form-control select2" name="idiomas[]" id="idiomas" multiple>
                                @foreach(['pt' => 'Português', 'en' => 'Inglês', 'es' => 'Espanhol', 'fr' => 'Francês'] as $codigo => $nome)
                                    <option value="{{ $codigo }}" 
                                        {{ in_array($codigo, $monitoramento->configuracoes['idiomas'] ?? []) ? 'selected' : '' }}>
                                        {{ $nome }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Deixe em branco para todos os idiomas</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="ativo" {{ $monitoramento->status == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="pausado" {{ $monitoramento->status == 'pausado' ? 'selected' : '' }}>Pausado</option>
                                <option value="inativo" {{ $monitoramento->status == 'inativo' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="card-footer text-center mb-3">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Salvar Alterações
                </button>
                <a href="{{ url('midias-sociais/monitoramentos') }}" class="btn btn-danger">
                    <i class="fa fa-times"></i> Cancelar
                </a>
                <button type="button" class="btn btn-warning btn-reset" onclick="resetarColetas()">
                    <i class="fa fa-refresh"></i> Resetar Coletas
                </button>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Select2 para campos de seleção múltipla
        $('.select2').select2({
            placeholder: 'Selecione...',
            allowClear: true
        });
        
        // Validação do formulário
        $('#frm_monitoramento_editar').on('submit', function(e) {
            var palavrasChave = $('#palavras_chave').val().trim();
            var tiposMidia = $('input[name="tipos_midia[]"]:checked').length;
            
            if (palavrasChave === '') {
                e.preventDefault();
                Swal.fire({
                    title: 'Campo Obrigatório',
                    text: 'Por favor, informe pelo menos uma palavra-chave para o monitoramento.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if (tiposMidia === 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Seleção Obrigatória',
                    text: 'Por favor, selecione pelo menos uma rede social para monitorar.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return false;
            }
        });
    });
    
    function resetarColetas() {
        Swal.fire({
            title: 'Resetar Coletas',
            text: 'Esta ação irá apagar todos os posts coletados até agora e reiniciar o monitoramento. Esta ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, resetar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var host = $('meta[name="base-url"]').attr('content');
                var token = $('meta[name="csrf-token"]').attr('content');
                
                $.ajax({
                    url: host + '/midias-sociais/monitoramentos/{{ $monitoramento->id }}/resetar',
                    type: 'POST',
                    data: {
                        "_token": token
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Resetado!', 'As coletas foram resetadas com sucesso.', 'success')
                                .then(() => location.reload());
                        }
                    },
                    error: function() {
                        Swal.fire('Erro', 'Não foi possível resetar as coletas', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
