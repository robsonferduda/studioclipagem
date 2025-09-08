@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-instagram ml-3"></i> Instagram
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Postagens
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
                        <input type="text" id="filtro-texto" name="filtro-texto" class="form-control form-control-sm mr-2" placeholder="Buscar texto...">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    </form>
                </div>
                <div class="col-md-12">
                    <div class="row" id="instagram-cards"></div>
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
           var filtroTexto = '';

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
                    url: host + '/instagram/posts-clientes?page=' + pagina +
                        (filtroData ? '&data=' + filtroData : '') +
                        (filtroTexto ? '&texto=' + encodeURIComponent(filtroTexto) : ''),
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

                            // Paginação
                            if (response.last_page && response.last_page > 1) {
                                html += `<div class="col-md-12 text-center mt-3">`;
                                for (let i = 1; i <= response.last_page; i++) {
                                    html += `<button class="btn btn-outline-primary btn-sm mx-1 pagina-btn" data-pagina="${i}">${i}</button>`;
                                }
                                html += `</div>`;
                            }

                            posts.forEach(function(post) {
                                html += `
                                <div class="col-md-12 mb-3">
                                    <div class="card">
                                        <div class="row no-gutters">
                                            <div class="col-md-2 d-flex align-items-center justify-content-center mb-3 mt-3">
                                                ${post.media_url ? `<img src="${post.media_url}" class="img-fluid rounded" style="max-height:220px;">` : '<i style="color: #3b5998;" class="fa fa-instagram fa-4x"></i>'}
                                            </div>
                                            <div class="col-md-10">
                                                <div class="card-body">
                                                    <p class="card-title">${post.caption ? post.caption : 'Sem mensagem'}</p>
                                                    <p class="card-text">
                                                        <small>${post.timestamp ? formatarDataBR(post.timestamp) : ''}</small>
                                                    </p>
                                                    <div>
                                                        <i class="fa fa-thumbs-up text-info mr-2"></i> ${post.like_count || 0}
                                                        <i class="fa fa-comment text-success mr-2"></i> ${post.comments_count || 0}
                                                    </div>
                                                    <a href="${post.permalink}" target="_blank" style="background: #962fbf;" class="btn btn-primary btn-sm pull-right mt-2"><i class="fa fa-instagram"></i> Ver no Instagram</a>
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
                        $('#instagram-cards').html(html);
                    },
                    error: function(xhr, status, error) {
                        $('#instagram-cards').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger text-center">
                                    Ocorreu um erro ao buscar os posts do Instagram.<br>
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
                filtroTexto = $('#filtro-texto').val();
               
                carregarPosts(1);
            });

            carregarPosts();
        });
    </script>
@endsection