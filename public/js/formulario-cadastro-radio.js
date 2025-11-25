$(document).ready(function() {

    var host = $('meta[name="base-url"]').attr('content');
    var clientes = [];
    var id_noticia = $("#id_noticia").val();

    /*
    $("#btn_enviar").click(function(e){

        if(!clientes.length){
            e.preventDefault();

            Swal.fire({
              title: "Notícia sem clientes",
              text: "Você não vinculou nenhum cliente. Deseja continuar?",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              cancelButtonText: "Cancelar",
              confirmButtonText: "Sim, salvar mesmo assim!"
            }).then((result) => {
              if (result.isConfirmed) {
                // Encontra o formulário correto (criar ou editar)
                var form = $("#frm_noticia_radio_criar").length ? $("#frm_noticia_radio_criar") : $("#frm_noticia_radio_editar");
                form.submit();
              }
            });
        }

    });

    $("#btn_enviar_e_salvar").click(function(e){

        if(!clientes.length){
            e.preventDefault();

            Swal.fire({
              title: "Notícia sem clientes",
              text: "Você não vinculou nenhum cliente. Deseja continuar?",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              cancelButtonText: "Cancelar",
              confirmButtonText: "Sim, salvar mesmo assim!"
            }).then((result) => {
              if (result.isConfirmed) {
                // Encontra o formulário correto (criar ou editar)
                var form = $("#frm_noticia_radio_criar").length ? $("#frm_noticia_radio_criar") : $("#frm_noticia_radio_editar");
                form.submit();
              }
            });
        }

    });*/

    $(document).on('keyup', '.monetario', function() {
                
        var retorno = 0;
        var valor = ($("#nu_valor_fonte").val()) ? $("#nu_valor_fonte").val() : 1;
        var largura = ($("#nu_largura").val()) ? $("#nu_largura").val() : 1;
        var colunas = ($("#nu_colunas").val()) ? $("#nu_colunas").val() : 1;

        retorno = valor * largura * colunas;

        // Truncar o valor com duas casas decimais
        retorno = retorno.toFixed(2);

        $("#valor_retorno").val(retorno);
    });

    //Inicializa o combo de clientes
    $.ajax({
        url: host+'/api/cliente/buscarClientes',
        type: 'GET',
        beforeSend: function() {
                
        },
        success: function(data) {
            if(!data) {
                Swal.fire({
                    text: 'Não foi possível buscar os clientes. Entre em contato com o suporte.',
                    type: "warning",
                    icon: "warning",
                });
                return;
            }

            data.forEach(element => {
                let option = new Option(element.text, element.id);
                $('.cliente').append(option);
            });
        },
        complete: function(){
                   
        }
    });

	$(document).on('change', '.cliente', function() {
        var cliente = $(this).val();
        buscarAreas(cliente);
    });

    $(document).on('click', '.btn-remover-cliente', function() {
            
        id = $(this).data("id");
        clientes.splice(id, 1);
            
        $("#clientes").val(JSON.stringify(clientes));

        $(".metadados").empty();
        $.each(clientes, function(index, value) {                
            $(".metadados").append('<li><div class="row"><div class="col-md-12 col-12 mb-2"><span>'+value.cliente+'</span> | <span>'+value.area+'</span> | <span>'+value.sentimento+'</span> | <span class="text-danger btn-remover-cliente" data-id="'+index+'">Excluir</span></div></div></li>');
        });
    });

    $.ajax({
        url: host+'/noticia/radio/clientes/'+id_noticia,
        type: 'GET',
        beforeSend: function() {
            
        },
        success: function(data) {
            $.each(data, function(index, value) {                
               
                var dados = { id_cliente: value.cliente_id, cliente: value.nome, id_area: value.area_id, area: value.area, id_sentimento: value.id_sentimento, sentimento: value.sentimento };
                inicializaClientes(dados);
            });
        },
        complete: function(){
                        
        }
    });

    $(document).on("click", ".btn-add-cliente", function(clientes) {

        var id_cliente = $("#cd_cliente").val();

        if(id_cliente){
                
            var cliente = $("#cd_cliente option:selected").text();
            var id_area = $("#cd_area").val();
            var id_sentimento = $("#cd_sentimento").val();
                
            if(id_area)
                var area = $("#cd_area option:selected").text();
            else
                var area = 'Nenhuma área selecionada';

                
            if(id_sentimento)
                var sentimento = $("#cd_sentimento option:selected").text();
            else
                var sentimento = "Nenhum sentimento selecionado";


                
            var dados = { id_cliente: id_cliente, cliente: cliente, id_area: id_area, area: area, id_sentimento: id_sentimento, sentimento: sentimento };
            
            inicializaClientes(dados);

        }else{
             
            Swal.fire({
                text: 'Obrigatório informar um cliente.',
                type: "warning",
                icon: "warning",
                confirmButtonText: '<i class="fa fa-check"></i> Ok',
            });
        }
    });

    $(document).on("click", ".btn-add-area", function() {

        var ds_area = $("#ds_area").val();
        var id_cliente = $("#cd_cliente").val();

        if(!id_cliente){

            Swal.fire({
                text: 'Obrigatório informar um cliente.',
                type: "warning",
                icon: "warning",
                confirmButtonText: '<i class="fa fa-check"></i> Ok',
            });

        }else{

            $.ajax({url: host+'/cliente/area/adicionar',
                type: 'POST',
                data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                        "ds_area": ds_area,
                        "id_cliente": id_cliente
                },
                beforeSend: function() {
        
                },
                success: function(data) {
                    $("#cd_cliente").trigger('change');                           
                },
                error: function(){
                    
                },
                complete: function(){
                    $('#modalArea').modal('hide');
                    $("#ds_area").val("");
                }
            });

        }
    });

    function inicializaClientes(dados){

        var index = -1;

        // Procura pelo id_cliente (ou cliente, dependendo do que define unicidade)
        for (var i = 0; i < clientes.length; i++) {
            if (clientes[i].id_cliente == dados.id_cliente) {
                index = i;
                break;
            }
        }

        if (index > -1) {
            // Atualiza apenas area e sentimento
            clientes[index].id_area     = dados.id_area;
            clientes[index].area        = dados.area;
            clientes[index].id_sentimento = dados.id_sentimento;
            clientes[index].sentimento  = dados.sentimento;
        } else {
            // Adiciona novo cliente ao array
            clientes.push(dados);
        }
        
        $("#clientes").val(JSON.stringify(clientes));

        $(".metadados").empty();

        $.each(clientes, function(index, value) {                
            $(".metadados").append('<li><div class="row"><div class="col-md-12 col-12 mb-2"><span>'+value.cliente+'</span> | <span>'+value.area+'</span> | <span>'+value.sentimento+'</span> | <span class="text-danger btn-remover-cliente" data-id="'+index+'">Excluir</span></div></div></li>');
        });
    }

	function buscarAreas(cliente){

        if(cliente == '') {
            $('.area').attr('disabled', true);
            $('.area').append('<option value="">Cliente não possui áreas</option>').val('');
            return;
        }

        $.ajax({
            url: host+'/api/cliente/getAreasCliente',
            type: 'GET',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "cliente": cliente,
            },
            beforeSend: function() {
                $('.area').append('<option value="">Carregando...</option>').val('');
            },
            success: function(data) {

                $('.area').find('option').remove();
                $('.area').attr('disabled', false);

                if(data.length == 0) {                            
                    $('.area').append('<option value="">Cliente não possui áreas vinculadas</option>').val('');
                    return;
                }
                        
                $('.area').append('<option value="">Selecione uma área</option>').val('');
                data.forEach(element => {
                    let option = new Option(element.descricao, element.id);
                    $('.area').append(option);
                });             
            },
            complete: function(){
                        
            }
        });
    }	
});