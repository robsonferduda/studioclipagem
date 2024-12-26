<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="base-url" content="{{ env('BASE_URL') }}">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link href="images/favicon.png" rel="shortcut icon">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>{{ config('app.name', 'Studio Clipagem') }}</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/paper-dashboard.css?v=2.0.1') }}" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="{{ asset('css/list.css') }}" rel="stylesheet" />
  <link href="{{ asset('demo/demo.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/schedule.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/croppie.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/jqcloud.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/jquery.loader.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/inputTags.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/bootstrap-multiselect.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/bootstrap-duallistbox.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/dropzone.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/cropper.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
  @yield('style')
</head>

<body class="">
    <div class="wrapper ">
        <div class="sidebar" data-color="white" data-active-color="danger">
            <div class="logo">
                <a style="padding-left: 8px;" href="{{ url('perfil') }}" class="simple-text logo-normal">
                  <i class="fa fa-user"></i> {{ (Auth::user()) ? explode(" ", Auth::user()->name)[0] : 'Não identificado' }}
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                <li class="{{ (Session::has('url') and Session::get('url') == 'home') ? 'active' : '' }}">
                    <a href="{{ url('/') }}">
                    <i class="nc-icon nc-chart-pie-36"></i>
                    <p>Dashboard</p>
                    </a>
                </li>
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'cliente') ? 'active' : '' }}">
                    <a href="{{ url('cliente') }}">
                    <i class="nc-icon nc-briefcase-24"></i>
                    <p>Clientes</p>
                    </a>
                  </li>
                @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'areas') ? 'active' : '' }}">
                    <a href="{{ url('areas') }}">
                    <i class="fa fa-tags"></i>
                    <p>Áreas</p>
                    </a>
                  </li>
                @endrole
                @role('administradores')
                <li class="{{ (Session::has('url') and Session::get('url') == 'tags') ? 'active' : '' }}">
                  <a href="{{ url('tags') }}">
                    <i class="fa fa-hashtag" aria-hidden="true"></i>
                  <p>Tags</p>
                  </a>
                </li>
              @endrole
              @role('administradores')
                <li class="{{ (Session::has('url') and Session::get('url') == 'assessorias') ? 'active' : '' }}">
                  <a href="{{ url('assessorias') }}">
                    <i class="fa fa-microphone" aria-hidden="true"></i>
                  <p>Assessorias</p>
                  </a>
                </li>
              @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'impresso') ? 'active' : '' }}">
                      <a data-toggle="collapse" href="#submenu-impresso" class="{{ (Session::has('url') and Session::get('url') == 'impresso') ? '' : 'collapsed' }}" aria-expanded="{{ (Session::has('url') and Session::get('url') == 'impresso') ? 'true' : 'false' }}">
                        <i class="fa fa-newspaper-o"></i>
                        <p>Impressos
                          <b class="caret"></b>
                        </p>
                      </a>
                      <div class="collapse {{ (Session::has('url') and Session::get('url') == 'impresso') ? 'show' : '' }}" id="submenu-impresso" aria-expanded="false">
                        <ul class="nav ml-5">
                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'impresso') ? 'active' : '' }}">
                            <a href="{{ url('impresso') }}">
                            <span class="sidebar-normal">Dashboard</span>
                            </a>
                         </li> 
                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'fonte-impressa') ? 'active' : '' }}">
                            <a href="{{ url('fonte-impresso/listar') }}">
                            <span class="sidebar-normal">Fontes Impressos</span>
                            </a>
                          </li>
                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'upload') ? 'active' : '' }}">
                            <a href="{{ url('jornal-impresso/upload') }}">
                              <span class="sidebar-normal">Arquivos Impressos</span>
                              </a>
                          </li> 
                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'arquivos-web') ? 'active' : '' }}">
                            <a href="{{ url('jornal-impresso/web') }}">
                              <span class="sidebar-normal">Edições Web</span>
                              </a>
                          </li>
                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'arquivos-paginas') ? 'active' : '' }}">
                            <a href="{{ url('jornal-impresso/paginas') }}">
                              <span class="sidebar-normal">Páginas Web</span>
                              </a>
                          </li>
                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'noticia-impressa-cadastrar') ? 'active' : '' }}">
                              <a href="{{ url('noticia/impresso/cadastrar') }}">
                              <span class="sidebar-normal">Cadastrar Notícia</span>
                              </a>
                          </li> 
                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'impresso/noticias') ? 'active' : '' }}">
                            <a href="{{ url('impresso/noticias') }}">
                              <span class="sidebar-normal">Notícias</span>
                              </a>
                          </li>
                        </ul>
                     </div>
                  </li>
                @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'jornal-web') ? 'active' : '' }}">
                    <a data-toggle="collapse" href="#submenu-web" class="{{ (Session::has('url') and Session::get('url') == 'jornal-web') ? '' : 'collapsed' }}" aria-expanded="{{ (Session::has('url') and Session::get('url') == 'jornal-web') ? 'true' : 'false' }}">
                        <i class="fa fa-globe"></i>
                        <p>Web
                          <b class="caret"></b>
                        </p>
                    </a>
                    <div class="collapse {{ (Session::has('url') and Session::get('url') == 'jornal-web') ? 'show' : '' }}" id="submenu-web" aria-expanded="false">
                       <ul class="nav ml-5">
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'web-dashboard') ? 'active' : '' }}">
                          <a href="{{ url('noticia/web/dashboard') }}">
                          <span class="sidebar-normal">Dashboard</span>
                          </a>
                        </li>
                         <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'jornal-web') ? 'active' : '' }}">
                          <a href="{{ url('noticia/web') }}">
                          <span class="sidebar-normal">Notícias</span>
                          </a>
                       </li>
                         <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'web-cadastrar') ? 'active' : '' }}">
                          <a href="{{ url('noticia/web/cadastrar') }}">
                          <span class="sidebar-normal">Nova Notícia</span>
                          </a>
                       </li>
                       <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'fonte-web') ? 'active' : '' }}">
                        <a href="{{ url('fonte-web/listar') }}">
                        <span class="sidebar-normal">Fontes Web</span>
                        </a>
                     </li>
                     <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'fonte-web-inconsistencias') ? 'active' : '' }}">
                      <a href="{{ url('fonte-web/inconsistencias') }}">
                      <span class="sidebar-normal">Inconsistências</span>
                      </a>
                   </li>
                     <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'web-estatisticas') ? 'active' : '' }}">
                      <a href="{{ url('jornal-web/estatisticas') }}">
                      <span class="sidebar-normal">Estatísticas</span>
                      </a>
                   </li>
                       </ul>
                    </div>
                 </li>                  
                @endrole
                @role('administradores')
                <li class="{{ (Session::has('url') and Session::get('url') == 'radio') ? 'active' : '' }}">
                  <a data-toggle="collapse" href="#submenu-radio" class="{{ (Session::has('url') and Session::get('url') == 'radio') ? '' : 'collapsed' }}" aria-expanded="{{ (Session::has('url') and Session::get('url') == 'radio') ? 'true' : 'false' }}">
                     <i class="fa fa-volume-up"></i>
                     <p>Rádio
                        <b class="caret"></b>
                     </p>
                  </a>
                  <div class="collapse {{ (Session::has('url') and Session::get('url') == 'radio') ? 'show' : '' }}" id="submenu-radio" aria-expanded="false">
                     <ul class="nav ml-5">
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'radio-dashboard') ? 'active' : '' }}">
                          <a href="{{ url('radio/dashboard') }}">
                          <span class="sidebar-normal">Dashboard</span>
                          </a>
                        </li>
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'radios') ? 'active' : '' }}">
                           <a href="{{ url('radio/noticias') }}">
                           <span class="sidebar-normal">Notícias</span>
                           </a>
                        </li>
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'radio-cadastrar') ? 'active' : '' }}">
                          <a href="{{ url('radio/noticias/cadastrar') }}">
                          <span class="sidebar-normal">Nova Notícia</span>
                          </a>
                        </li>
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'radio-arquivos') ? 'active' : '' }}">
                          <a href="{{ url('radio/arquivos') }}">
                          <span class="sidebar-normal">Arquivos Rádio</span>
                          </a>
                        </li>                        
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'emissoras-radio') ? 'active' : '' }}">
                           <a href="{{ url('emissoras/radio') }}">
                           <span class="sidebar-normal">Emissoras</span>
                           </a>
                        </li>
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'programas-radio') ? 'active' : '' }}">
                           <a href="{{ url('programas/radio') }}">
                           <span class="sidebar-normal">Programas</span>
                           </a>
                        </li>
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'radio-estatisticas') ? 'active' : '' }}">
                           <a href="{{ url('radio/estatisticas') }}">
                           <span class="sidebar-normal">Estatísticas</span>
                           </a>
                        </li>
                     </ul>
                  </div>
               </li>
                @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'tv') ? 'active' : '' }}">
                      <a data-toggle="collapse" href="#submenu-tv" class="{{ (Session::has('url') and Session::get('url') == 'tv') ? '' : 'collapsed' }}" aria-expanded="{{ (Session::has('url') and Session::get('url') == 'tv') ? 'true' : 'false' }}">
                        <i class="fa fa-tv"></i>
                        <p>TV
                           <b class="caret"></b>
                        </p>
                     </a>
                     <div class="collapse {{ (Session::has('url') and Session::get('url') == 'tv') ? 'show' : '' }}" id="submenu-tv" aria-expanded="false">
                        <ul class="nav ml-5">
                          
                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'tv-dashboard') ? 'active' : '' }}">
                            <a href="{{ url('tv/dashboard') }}">
                            <span class="sidebar-normal">Dashboard</span>
                            </a>
                         </li>

                         <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'tv-videos') ? 'active' : '' }}">
                          <a href="{{ url('tv/videos') }}">
                          <span class="sidebar-normal">Vídeos TV</span>
                          </a>
                       </li>

                          <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'tv-noticias') ? 'active' : '' }}">
                            <a href="{{ url('tv/noticias') }}">
                            <span class="sidebar-normal">Notícias</span>
                            </a>
                         </li>

                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'tv-cadastrar') ? 'active' : '' }}">
                          <a href="{{ url('tv/noticias/cadastrar') }}">
                          <span class="sidebar-normal">Nova Notícia</span>
                          </a>
                        </li>

                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'tv-emissoras') ? 'active' : '' }}">
                          <a href="{{ url('tv/emissoras') }}">
                          <span class="sidebar-normal">Emissoras</span>
                          </a>
                        </li>

                         <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'tv-decupagem') ? 'active' : '' }}">
                          <a href="{{ url('tv/decupagem') }}">
                          <span class="sidebar-normal">Decupagem</span>
                          </a>
                       </li>
                           
                            <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'emissoras-tv') ? 'active' : '' }}">
                                <a href="{{ url('emissoras/tv') }}">
                                <span class="sidebar-normal">Emissoras</span>
                                </a>
                            </li>
                           <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'programas-tv') ? 'active' : '' }}">
                              <a href="{{ url('programas/tv') }}">
                              <span class="sidebar-normal">Programas</span>
                              </a>
                           </li>
                           
                         <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'tv-estatisticas') ? 'active' : '' }}">
                          <a href="{{ url('tv/estatisticas') }}">
                          <span class="sidebar-normal">Estatísticas</span>
                          </a>
                       </li>
                           
                        </ul>
                     </div>
                  </li>
                @endrole
                @role('administradores')
                <li class="{{ (Session::has('url') and Session::get('url') == 'pautas') ? 'active' : '' }}">
                  <a data-toggle="collapse" href="#submenu-pauta" class="{{ (Session::has('url') and Session::get('url') == 'pautas') ? '' : 'collapsed' }}" aria-expanded="{{ (Session::has('url') and Session::get('url') == 'jornal-web') ? 'true' : 'false' }}">
                      <i class="fa fa-file-text-o"></i>
                      <p>Pautas
                        <b class="caret"></b>
                      </p>
                  </a>
                  <div class="collapse {{ (Session::has('url') and Session::get('url') == 'pautas') ? 'show' : '' }}" id="submenu-pauta" aria-expanded="false">
                     <ul class="nav ml-5">
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'pautas') ? 'active' : '' }}">
                           <a href="{{ url('pautas') }}">
                           <span class="sidebar-normal">Listar</span>
                           </a>
                        </li>
                        <li class="{{ (Session::has('sub-menu') and Session::get('sub-menu') == 'pauta-cadastrar') ? 'active' : '' }}">
                          <a href="{{ url('pauta/cadastrar') }}">
                          <span class="sidebar-normal">Cadastrar</span>
                          </a>
                       </li>
                     </ul>
                  </div>
               </li>                  
              @endrole
              @role('administradores')
                <li class="{{ (Session::has('url') and Session::get('url') == 'coleta') ? 'active' : '' }}">
                  <a href="{{ url('coletas') }}">
                  <i class="fa fa-database"></i>
                  <p>Coletas</p>
                  </a>
                </li>
              @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'monitoramento') ? 'active' : '' }}">
                    <a href="{{ url('monitoramento') }}">
                    <i class="nc-icon nc-sound-wave"></i>
                    <p>Monitoramento</p>
                    </a>
                  </li>
                @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'boletins') ? 'active' : '' }}">
                    <a href="{{ url('boletins') }}">
                    <i class="fa fa-file-o"></i>
                    <p>Boletins</p>
                    </a>
                  </li>
                @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'relatorios') ? 'active' : '' }}">
                    <a href="{{ url('relatorios') }}">
                    <i class="fa fa-file-pdf-o"></i>
                    <p>Relatórios</p>
                    </a>
                  </li>
                @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'exportar') ? 'active' : '' }}">
                    <a href="{{ url('exportar') }}">
                    <i class="fa fa-upload"></i>
                    <p>Exportação</p>
                    </a>
                  </li>
                @endrole
                <hr/>
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'perfis') ? 'active' : '' }}">
                      <a href="{{ url('perfis') }}">
                      <i class="fa fa-group"></i>
                      <p>Perfis</p>
                      </a>
                  </li>
                @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'permissoes') ? 'active' : '' }}">
                      <a href="{{ url('permissoes') }}">
                      <i class="nc-icon nc-lock-circle-open"></i>
                      <p>Permissões</p>
                      </a>
                  </li>
                @endrole
                @role('administradores')
                  <li class="{{ (Session::has('url') and Session::get('url') == 'usuarios') ? 'active' : '' }}">
                      <a href="{{ url('usuarios') }}">
                      <i class="nc-icon nc-circle-10"></i>
                      <p>Usuários</p>
                      </a>
                  </li>  
                @endrole               
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                      <i class="nc-icon nc-button-power"></i>
                      <p>Sair</p>
                    </a>
                </li>
              </ul>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </div>
        </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand upper" href="{{ url('dashboard') }}">Studioclipagem</a>
            <div class="mb-1 ml-2 mt-2">
              <span class="troca_cliente">Koerich <i class="fa fa-refresh"></i></span> 
            </div>
            <div class="mb-1 ml-4 mt-2">
              <span class="data-refresh">{{ \Carbon\Carbon::parse(Session::get('data_atual'))->format('d/m/Y') }} <i class="fa fa-refresh"></i></span> 
            </div>
            
          </div>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                  <i class="fa fa-sign-out"></i>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">       
        @yield('content')          
      </div>

      <footer class="footer footer-black  footer-white ">
        <div class="container-fluid">
          <div class="row">
            
            <div class="credits ml-auto">
              <span class="copyright">
                © 2021 - 
                <script>
                  document.write(new Date().getFullYear())
                </script> - Studio Clipagem
              </span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  
  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
  <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>

  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
 
  <!-- Chart JS -->
  <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
  <!--  Notifications Plugin    -->
  <script src="{{ asset('js/plugins/bootstrap-notify.js') }}"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
 
  <script src="{{ asset('js/paper-dashboard.min.js?v=2.0.1') }}" type="text/javascript"></script>
  <script src="{{ asset('js/plugins/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('js/plugins/bootstrap-datetimepicker.js') }}"></script>
  <script src="{{ asset('js/plugins/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('js/plugins/jqcloud.min.js') }}"></script>
  <script src="{{ asset('js/plugins/jquery.loader.min.js') }}"></script>
  <script src="{{ asset('js/plugins/inputTags.jquery.min.js') }}"></script>
  <script src="{{ asset('js/sweetalert2.js') }}"></script>
  <script src="{{ asset('js/jquery.mask.min.js') }}"></script>
  <script src="{{ asset('demo/demo.js') }}"></script>
  <script src="{{ asset('js/custom.js') }}"></script>
  <script src="{{ asset('js/croppie.min.js') }}"></script>
  <script src="{{ asset('js/upload-image.js') }}"></script>
  <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap-multiselect.js') }}"></script>
  <script src="{{ asset('js/jquery.bootstrap-duallistbox.min.js') }}"></script>


  
  <script src="{{ asset('js/select2.js') }}"></script>



  
  <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('js/dropzone.js') }}"></script>
  <script src="{{ asset('js/cropper.js') }}"></script>
  <script src="{{ asset('js/cropper-main.js') }}"></script>
  <script src="{{ asset('js/plugins/chartist.min.js') }}"></script>
  <script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
  @yield('script')
  <script>
    function setFormValidation(id) {
      $(id).validate({
        highlight: function(element) {
          $(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
          $(element).closest('.form-check').removeClass('has-success').addClass('has-danger');
        },
        success: function(element) {
          $(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
          $(element).closest('.form-check').removeClass('has-danger').addClass('has-success');
        },
        errorPlacement: function(error, element) {
          $(element).closest('.form-group').append(error);
        },
      });
    }

    $(document).ready(function() {
      // Javascript method's body can be found in assets/assets-for-demo/js/demo.js
      //demo.initChartsPages();
      
      demo.initDateTimePicker();
      
      setFormValidation('#RegisterValidation');
    });
  </script>
  <script>
    $(document).ready(function() {

      let APP_URL = {!! json_encode(url('/')) !!}

      $('.select2').select2();

      $('#frm_notification_create').validate();
      $('#frm_social_search').validate();
      
      jQuery.extend(jQuery.validator.messages, {
        required: "Campo obrigatório",
        minlength: jQuery.validator.format("Tamanho mínimo do campo é de {0} cadacteres")
      });
      
      $('#datatable').DataTable({
        "pagingType": "full_numbers",
        
        "lengthMenu": [
          [10, 25, 50, -1],
          [10, 25, 50, "Todos"]
        ],
        responsive: true,
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Filtrar",
        }

      });

      $('.datatable_pages').DataTable({
        "pagingType": "full_numbers",
        
        "lengthMenu": [
          [10, 25, 50, -1],
          [10, 25, 50, "All"]
        ],
        responsive: false,
        ordering: false,
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Filtrar",
        }

      });
      
    });
  </script>
  
</body>

</html>