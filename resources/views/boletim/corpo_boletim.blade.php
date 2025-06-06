<div class="col-md-12">
    <span class="pull-right mr-3">Total de notícias: {{ count($noticias_impresso) + count($noticias_web) + count($noticias_radio) + count($noticias_tv) }}</span>
    @if(count($noticias_impresso) > 0)
        <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-newspaper-o"></i> Clipagens de Jornal</p>
    @endif
    @foreach($noticias_impresso as $key => $noticia)
        <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
            <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia['titulo']) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
            <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['dt_clipagem'])) }}</p>
            <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia['fonte'] }}</p>
            @if($noticia['secao'])
                <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ ($noticia['secao']) ? $noticia['secao'] : 'Não informado' }}</p>
            @endif
            <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
            <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Veja</a></p>
        </div>
    @endforeach

    @if(count($noticias_web) > 0)
        <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-globe"></i> Clipagens de Web</p>
    @endif
    @foreach($noticias_web as $key => $noticia)
        <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
            <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia['titulo']) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
            <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
            <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia['fonte'] }}</p>
            @if($noticia['secao'])
                <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ ($noticia['secao']) ? $noticia['secao'] : 'Não informado' }}</p>
            @endif
            <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
            <p style="margin-bottom: 0px;"><strong>Link:</strong><a href="{{ $noticia['url_noticia'] }}" target="_blank"> Acesse</a></p>
            <p style="margin-bottom: 10px;"><strong>Print:</strong><a href="{{ asset($noticia['path_midia']) }}" target="_blank"> Veja</a></p>
        </div>
    @endforeach

    @if(count($noticias_tv) > 0)
        <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-tv"></i> Clipagens de TV</p>
    @endif
    @foreach($noticias_tv as $key => $noticia)
        <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
            <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_noticia)) }}</p>
            <p style="margin-bottom: 0px;"><strong>Emissora:</strong> {{ $noticia->emissora->nome_emissora }}</p>
            <p style="margin-bottom: 0px;"><strong>Programa:</strong> {{ ($noticia->programa) ? $noticia->programa->nome_programa : 'Não informado' }}</p>
            <p style="margin-bottom: 0px;"><strong>Duração:</strong> {{ ($noticia->duracao) ? $noticia->duracao : 'Não informado' }}</p>
            <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
            <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('video/noticia-tv/'.$noticia->ds_caminho_video) }}" target="_blank">Assista</a></p>
        </div>
    @endforeach


    @if(count($noticias_radio) > 0)
        <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-volume-up"></i> Clipagens de Rádio</p>
    @endif
    @foreach($noticias_radio as $key => $noticia)
        <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
            <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_clipagem)) }}</p>
            <p style="margin-bottom: 0px;"><strong>Emissora:</strong> {{ $noticia->emissora->nome_emissora }}</p>
            <p style="margin-bottom: 0px;"><strong>Programa:</strong> {{ ($noticia->programa) ? $noticia->programa->nome_programa : 'Não informado' }}</p>
            <p style="margin-bottom: 0px;"><strong>Duração:</strong> {{ ($noticia->duracao) ? $noticia->duracao : 'Não informado' }}</p>
            <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
            <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('audio/noticia-radio/'.$noticia->ds_caminho_audio) }}" target="_blank">Ouça</a></p>
        </div>
    @endforeach
</div> 