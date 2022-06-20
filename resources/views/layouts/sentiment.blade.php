<h3>
    @switch($media['sentiment'])
        @case(-1)
                <i class="fa fa-frown-o text-danger"></i>
                <a href="{{ url('media/'.$media['id'].'/tipo/'.$media['type_message'].'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                <a href="{{ url('media/'.$media['id'].'/tipo/'.$media['type_message'].'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>
            @break
        @case(0)
                <a href="{{ url('media/'.$media['id'].'/tipo/'.$media['type_message'].'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a> 
                <i class="fa fa-ban text-primary"></i>
                <a href="{{ url('media/'.$media['id'].'/tipo/'.$media['type_message'].'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>                                                
            @break
        @case(1)
                <a href="{{ url('media/'.$media['id'].'/tipo/'.$media['type_message'].'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a>
                <a href="{{ url('media/'.$media['id'].'/tipo/'.$media['type_message'].'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                <i class="fa fa-smile-o text-success"></i>
            @break                                            
    @endswitch

    <a title="Excluir Mídia" href="{{ url('media/'.$media['id'].'/tipo/'.$media['type_message'].'/excluir') }}" class="btn btn-danger btn-link btn-icon pull-right btn-delete-media"><i class="fa fa-trash fa-2x"></i></a>
</h3>