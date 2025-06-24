$(document).ready(function() {

    var host =  $('meta[name="base-url"]').attr('content');
    var token = $('meta[name="csrf-token"]').attr('content');

    $('.clientes-noticia').each(function() {

        const container = $(this);
        const noticiaId = container.data('id');
        const tipo = container.data('tipo');

        $.ajax({
            
            url: host+'/noticia/' + noticiaId + '/tipo/'+tipo+'/clientes',
            type: 'GET',
            beforeSend: function(){
                $(".clientes-noticia-"+noticiaId).loader('show');
            },
            success: function(html) {
                container.html(html);
            },
            error: function() {
                container.html('<p class="text-danger">Erro ao carregar clientes.</p>');
            },
            complete: function() {
                $(".clientes-noticia-"+noticiaId).loader('hide');
            }
        });
    });

    $(document).on('click', '.btn-excluir-noticia', function(e) {

        e.preventDefault();
        const btn = $(this);
        const pivotId = btn.data('pivot-id');
        const noticiaId = btn.data('noticia-id');
        const tipo = btn.data('tipo-id');
        const container = btn.closest('.clientes-noticia');

        Swal.fire({
            title: 'Remover o cliente da notícia?',
            text: "Esta ação irá remover o cliente da notícia.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            
            if (result.isConfirmed) {
                
                $.ajax({
                    url: host+'/noticia/'+pivotId+'/vinculo/remover',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function() {

                        const container = $('.clientes-noticia[data-id="' + noticiaId + '"]');

                        $.ajax({
                            
                            url: host+'/noticia/' + noticiaId + '/tipo/'+tipo+'/clientes',
                            type: 'GET',
                            beforeSend: function(){
                                $(".clientes-noticia-"+noticiaId).loader('show');
                            },
                            success: function(html) {
                                container.html(html);
                            },
                            error: function() {
                                console.warn('Erro ao verificar clientes restantes.');
                            },
                            complete: function() {
                                $(".clientes-noticia-"+noticiaId).loader('hide');
                            }
                        });  
                    },
                    error: function() {
                        Swal.fire(
                            'Erro!',
                            'Não foi possível remover o cliente.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-sentimento', function(e) {
                
        e.preventDefault();

        const btn = $(this);
        const noticiaId = btn.data('noticia-id');
        const tipoId = btn.data('tipo-id');
        const clienteId = btn.data('cliente-id');
        const sentimento = btn.data('sentimento');

        $.ajax({
            
            url: host+'/noticia/sentimento/atualizar',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': token
            },
            data: {
                noticia_id: noticiaId,
                tipo_id: tipoId,
                cliente_id: clienteId,
                sentimento: sentimento
            },
            success: function() {

                const container = $('.clientes-noticia[data-id="' + noticiaId + '"]');

                $.ajax({
                    url: host+'/noticia/' + noticiaId + '/tipo/'+tipoId+'/clientes',
                    type: 'GET',
                    beforeSend: function(){
                        $(".clientes-noticia-"+noticiaId).loader('show');
                    },
                    success: function(html) {
                        container.html(html);
                    },
                    error: function() {
                        container.html('<p class="text-danger">Erro ao carregar clientes.</p>');
                    },
                    complete: function() {
                        $(".clientes-noticia-"+noticiaId).loader('hide');
                    }
                });
            },
            error: function() {
                Swal.fire(
                    'Erro!',
                    'Não foi possível atualizar o sentimento.',
                    'error'
                );
            }
        });
    });
});