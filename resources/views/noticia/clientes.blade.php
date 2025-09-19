@forelse($noticia->clientes as $cliente)
    <p class="mb-2">
        <span>{{ $cliente->nome }}</span>
        <span>
            {{ ($cliente->pivot->area) ? 
                " - ".App\Models\ClienteArea::where('cliente_id', $cliente->pivot->cliente_id)
                ->where('area_id', $cliente->pivot->area)
                ->first()
                ->area->descricao : '' 
            }}
        </span>
        <a href="#" class="btn-sentimento" 
           data-noticia-id="{{ $cliente->pivot->noticia_id }}" 
           data-tipo-id="{{ $cliente->pivot->tipo_id }}" 
           data-cliente-id="{{ $cliente->pivot->cliente_id }}" 
           data-sentimento="-1">
            <i class="fa fa-frown-o {{ $cliente->pivot->sentimento == -1 ? 'text-danger' : 'op-2' }}"></i>
        </a>

        <a href="#" class="btn-sentimento" 
           data-noticia-id="{{ $cliente->pivot->noticia_id }}" 
           data-tipo-id="{{ $cliente->pivot->tipo_id }}" 
           data-cliente-id="{{ $cliente->pivot->cliente_id }}" 
           data-sentimento="0">
            <i class="fa fa-ban {{ $cliente->pivot->sentimento == 0 ? 'text-primary' : 'op-2' }}"></i>
        </a>

        <a href="#" class="btn-sentimento" 
           data-noticia-id="{{ $cliente->pivot->noticia_id }}" 
           data-tipo-id="{{ $cliente->pivot->tipo_id }}" 
           data-cliente-id="{{ $cliente->pivot->cliente_id }}" 
           data-sentimento="1">
            <i class="fa fa-smile-o {{ $cliente->pivot->sentimento == 1 ? 'text-success' : 'op-2' }}"></i>
        </a>
        <span class="text-danger btn-excluir-noticia" 
               data-pivot-id="{{ $cliente->pivot->id }}" 
               data-tipo-id="{{ $cliente->pivot->tipo_id }}"
               data-noticia-id="{{ $cliente->pivot->noticia_id }}">Remover Cliente</span>
    </p>
@empty
    <p class="text-danger mb-1">Nenhum cliente associado à notícia</p>
@endforelse