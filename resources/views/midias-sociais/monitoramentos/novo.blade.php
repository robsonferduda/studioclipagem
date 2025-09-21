@extends('layouts.app')


@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_monitoramento_novo', 'url' => ['midias-sociais/monitoramentos']]) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-3">
                            <i class="fa fa-hashtag"></i> Mídias Sociais
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Novo Monitoramento
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('midias-sociais/monitoramentos') }}" class="btn btn-primary pull-right" style="margin-right: 12px;">
                            <i class="fa fa-list"></i> Voltar para Lista
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
                
                <!-- Informações Básicas -->
                <div class="row mr-1 ml-1">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fa fa-info-circle"></i> Informações Básicas</h6>
                        <hr>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome do Monitoramento <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Ex: Monitoramento Marca X" required>
                            <small class="form-text text-muted">Um nome descritivo para identificar este monitoramento</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control select2" name="cliente_id" id="cliente_id" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descrição (opcional)</label>
                            <textarea class="form-control" name="descricao" id="descricao" rows="3" 
                                placeholder="Descreva brevemente o objetivo deste monitoramento..."></textarea>
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
                                        <input class="form-check-input" type="checkbox" name="tipos_midia[]" value="twitter">
                                        <span class="form-check-sign"></span>
                                        <i class="fa fa-twitter text-info"></i> Twitter
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="tipos_midia[]" value="linkedin">
                                        <span class="form-check-sign"></span>
                                        <i class="fa fa-linkedin text-primary"></i> LinkedIn
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="tipos_midia[]" value="facebook">
                                        <span class="form-check-sign"></span>
                                        <i class="fa fa-facebook text-primary"></i> Facebook
                                        <small class="badge badge-warning ml-2">Em Desenvolvimento</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="tipos_midia[]" value="instagram">
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
                            <textarea class="form-control" name="palavras_chave" id="palavras_chave" rows="4" 
                                placeholder="Digite as palavras-chave separadas por vírgula&#10;Exemplo: marca, produto, empresa, nome do CEO" required></textarea>
                            <small class="form-text text-muted">
                                Separe cada palavra-chave por vírgula. O sistema buscará posts que contenham pelo menos uma dessas palavras.
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Idiomas</label>
                            <select class="form-control select2" name="idiomas[]" id="idiomas" multiple>
                                <option value="pt">Português</option>
                                <option value="en">Inglês</option>
                                <option value="es">Espanhol</option>
                                <option value="fr">Francês</option>
                            </select>
                            <small class="form-text text-muted">Deixe em branco para todos os idiomas</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Inicial</label>
                            <select class="form-control" name="status" id="status">
                                <option value="ativo">Ativo</option>
                                <option value="pausado">Pausado</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="card-footer text-center mb-3">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Criar Monitoramento
                </button>
                <a href="{{ url('midias-sociais/monitoramentos') }}" class="btn btn-danger">
                    <i class="fa fa-times"></i> Cancelar
                </a>
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
        $('#frm_monitoramento_novo').on('submit', function(e) {
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
        
        // Preview das palavras-chave
        $('#palavras_chave').on('blur', function() {
            var palavras = $(this).val().trim();
            if (palavras) {
                var arrayPalavras = palavras.split(',').map(function(palavra) {
                    return palavra.trim();
                }).filter(function(palavra) {
                    return palavra.length > 0;
                });
                
                if (arrayPalavras.length > 0) {
                    var preview = '<div class="mt-2"><small class="text-muted">Palavras-chave: </small>';
                    arrayPalavras.forEach(function(palavra) {
                        preview += '<span class="badge badge-primary mr-1">' + palavra + '</span>';
                    });
                    preview += '</div>';
                    
                    $('#palavras_chave').next('.form-text').after(preview);
                }
            }
        });
    });
</script>
@endsection
