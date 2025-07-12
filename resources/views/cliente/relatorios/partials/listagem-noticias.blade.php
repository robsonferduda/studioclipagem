{{-- Lista de notícias por tipo de mídia --}}
@if(isset($noticias) && !empty($noticias))
    @php
        $totalNoticias = collect($noticias)->sum(function($tipoNoticias) {
            return count($tipoNoticias);
        });
    @endphp

    @if($totalNoticias > 0)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Notícias Encontradas ({{ $totalNoticias }})</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selecionarTodas()">
                    <i class="fa fa-check-square-o"></i> Selecionar Todas
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary ml-2" onclick="deselecionarTodas()">
                    <i class="fa fa-square-o"></i> Desmarcar Todas
                </button>
            </div>
        </div>

        {{-- Notícias Web --}}
        @if(isset($noticias['web']) && count($noticias['web']) > 0)
            @include('cliente.relatorios.partials.tipo-midia', [
                'tipo' => 'web',
                'titulo' => 'Notícias Web',
                'icone' => 'fa-globe',
                'corCard' => 'border-primary',
                'noticias' => $noticias['web']
            ])
        @endif

        {{-- Notícias TV --}}
        @if(isset($noticias['tv']) && count($noticias['tv']) > 0)
            @include('cliente.relatorios.partials.tipo-midia', [
                'tipo' => 'tv',
                'titulo' => 'Notícias TV',
                'icone' => 'fa-television',
                'corCard' => 'border-danger',
                'noticias' => $noticias['tv']
            ])
        @endif

        {{-- Notícias Rádio --}}
        @if(isset($noticias['radio']) && count($noticias['radio']) > 0)
            @include('cliente.relatorios.partials.tipo-midia', [
                'tipo' => 'radio',
                'titulo' => 'Notícias Rádio',
                'icone' => 'fa-volume-up',
                'corCard' => 'border-success',
                'noticias' => $noticias['radio']
            ])
        @endif

        {{-- Notícias Impressas --}}
        @if(isset($noticias['impresso']) && count($noticias['impresso']) > 0)
            @include('cliente.relatorios.partials.tipo-midia', [
                'tipo' => 'impresso',
                'titulo' => 'Notícias Impressas',
                'icone' => 'fa-newspaper-o',
                'corCard' => 'border-warning',
                'noticias' => $noticias['impresso']
            ])
        @endif
    @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Nenhuma notícia encontrada para os critérios informados.
        </div>
    @endif
@else
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> Use os filtros acima para pesquisar notícias.
    </div>
@endif

<script>
// Funções para manipulação das notícias
function selecionarTodas() {
    $('.selecionar-noticia').prop('checked', true);
    $('.selecionar-todas-tipo').prop('checked', true);
    atualizarContadores();
}

function deselecionarTodas() {
    $('.selecionar-noticia').prop('checked', false);
    $('.selecionar-todas-tipo').prop('checked', false);
    atualizarContadores();
}

function selecionarTodasTipo(tipo, checkbox) {
    $('.selecionar-noticia[data-tipo="' + tipo + '"]').prop('checked', checkbox.checked);
    atualizarContadores();
}

function atualizarContadores() {
    var totalSelecionadas = $('.selecionar-noticia:checked').length;
    $('#totalSelecionadas').text(totalSelecionadas);
    $('#qtdSelecionadasBtn').text(totalSelecionadas);
}

function visualizarNoticia(id, tipo) {
    // Implementar modal de visualização
    alert('Visualizar notícia ' + id + ' do tipo ' + tipo);
}

function editarNoticia(id, tipo) {
    // Implementar modal de edição
    alert('Editar notícia ' + id + ' do tipo ' + tipo);
}

function excluirNoticia(vinculoId, tipo) {
    if (confirm('Tem certeza que deseja excluir esta notícia?')) {
        var host = $('meta[name="base-url"]').attr('content');
        
        $.ajax({
            url: host + '/cliente/relatorios/excluir-noticia',
            type: 'POST',
            data: {
                vinculo_id: vinculoId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Notícia excluída com sucesso!');
                    // Recarregar lista
                    $('#btn-pesquisar').click();
                } else {
                    alert('Erro ao excluir notícia: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao excluir notícia:', error);
                alert('Erro ao excluir notícia. Tente novamente.');
            }
        });
    }
}
</script> 