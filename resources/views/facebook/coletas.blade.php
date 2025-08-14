@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-facebook ml-3"></i> Facebook 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Coletas
                    </h4>
                </div>
                <div class="col-md-4">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="col-md-12">
                    <form id="form-filtro-data" class="form-inline float-right">
                        <label for="filtro-data" class="mr-2">Filtrar por data:</label>
                        <input type="date" id="filtro-data" name="filtro-data" class="form-control form-control-sm mr-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    </form>
                </div>
                <div class="col-md-12">
                    <div class="row" id="facebook-cards"></div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');
            var pagina = 1;

           var filtroData = '';

            function formatarDataBR(dataIso) {
                if (!dataIso) return '';
                let d = new Date(dataIso.replace(' ', 'T'));
                if (isNaN(d.getTime())) return dataIso;
                let dia = String(d.getDate()).padStart(2, '0');
                let mes = String(d.getMonth() + 1).padStart(2, '0');
                let ano = d.getFullYear();
                let hora = String(d.getHours()).padStart(2, '0');
                let min = String(d.getMinutes()).padStart(2, '0');
                let seg = String(d.getSeconds()).padStart(2, '0');
                return `${dia}/${mes}/${ano} ${hora}:${min}:${seg}`;
            }

            function carregarPosts(pagina = 1) {
                $.ajax({
                    url: host + '/facebook/posts?page=' + pagina + (filtroData ? '&data=' + filtroData : ''),
                    type: 'GET',
                    success: function(response) {
                        let posts = response.data || response;
                        let html = '';
                        if (posts.length === 0) {
                            html = `
                                <div class="col-md-12">
                                    <div class="alert alert-warning text-center">
                                        Nenhum post encontrado.
                                    </div>
                                </div>
                            `;
                        } else {
                            posts.forEach(function(post) {
                                html += `
                                <div class="col-md-12 mb-3">
                                    <div class="card">
                                        <div class="row no-gutters">
                                            <div class="col-md-2 d-flex align-items-center justify-content-center mb-3 mt-3">
                                                ${post.imagem ? `<img src="${post.imagem}" class="img-fluid rounded" style="max-height:120px;">` : '<i style="color: #3b5998;" class="fa fa-facebook fa-4x"></i>'}
                                            </div>
                                            <div class="col-md-10">
                                                <div class="card-body">
                                                    <p class="card-title">${post.mensagem ? post.mensagem : 'Sem mensagem'}</p>
                                                    <p class="card-text">
                                                        <small>${post.data_postagem ? formatarDataBR(post.data_postagem) : ''}</small>
                                                    </p>
                                                    <div>
                                                        <i class="fa fa-thumbs-up text-info mr-2"></i> ${post.reactions || 0}
                                                        <i class="fa fa-comment text-success mr-2"></i> ${post.comments || 0}
                                                        <i class="fa fa-share text-warning mr-2"></i> ${post.shares || 0}
                                                    </div>
                                                    <a href="${post.link}" target="_blank" style="background: #3b5998;" class="btn btn-primary btn-sm pull-right mt-2"><i class="fa fa-facebook"></i> Ver no Facebook</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                `;
                            });

                            // Paginação
                            if (response.last_page && response.last_page > 1) {
                                html += `<div class="col-md-12 text-center mt-3">`;
                                for (let i = 1; i <= response.last_page; i++) {
                                    html += `<button class="btn btn-outline-primary btn-sm mx-1 pagina-btn" data-pagina="${i}">${i}</button>`;
                                }
                                html += `</div>`;
                            }
                        }
                        $('#facebook-cards').html(html);
                    },
                    error: function(xhr, status, error) {
                        $('#facebook-cards').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger text-center">
                                    Ocorreu um erro ao buscar os posts do Facebook.<br>
                                    Tente novamente mais tarde.
                                </div>
                            </div>
                        `);
                    }
                });
            }

            // Evento de paginação
            $(document).on('click', '.pagina-btn', function() {
                var pagina = $(this).data('pagina');
                carregarPosts(pagina);
            });

            // Evento do filtro por data
            $('#form-filtro-data').on('submit', function(e) {
                e.preventDefault();
                filtroData = $('#filtro-data').val();
                carregarPosts(1);
            });

            carregarPosts();
        });
    </script>
@endsection