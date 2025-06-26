@forelse ($dados as $noticia)
    <div class="card mb-3 shadow-sm noticia-card"
        data-noticia-id="{{ $noticia->id }}"
        data-tipo="{{ $noticia->tipo }}"
        data-titulo="{{ strtolower($noticia->titulo) }}"
        data-texto="{{ strip_tags($noticia->sinopse) }}"
        data-tags="{{ $noticia->tags ?? '' }}">

        <div class="card-body" style="border: 1px solid #ededed; border-left: 5px solid gray; border-radius: 8px;">

            <div class="row">
                <div class="col-md-12">
                    <div class="float-left">
                        <div class="form-check">
                            <div class="form-check">
                                <label class="form-check-label" for="check_{{ $noticia->id }}">
                                    <input class="form-check-input" type="checkbox" name="check_{{ $noticia->id }}" id="check_{{ $noticia->id }}" value="{{ $noticia->id }}" checked>
                                    <span class="form-check-sign"></span>   
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="float-left">
                        <h6 class="mb-2">{{ $noticia->titulo }}</h6>
                        <p class="mb-1">{{ $noticia->data_formatada }} {{ $noticia->fonte }} {{ $noticia->pagina ? ' - Página '.$noticia->pagina : '' }}</p>
                        <span class="">
                            @switch($noticia->tipo)
                                @case('web') <i class="fa fa-globe"></i> Web @break
                                @case('tv') <i class="fa fa-television"></i> TV @break
                                @case('radio') <i class="fa fa-volume-up"></i> Rádio @break
                                @case('impresso') <i class="fa fa-newspaper-o"></i> Impressos @break
                                @default 📄
                            @endswitch
                        </span>
                    </div>
                    <span class="badge bg-success pull-right">R$ {{ ($noticia->valor_retorno) ? number_format($noticia->valor_retorno, 2, ',', '.') : 0,00 }}</span>
                </div>
                <div class="col-md-12 mt-2">
                    <div class="noticia-conteudo">
                        @if(!empty($noticia->url_noticia))   
                            <p class="mb-1"><strong>Link:</strong>                 
                                <a href="{{ $noticia->url_noticia }}" target="_blank" style="color: #0d6efd;" class="text-decoration-none">{{ $noticia->url_noticia }}</a>
                            </p>
                        @endif
                        <p class="mb-0 mt-0"><strong>Sinopse:</strong> {!! $noticia->sinopse !!}</p>
                        <div class="pull-right">
                            @if($noticia->tipo == 'web')
                                <a href="{{ url('noticia/web/'.$noticia->id.'/editar') }}" target="_blank" class="btn btn-primary btn-sm btn-editar-noticia me-1" title="Editar notícia">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="{{ url('noticia/web/importar-imagem', $noticia->id) }}" class="btn btn-warning btn-sm btn-upload-imagem me-1" title="Upload de imagem">
                                    <i class="fa fa-picture-o"></i>
                                </a>
                            @elseif($noticia->tipo == 'impresso')
                                <a href="{{ url('noticia-impressa/'.$noticia->id.'/editar') }}" target="_blank" class="btn btn-primary btn-sm btn-editar-noticia me-1" title="Editar notícia">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endif
                            <a href="{{ url('relatorios/'.$noticia->tipo.'/pdf/'.$noticia->id) }}" class="btn btn-danger btn-sm btn-excluir-noticia" title="Gerar PDF">
                                <i class="fa fa-file-pdf-o"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info">Nenhum resultado encontrado.</div>
@endforelse