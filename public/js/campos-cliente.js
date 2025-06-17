$(document).ready(function() {

    var host =  $('meta[name="base-url"]').attr('content');

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