@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icon-big text-center icon-warning">
                                <i class="nc-icon nc-briefcase-24 text-info"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Clientes</p>
                                <p class="card-title"><a href="{{ url('clientes') }}">{{ $clientes }}</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <a href="{{ url('clientes/create') }}"><i class="fa fa-plus text-info"></i> Novo</a>                
                        </div>
                </div>
            </div>
        </div>  
        <div class="col-lg-3 col-md-3 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icon-big text-center icon-warning">
                                <i class="fa fa-facebook text-facebook"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Média Facebook</p>
                                <p class="card-title">{{ $media_facebook }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer ">
                    <hr>
                    <span class="text-info"><i class="fa fa-clock-o"></i> Atualizado em {{ date('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>  
        <div class="col-lg-3 col-md-3 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icon-big text-center icon-warning">
                                <i class="fa fa-instagram text-pink""></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Média Instagram</p>
                                <p class="card-title">{{ $media_instagram }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer ">
                    <hr>
                    <span class="text-info"><i class="fa fa-clock-o"></i> Atualizado em {{ date('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>    
        <div class="col-lg-3 col-md-3 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icon-big text-center icon-warning">
                                <i class="fa fa-twitter text-info"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Média de Tweets</p>
                                <p class="card-title"><a>{{ $media_twitter }}</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer ">
                    <hr>
                    <span class="text-info"><i class="fa fa-clock-o"></i> Atualizado em {{ date('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>   
    </div> 
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="custom-cloud" id='cloud'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <h6 class="mt-2 mb-3">
                                Termos Ativos
                                <a class="pull-right" href="{{ url('terms/client', session('cliente')['id']) }}"><i class="fa fa-plus"></i> Adicionar</a>
                            </h6>
                            @foreach($terms as $term)
                                <p>{{ $term->term }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>   
  
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <h6 class="mt-2 mb-3">
                                Hashtags Ativas
                                <a class="pull-right" href="{{ url('client/hashtags', session('cliente')['id']) }}"><i class="fa fa-plus"></i> Adicionar</a>
                            </h6>
                            @foreach($hashtags as $hashtag)
                                <p>#{{ $hashtag->hashtag }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        $('body').loader('show');

        var APP_URL = {!! json_encode(url('/')) !!};
        var tamanho = 0.02;

        fetch(APP_URL+'/nuvem-palavras/words', {
            method: 'GET', 
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        }).then(function(response) {
            return response.json();
        }).then(function(response){
            
            let words = [];

            $('body').loader('hide');
            const _token = $('meta[name="csrf-token"]').attr('content');

            Object.entries(response).forEach(element => {
                words.push(
                    {
                        text: element[0], 
                        weight: element[1],
                        html: {
                            class: 'cloud-word'
                        },                        
                    }
                );
            });

            let cloud = $('#cloud').jQCloud(words, {
                autoResize: true,
                classPattern: null,
                colors: ["#66C2A5", "#FC8D62", "#800026", "#E78AC3", "#A6D854", "#FFD92F", "#E5C494", "#B3B3B3"],
                fontSize: function (width, height, step) {
                    if (step < 5)
                        tamanho = tamanho - 0.001;
                    return width * tamanho * step + 'px';
                }
            });            
        });
    });
</script>
@endsection