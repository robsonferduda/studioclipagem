$(document).ready(function() {

    $('#nu_cpf_par').mask('000.000.000-00');
    $('.dt_inicio').mask('00/00/0000',{ "placeholder": "dd/mm/YYYY" });
    $('.dt_termino').mask('00/00/0000',{ "placeholder": "dd/mm/YYYY" });
    $('.dt_inicial_relatorio').mask('00/00/0000',{ "placeholder": "dd/mm/YYYY" });
    $('.dt_final_relatorio').mask('00/00/0000',{ "placeholder": "dd/mm/YYYY" });

    var host =  $('meta[name="base-url"]').attr('content');
    var token = $('meta[name="csrf-token"]').attr('content');
            
    $(".flag_regras").click(function(){

        var flag = $(this).data("value");

        $('.content').loader('show');

        $.ajax({
            url: host+'/configuracoes/flag-regras/atualizar',
            type: 'POST',
            data: { "_token": token,
                    "valor": flag },
            success: function(response) {
                window.location.reload();
            },
            complete: function(){
                $('.content').loader('hide');
            }
        }); 

    });

    $('body').on("click", ".btn-enviar", function(e) {
        e.preventDefault();
        var url = $(this).attr("href");

        Swal.fire({
            title: "Tem certeza que deseja enviar o boletim?",
            text: "O boletim será enviado por email com os mesmos dados que aparecem em tela",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: '<i class="fa fa-send"></i> Enviar',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar'
        }).then(function(result) {
            if (result.value) {
                window.location.href = url;
            }
        });
    }); 

    $('body').on("click", ".btn-send-mail", function(e) {
        
        Swal.fire({
            title: "Envio de Boletim",            
            text: "Aguarde, o sistema está enviado as mensagens",
            html: '<i style="font-size: 65px;" class="fa fa-spinner fa-spin"></i><br/><br/>Aguarde, o sistema está enviado as mensagens',
            showConfirmButton: false,
            allowEscapeKey: true,
            allowOutsideClick: false
        });
    }); 

   
    $('body').on("click", ".button-remove-hashtag", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        Swal.fire({
            title: "Tem certeza que deseja excluir a hashtag?",
            text: "Essa ação irá excluir a hashtag, mas não o conteúdo relacionado a ela durante a coleta.",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Sim, excluir!",
            cancelButtonText: "Cancelar"
        }).then(function(result) {
            if (result.value) {
                form.submit();
            }
        });
    }); 

    $('body').on("click", ".button-remove", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        Swal.fire({
            title: "Tem certeza que deseja excluir?",
            text: "Você não poderá recuperar o registro excluído",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Sim, excluir!",
            cancelButtonText: "Cancelar"
        }).then(function(result) {
            if (result.value) {
                form.submit();
            }
        });
    }); 

    $('body').on("click", ".btn-delete-media", function(e) {
        e.preventDefault();
        var url = $(this).attr("href");
        var clique = $(this);

        Swal.fire({
            title: "Tem certeza que deseja excluir?",
            text: "Você não poderá recuperar o registro excluído",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: '<i class="fa fa-check"></i> Confirmar',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar'
        }).then(function(result) {
            if (result.value) {

                var div_pai = clique.closest('.card');                
                div_pai.loader('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        div_pai.remove();             
                    },
                    error: function(response){

                        Swal.fire({
                            title: "Erro ao realizar operação",
                            text: "Houve um erro ao realizar a exclusão",
                            type: "error",
                            icon: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#28a745",
                            confirmButtonText: "Ok"
                        });
                        
                    },
                    complete: function(response){
                        div_pai.loader('hide');
                    }
                });
                
                return false;
            }
        });
    }); 

    $('body').on("click", ".button-redo", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        Swal.fire({
            title: "Tem certeza que deseja voltar com essa expressão?",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Sim!",
            cancelButtonText: "Cancelar"
        }).then(function(result) {
            if (result.value) {
                form.submit();
            }
        });
    }); 


    $('body').on("click", ".button-remove-evento", function(e) {
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
            title: "Tem certeza que deseja remover a participação neste evento?",
            text: "Você não poderá recuperar o registro excluído",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) {
                  return 'You need to write something!'
                }
            },
            confirmButtonColor: "#28a745",
            confirmButtonText: "Sim, excluir!",
            cancelButtonText: "Cancelar"
        }).then(function(result) {
            if (result.value) {
                window.location.href = link;
            }
        });
    });

    var host =  $('meta[name="base-url"]').attr('content');

    var inputOptionsPromise = new Promise(function (resolve) {
        
        var options = {};
        $.ajax({
            url: host+'/cliente/get/json',
            type: 'GET',
            success: function(response) {

                $.map(response,
                    function(o) {
                        options[o.id] = o.name;
                    });

                resolve(options)               
            }
        });
    });

    $('body').on("click", ".config_periodo", function(e) {

        var periodo_atual = $(".periodo_atual").text();
        e.preventDefault();
        Swal.fire({
            title: "Informe o período em dias",
            text: "Digite ou selecione um valor",
            input: 'number',
            inputValue: periodo_atual,
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) {
                  return 'Você precisa informar um valor para o período'
                }
            },
            confirmButtonColor: "#28a745",
            confirmButtonText: '<i class="fa fa-check"></i> Confirmar',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {

                var periodo = $(".swal2-input").val();

                $.ajax({
                    url: host+'/configuracoes/periodo/selecionar',
                       type: 'POST',
                       data: {
                            "_token": $('meta[name="csrf-token"]').attr('content'),
                            "periodo": periodo
                    },
                    success: function(response) {
                        window.location.reload();                                
                    },
                    error: function(response){
                        console.log(response);
                    }
                });
            }
        });
    });

    $('body').on("click", ".config_cliente", function(e) {
        var cliente_atual = $(".periodo_atual").text();
        e.preventDefault();
        Swal.fire({
            title: "Selecione um cliente",
            input: 'select',
            inputValue: cliente_atual,
            inputOptions: inputOptionsPromise,
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: '<i class="fa fa-check"></i> Confirmar',
            cancelButtonText: '<i class="fa fa-times"></i> Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {

                var cliente = $(".swal2-select").val();

                $.ajax({
                    url: host+'/configuracoes/cliente/selecionar',
                       type: 'POST',
                       data: {
                            "_token": $('meta[name="csrf-token"]').attr('content'),
                            "cliente": cliente
                    },
                    success: function(response) {
                        window.location.reload();                                
                    },
                    error: function(response){
                        console.log(response);
                    }
                });
            }
        });
    });

    $('body').on("click", ".troca_cliente", function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Selecione um cliente",
            input: 'select',
            inputOptions: inputOptionsPromise,
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Confirmar",
            cancelButtonText: "Cancelar"
        }).then(function(result) {
            if (result.isConfirmed) {

                var cliente = $(".swal2-select").val();

                $.ajax({
                    url: host+'/cliente/selecionar',
                       type: 'POST',
                       data: {
                            "_token": $('meta[name="csrf-token"]').attr('content'),
                            "cliente": cliente
                    },
                    success: function(response) {
                        window.location.reload();                                
                    },
                    error: function(response){
                        console.log(response);
                    }
                });
            }
        });
    });
    
    $("#is_password").change(function(){

        if($(this).is(':checked'))
            $('.box-password').css("display","block");
        else
            $('.box-password').css("display","none");

    });  
    
    $("#todos").change(function(){

        if($(this).is(':checked')){

            $(".form-check-input").each(function(){
                $(this).prop("checked", true);
            });

        }else{

            $(".form-check-input").each(function(){
                $(this).prop("checked", false);
            });
        }        
    });
});