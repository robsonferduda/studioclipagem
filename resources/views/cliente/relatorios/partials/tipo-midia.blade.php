{{-- Card para um tipo específico de mídia --}}
<div class="card mb-3 {{ $corCard }}">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fa {{ $icone }}"></i> {{ $titulo }} ({{ count($noticias) }})
            </h6>
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input selecionar-todas-tipo" type="checkbox" 
                           onchange="selecionarTodasTipo('{{ $tipo }}', this)">
                    <span class="form-check-sign"></span>
                    <small>Selecionar todas</small>
                </label>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="selecionar-todas-{{ $tipo }}" 
                                   onchange="selecionarTodasTipo('{{ $tipo }}', this)">
                        </th>
                        <th>Título</th>
                        <th>Veículo</th>
                        <th>Data</th>
                        @if($tipo == 'web')
                            <th>Link</th>
                        @elseif($tipo == 'tv' || $tipo == 'radio')
                            <th>Programa</th>
                            <th>Horário</th>
                        @elseif($tipo == 'impresso')
                            <th>Seção</th>
                            <th>Página</th>
                        @endif
                        <th>Área</th>
                        <th>Sentimento</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($noticias as $noticia)
                        <tr>
                            <td>
                                <input type="checkbox" class="selecionar-noticia" 
                                       data-tipo="{{ $tipo }}" 
                                       data-id="{{ $noticia['id'] ?? '' }}"
                                       data-vinculo-id="{{ $noticia['vinculo_id'] ?? '' }}"
                                       onchange="atualizarContadores()">
                            </td>
                            <td>
                                <strong>{{ $noticia['titulo'] ?? 'N/A' }}</strong>
                                @if(!empty($noticia['tags']))
                                    <br><small class="text-muted">
                                        <i class="fa fa-tags"></i> {{ $noticia['tags'] }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $noticia['veiculo'] ?? 'N/A' }}</td>
                            <td>{{ $noticia['data_formatada'] ?? 'N/A' }}</td>
                            
                            @if($tipo == 'web')
                                <td>
                                    @if(!empty($noticia['link']))
                                        <a href="{{ $noticia['link'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            @elseif($tipo == 'tv' || $tipo == 'radio')
                                <td>{{ $noticia['programa'] ?? 'N/A' }}</td>
                                <td>{{ $noticia['horario'] ?? 'N/A' }}</td>
                            @elseif($tipo == 'impresso')
                                <td>{{ $noticia['secao'] ?? 'N/A' }}</td>
                                <td>{{ $noticia['pagina'] ?? 'N/A' }}</td>
                            @endif
                            
                            <td>{{ $noticia['area'] ?? 'Sem área' }}</td>
                            <td>
                                @php
                                    $sentimento = $noticia['sentimento'] ?? 0;
                                    $classeSentimento = '';
                                    $iconeSentimento = '';
                                    $textoSentimento = '';
                                    
                                    if ($sentimento == 1) {
                                        $classeSentimento = 'text-success';
                                        $iconeSentimento = 'fa-smile-o';
                                        $textoSentimento = 'Positivo';
                                    } elseif ($sentimento == -1) {
                                        $classeSentimento = 'text-danger';
                                        $iconeSentimento = 'fa-frown-o';
                                        $textoSentimento = 'Negativo';
                                    } else {
                                        $classeSentimento = 'text-muted';
                                        $iconeSentimento = 'fa-meh-o';
                                        $textoSentimento = 'Neutro';
                                    }
                                @endphp
                                <span class="{{ $classeSentimento }}">
                                    <i class="fa {{ $iconeSentimento }}"></i>
                                    <small>{{ $textoSentimento }}</small>
                                </span>
                            </td>
                            <td>
                                @if(!empty($noticia['valor']) && $noticia['valor'] > 0)
                                    R$ {{ number_format($noticia['valor'], 2, ',', '.') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="visualizarNoticia('{{ $noticia['id'] }}', '{{ $tipo }}')"
                                            title="Visualizar">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            onclick="editarNoticia('{{ $noticia['id'] }}', '{{ $tipo }}')"
                                            title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    @if($tipo == 'web' || $tipo == 'impresso')
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="visualizarImagem('{{ $noticia['id'] }}', '{{ $tipo }}')"
                                                title="Ver Imagem">
                                            <i class="fa fa-image"></i>
                                        </button>
                                    @endif
                                    @if($tipo == 'radio')
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="reproduzirAudio('{{ $noticia['audio'] ?? '' }}')"
                                                title="Reproduzir Áudio">
                                            <i class="fa fa-play"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="excluirNoticia('{{ $noticia['vinculo_id'] }}', '{{ $tipo }}')"
                                            title="Excluir">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Função adicional para visualizar imagem
function visualizarImagem(id, tipo) {
    var host = $('meta[name="base-url"]').attr('content');
    var url = host + '/imagem/' + tipo + '/' + id;
    window.open(url, '_blank');
}

// Função adicional para reproduzir áudio
function reproduzirAudio(audioPath) {
    if (audioPath) {
        var host = $('meta[name="base-url"]').attr('content');
        var url = host + '/audio/' + audioPath;
        window.open(url, '_blank');
    } else {
        alert('Áudio não disponível');
    }
}
</script> 