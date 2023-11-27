@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Jornal Web
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Coletas
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('buscar-web') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Notícias Web</a>
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-globe"></i> Fontes Web</a>
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <input type="hidden" id="id_fonte" value="{{ $fonte->id }}"/>
                <input type="hidden" id="id_knewin" value="{{ $fonte->id_knewin }}"/>
                <div class="col-lg-6 col-sm-12">
                    <h5><i class="fa fa-database" aria-hidden="true"></i> Dados Studio Clipagem</h5>
                    <div class="dados-studio" style="min-height: 500px; position: relative; "></div>
                    <div class="box-studio">
                        <table id="table_studio" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Notícia</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Data</th>
                                    <th>Notícia</th>
                                    <th>Ver</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <h5><i class="fa fa-database" aria-hidden="true"></i> Dados Knewin</h5>
                    <div class="dados-knewin" style="min-height: 500px; position: relative; "></div>
                    <div class="box-knewin">
                        <table id="table_knewin" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Notícia</th>
                                    <th class="center">Ver</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Data</th>
                                    <th>Notícia</th>
                                    <th class="center">Ver</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                
                            </tbody>
                        </table>
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

        var id_fonte = $("#id_fonte").val();
        var id_knewin = $("#id_knewin").val();

        $('.dados-studio').loader('show');
        $(".box-studio").css("display","none");

        $('.dados-knewin').loader('show');
        $(".box-knewin").css("display","none");

        function formataData(data){

            const dataCriada = new Date(data);
            const dataFormatada = dataCriada.toLocaleDateString('pt-BR', {
            timeZone: 'UTC',
            });

            return data;
        }

        
      
        $.ajax({
            url: '../coletas/studio/listar/'+id_fonte,
            type: 'GET',
            success: function(result) {
                $("#table_studio  > tbody > tr").remove();
                if(result.length){
                    $.each(result, function( index, value ) {
                        $("#table_studio").append('<tr><td>'+value.dt_clipagem+'</td><td>'+value.titulo+'</td><td class="center"><a class="fa fa-eye" href="../../jornal-web/noticia/'+value.id+'"></a></td></tr>');
                    }); 
                }else{
                    $("#table_knewin").append('<tr><td colspan="3">Nenhuma notícia coletada</td></tr>');
                }            
            },
            error: function(response){

            },
            complete: function(response) {
                $('.dados-studio').loader('hide');
                $('.dados-studio').remove();
                $(".box-studio").css("display","block");      
            }
        });  
        
        $.ajax({
            url: '../coletas/knewin/listar/'+id_knewin,
            type: 'GET',
            success: function(result) {
                $("#table_knewin  > tbody > tr").remove();
                if(result.length){
                    $.each(result, function( index, value ) {
                        alert(formataData(value.data_cadastro));
                        $("#table_knewin").append('<tr><td>'+value.data_cadastro+'</td><td>'+value.titulo+'</td><td class="center"><a class="fa fa-eye" href="../../jornal-web/noticia/'+value.id+'"></a></td></tr>');
                    });    
                }else{
                    $("#table_knewin").append('<tr><td colspan="3">Nenhuma notícia coletada</td></tr>');
                }        
            },
            error: function(response){

            },
            complete: function(response) {
                $('.dados-knewin').loader('hide');
                $('.dados-knewin').remove();
                $(".box-knewin").css("display","block");      
            }
        });  
      
    });
</script>
@endsection
