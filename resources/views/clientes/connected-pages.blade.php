@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2"><i class="nc-icon nc-briefcase-24"></i> Páginas dos Concorrentes</h4>
                </div>
                <div class="col-md-6">
                 
                </div>
            </div>
        </div>
        <div class="card-body">            
            <table class="table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td class="text-center">Reactions</td>  
                        <td class="text-center">Total de Comentários</td>  
                        <td class="text-center">Total de Compartilhamentos</td>                          
                    </tr>
                </thead>
                <tbody>
                    @php
                        $reaction_count = 0;   

                        $objs = [];
                        $obj1 = new stdClass();

                        $obj1->name =  'Página Exemplo 2';

                        $objs[] = $obj1;

                        $obj2 = new stdClass();

                        $obj2->name =  'Página Exemplo 1';

                        $objs[] = $obj2;
 
                        $client->pagesMonitor = $objs;
                    @endphp

                    @foreach($client->pagesMonitor as $page)

                        @php
                            
                            foreach ($reactions as $reaction) {
                                ${"reaction_".$reaction->name} = 0;
                            }
                            
                            // foreach($page->fbPagesPost as $post) {      
                              
                            //     foreach($post->reactions as $reaction) {
                            //         ${"reaction_".$reaction->name}++;                                     
                            //     }
                            // }    
                        @endphp
                       
                        <tr>
                            <td>{{ $page->name }}</td>
                            <td class="text-center">
                                @foreach ($reactions as $reaction) 
                                    {{-- <div>{{ $reaction->icon }} {{ ${"reaction_".$reaction->name} }}</div> --}}
                                    <div>{{ $reaction->icon }} {!!  (rand(0, 100) > rand(0, 100) ? rand(0, 100). " <strong class='text-success' ><i class='fa fa-arrow-up'></i></strong> " .rand(0, 100). "%"  :  rand(0, 100). " <strong class='text-danger' ><i class='fa fa-arrow-down'></i></strong> ".rand(0, 100). "%" ) !!}</div>
                                    
                                @endforeach
                            
                            </td>  
                            {{-- <td class="text-center">{{ $page->fbPagesPost->sum('comment_count') }}</td>
                            <td class="text-center">{{ $page->fbPagesPost->sum('share_count') }}</td> --}}
                            <td class="text-center">{!!  (rand(0, 100) > rand(0, 100) ? rand(0, 100). " <strong class='text-success' ><i class='fa fa-arrow-up'></i></strong> " .rand(0, 100). "%"  :  rand(0, 100). " <strong class='text-danger' ><i class='fa fa-arrow-down'></i></strong> ".rand(0, 100). "%" ) !!}</td>
                            <td class="text-center">{!!  (rand(0, 100) > rand(0, 100) ? rand(0, 100). " <strong class='text-success' ><i class='fa fa-arrow-up'></i></strong> " .rand(0, 100). "%"  :  rand(0, 100). " <strong class='text-danger' ><i class='fa fa-arrow-down'></i></strong> ".rand(0, 100). "%" ) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection