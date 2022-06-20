@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2"><i class="nc-icon nc-briefcase-24"></i> Clientes > Conexões > {{ $client->name }}</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('clientes') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Clientes</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                <table class="table">
                    <thead class="">
                        <tr>
                            <th>Data da Conexão</th>
                            <th>Conta</th>
                            <th>Páginas</th>
                            <th>Coletar Menções?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($client->fbAccount as $key => $account)
                            <tr>
                                <td>{{ date('d/m/Y H:i:s', strtotime($account->updated_at)) }}</td>
                                <td>{{ $account->name }}</td>
                                <td>
                                    @foreach($account->fbPages as $key => $page)
                                        <p>
                                            <a href="" class='info-pagina' data-tipo='page'  data-token={{ $page->token }}><strong><i class="fa fa-facebook"></i> {{ $page->name }}</strong></a>
                                            @if($page->igPage)
                                                <a href="" class='info-pagina' data-tipo='instagram' data-token={{ $account->token }}><i class="fa fa-instagram"></i> {{ $page->igPage->name }}</a>
                                            @endif
                                        </p>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="form-check mt-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input data-accountid="{{ $account->id }}"  {{  $account->mention ? 'checked' : '' }}  class="form-check-input" type="checkbox" name="mencao">   
                                                <span class="form-check-sign"></span>                                                                                            
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>   
            </div>        
        </div>
    </div>
</div> 

<div class="modal fade" id="info-pagina" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
<script>
    $( document ).ready(function() {

        const APP_URL = {!! json_encode(url('/')) !!}

        $('input[name="mencao"]').click(function(){

            let checked = false;
            const account_id = $(this).data('accountid');
            const _token = $('meta[name="csrf-token"]').attr('content');

            if($(this).is(':checked')) {
                checked = true;
            } 
        
            fetch(APP_URL+'/account/collect/mention', {
                    method: 'POST', 
                    body: JSON.stringify({ _token: _token, account_id: account_id, checked: checked }),
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
            });
        });

        $('.info-pagina').click(function(){

            $('body').loader('show');

            let text = $(this).text();
            const tipo = $(this).data('tipo');
            const _token = $('meta[name="csrf-token"]').attr('content');
            const page_token = $(this).data('token');

            if(tipo == 'page') {
                text = '<i class="fa fa-facebook">'+text+'</i>';
            }

            if(tipo == 'instagram') {
                text = '<i class="fa fa-instagram">'+text+'</i>';
            }

            $('#info-pagina').find('.modal-title').html(text);

            fetch(APP_URL+'/check/token', {
                    method: 'POST', 
                    body: JSON.stringify({_token: _token, page_token: page_token}),
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
            }).then(function(response) {
                    return response.json();
                    //words = JSON.stringify(words);

            }).then(function(response){
                let html = '';
                if(response.is_valid === true){
                    html = '<p>O login é válido até: '+response.expires_at+'.</p>';
                } else {
                    html = '<p>Login inválido. Realize novamente a conexão com o Facebook.</p>';
                }
                
                $('#info-pagina').find('.modal-body').html(html);

                $('body').loader('hide');
                $('#info-pagina').modal('show');
            });

            return false;
        });
    });
</script>
@endsection