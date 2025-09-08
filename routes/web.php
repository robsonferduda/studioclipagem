<?php

use App\Classes\FbHashtag;
use App\Classes\FbTerm;
use App\Classes\Rule as ClassesRule;
use App\FbPagePost;
use App\Media;
use Illuminate\Support\Facades\Route;

//Route::get('/', 'HomeController@index');
Route::get('/', 'HomeController@site');
Route::get('evento', 'HomeController@evento');
Route::get('/home', 'HomeController@index');
Route::get('/politica-de-privacidade', function () { return view('politica-de-privacidade'); });
Route::get('/termos-de-servico', function () { return view('termos-de-servico'); });

Route::middleware(['web'])->group(function () {

	Auth::routes();

	Route::get('/test-session', function () {
	    session(['test_key' => 'test_valuedddd']);
	    return session('test_key');
	});

	// Route::resource('client', 'ClientController');
	Route::resource('tag', 'TagController');
	// Route::resource('notification', 'NotificacaoController'); // Controller não existe
	Route::resource('usuario', 'UserController');
	Route::resource('perfis','RoleController');
	Route::resource('email', 'EmailController');
	Route::resource('emissora', 'EmissoraController');
	Route::resource('programa', 'ProgramaController');

	Route::get('areas','AreaController@index');
	Route::get('areas/cadastrar','AreaController@cadastrar');
	Route::get('areas/{id}/editar','AreaController@editar');
	Route::get('areas/{id}/remover','AreaController@remover');
	Route::get('areas/executar/web','AreaController@executarWeb');
	Route::get('areas/executar/impresso','AreaController@executarImpresso');

	Route::get('assessorias/clientes','AssessoriaController@clientes');

	Route::post('alterar-data','HomeController@atualizarData');
	Route::get('inicio/estatisticas','HomeController@estatisticas');

	Route::get('import','FonteWebController@importacaoNova');

	Route::get('clientes/usuarios','UserController@insereClientes');
	
	Route::post('areas/inserir','AreaController@inserir');
	Route::post('areas/{id}/atualizar','AreaController@atualizar');

	Route::post('areas/cliente/cadastrar','AreaController@cadastrarAreaCliente');

	Route::match(array('GET', 'POST'),'boletins','BoletimController@index');

	Route::get('boletim/cadastrar','BoletimController@cadastrar');
	Route::get('boletim/{id}/detalhes','BoletimController@detalhes');
	Route::get('boletim/editar/{id}','BoletimController@editar');
	Route::get('boletim/excluir/{id}','BoletimController@destroy');
	Route::get('boletim/{id}/enviar','BoletimController@enviar');
	Route::get('boletim/{id}/resumo','BoletimController@resumo');
	Route::get('boletim/{id}/outlook','BoletimController@outlook');
	Route::get('boletim/{id}/visualizar','BoletimController@visualizar');
	Route::post('boletim/noticias','BoletimController@noticias');
	Route::post('boletim/enviar/lista','BoletimController@enviarLista');
	Route::post('boletim/noticia/adicionar','BoletimController@adicionarNoticia');
	Route::post('boletim/noticia/remover','BoletimController@removerNoticia');
	Route::resource('boletim','BoletimController');

	// Rotas específicas do cliente (devem vir ANTES do resource)
	Route::match(array('GET', 'POST'),'clientes','ClienteController@index');
	Route::match(array('GET', 'POST'),'cliente/noticias','ClienteController@gerarRelatorios');
	Route::get('cliente/relatorios','ClienteController@relatoriosSalvos');
	Route::get('cliente/relatorios/api','ClienteController@listarRelatorios');
	Route::get('cliente/relatorios/historico','ClienteController@relatoriosSalvos');
	Route::match(array('GET', 'POST'),'cliente/relatorios/listar','ClienteController@relatorios');

	// Novas rotas para substituir funcionalidades do Flask app.py
	Route::post('cliente/relatorios/listar-noticias','ClienteController@listarNoticias');
	Route::post('cliente/relatorios/gerar-pdf','ClienteController@gerarRelatorioPDF');
	Route::post('cliente/relatorios/gerar-pdf-web','ClienteController@gerarRelatorioPDFWeb');
	Route::post('cliente/relatorios/gerar-pdf-impresso','ClienteController@gerarRelatorioPDFImpresso');
	Route::get('cliente/{cliente}/relatorios/download/{arquivo}','ClienteController@downloadRelatorio');
	Route::post('cliente/relatorios/adicionar-noticia','ClienteController@adicionarNoticia');
	Route::post('cliente/relatorios/editar-noticia','ClienteController@editarNoticia');
	Route::post('cliente/relatorios/excluir-noticia','ClienteController@excluirNoticia');
	Route::get('cliente/relatorios/noticia/{id}/{tipo}','ClienteController@buscarNoticia');
	Route::post('cliente/relatorios/vincular-noticia-area','ClienteController@vincularNoticiaArea');
	Route::post('cliente/relatorios/upload-imagem','ClienteController@uploadImagem');
	
	// Rotas para gerenciamento de tags
	Route::get('cliente/tags/disponiveis','ClienteController@getTagsDisponiveis');
	Route::post('cliente/tags/noticias','ClienteController@getTagsNoticias');
	Route::post('cliente/tags/adicionar','ClienteController@adicionarTag');
	Route::post('cliente/tags/remover','ClienteController@removerTag');
	
	// Rota para alterar sentimento de notícias
	Route::post('cliente/relatorios/alterar-sentimento','ClienteController@alterarSentimento');
	
	// Rotas para buscar fontes/emissoras/programas para filtros
	Route::get('cliente/fontes/web','ClienteController@obterFontesWeb');
	Route::get('cliente/fontes/impresso','ClienteController@obterFontesImpresso');
	Route::get('cliente/fontes/tv','ClienteController@obterFontesTv');
	Route::get('cliente/fontes/radio','ClienteController@obterFontesRadio');
	
	Route::post('cliente/{id}/areas/reordenar', 'ClienteController@reordenarAreas');
	Route::post('cliente/area/{id}/toggle-situacao', 'AreaController@alternarSituacao');
	
	// Outras rotas específicas de cliente
	Route::post('cliente/selecionar','ClienteController@selecionar');
	Route::get('cliente/paginas-associadas/{client}','ClientController@connectedtPages');
	Route::get('cliente/area/{id}/remover','ClienteController@removerArea');
	Route::get('cliente/area/{id}/situacao','ClienteController@alteraSituacao');
	Route::post('cliente/area/adicionar','ClienteController@adicionarArea');
	Route::get('cliente/flags-midia/{id}', 'ClienteController@flagsMidia');
	Route::get('cliente/configuracoes/{id}', 'ClienteController@configuracoes');
	
	Route::resource('cliente','ClienteController');

	// API routes para dados de relatórios
	Route::get('api/clientes','ClienteController@getClientesApi');
	Route::get('api/cliente/validar','ClienteController@validarCliente');
	Route::get('api/cliente/areas','ClienteController@getAreasClienteRelatorio');
	Route::get('api/cliente/{id}/areas','ClienteController@getAreasClienteEspecifico');

	Route::match(array('GET', 'POST'),'coletas','ColetaController@index');

	Route::match(array('GET', 'POST'),'emissoras/{tipo}','EmissoraController@listar');
	Route::get('emissora/{id}/gravacao/atualiza','EmissoraController@atualizaGravacao');
	Route::post('emissora/{tipo}/adicionar','EmissoraController@store');
	Route::post('emissora/{tipo}/atualizar','EmissoraController@update');
	Route::post('emissoras/horario/adicionar','EmissoraController@adicionarHorarios');

	Route::get('email/cliente/excluir/{id}','EmailController@excluir');
	Route::post('email/cliente/cadastrar','EmailController@cadastrar');

	Route::get('estado/{id}/cidades','EstadoController@getCidades');
	Route::get('estado/siglas','EstadoController@siglas');

	Route::get('exportar/atualizar','ExportarController@atualizar');
	Route::match(array('GET', 'POST'),'exportar/{log?}','ExportarController@index');
	Route::match(array('GET', 'POST'),'importar','ExportarController@importar');
	Route::get('teste','EmailController@teste');

	Route::get('facebook/coletas','FacebookController@coletas');
	Route::get('facebook/postagens','FacebookController@postagens');
	Route::get('facebook/posts', 'FacebookController@listarPostsAjax');

	Route::get('instagram/coletas','InstagramController@coletas');
	Route::get('instagram/postagens','InstagramController@postagens');
	Route::get('instagram/posts', 'InstagramController@listarPostsAjax');
	Route::get('instagram/postagens', 'InstagramController@listarPostagensAjax');

	Route::match(array('GET', 'POST'),'fonte-impresso/listar','FonteImpressoController@listar');

	Route::get('fonte-impresso/cadastrar','FonteImpressoController@cadastrar');
	Route::get('fonte-impresso/{id}/editar','FonteImpressoController@editar');
	Route::get('fonte-impresso/{id}/excluir','FonteImpressoController@excluir');
	Route::get('fonte-impresso/{id}/sessao','FonteImpressoController@sessao');
	Route::get('fonte-impresso/todas','FonteImpressoController@getFontes');
	Route::get('fonte-impresso/{id}/valores/{local}','FonteImpressoController@getValores');
	Route::get('fonte-impresso/secao/excluir/{id}','FonteImpressoController@excluirSecao');
	Route::get('fonte-impresso/{id}/preferencia/atualiza','FonteImpressoController@atualizaPreferencia');
	Route::post('fonte-impresso','FonteImpressoController@inserir');
	Route::post('fonte-impresso/secao','FonteImpressoController@secao');
	Route::post('fonte-impresso/adicionar','FonteImpressoController@adicionar');
	Route::post('fonte-impresso/{id}/atualizar','FonteImpressoController@atualizar');

	Route::get('jornal-impresso/edicao/{edicao}/paginas','JornalImpressoController@paginas');
	Route::match(array('GET', 'POST'),'jornal-impresso/paginas','JornalImpressoController@todasPaginas');

	Route::get('fontes','FonteController@index');
	Route::post('fonte-web/filtrar-situacao','FonteWebController@filtrarSituacao');
	Route::post('fonte-web/prioridade/atualizar','FonteWebController@atualizarPrioridade');
	Route::get('fonte-web/coletas/{origem}/listar/{id_fonte}','FonteWebController@listarColetas');
	Route::get('fonte-web/coletas/{id}','FonteWebController@coletas');
	Route::get('fonte-web/relatorios','FonteWebController@relatorios');
	Route::get('fonte-web/editar/{id}','FonteWebController@edit');
	Route::get('fonte-web/editar/inconsistencia/{id}','FonteWebController@editInconsistencia');
	Route::get('fonte-web/excluir/{id}','FonteWebController@destroy');
	Route::get('fonte-web/importar','FonteWebController@importarNoticia');
	Route::get('fonte-web/inconsistencias','FonteWebController@inconsistencias');
	Route::get('fonte-web/estatisticas/{id}','FonteWebController@estatisticas');
	Route::get('fonte-web/estatisticas/coleta','FonteWebController@estatisticasColeta');
	Route::get('fonte-web/limpar','FonteWebController@limpar');
	Route::get('fonte-web/estatisticas/top/{top}','FonteWebController@getTopColetas');
	Route::get('fonte-web/estatisticas/sem/{top}','FonteWebController@getSemColetas');
	Route::get('fonte-web/estatisticas/coletas/{fonte}','FonteWebController@getColetasByFonte');

	Route::get('fonte-web/buscar/combo', 'FonteWebController@buscar');

	Route::get('fonte-web/atualizar-estado','FonteWebController@atualizarEstado');
	Route::get('fonte-web/atualizar-valor','FonteWebController@atualizarValor');

	Route::get('fonte-web/totais/semana/{id}','FonteWebController@getSemanaColetas');
	ROute::post('fonte-web/inconsistencias/editar','FonteWebController@editarInconsistencia');
	Route::match(array('GET', 'POST'),'fonte-web/listar','FonteWebController@index');
	Route::resource('fonte-web','FonteWebController');

	Route::get('jornal-impresso/web/pagina/download/{id}','JornalImpressoController@getImg');
	Route::get('jornal-impresso/web/download/{id}','JornalImpressoController@getPdf');
	Route::get('impresso','JornalImpressoController@dashboard');
	Route::get('impresso/limpar','FonteImpressoController@limpar');
	Route::get('impresso/coleta/estatisticas','JornalImpressoController@estatisticas');
	Route::match(array('GET', 'POST'),'jornal-impresso/edicoes','JornalImpressoController@edicoes');
	Route::match(array('GET', 'POST'),'jornal-impresso/processamento','JornalImpressoController@processamento');
	Route::match(array('GET', 'POST'),'jornal-impresso/monitoramento','JornalImpressoController@monitoramento');
	Route::match(array('GET', 'POST'),'jornal-impresso/noticias','JornalImpressoController@index');
	Route::match(array('GET', 'POST'),'impresso/noticias','JornalImpressoController@index');

	Route::match(array('GET', 'POST'),'jornal-impresso/buscar','JornalImpressoController@buscar');
	Route::post('jornal-impresso/monitoramento/{cliente}/listar','JornalImpressoController@listarMonitoramento');
	Route::get('jornal-impresso/processar','JornalImpressoController@processar');
	Route::get('jornal-impresso/noticia/extrair/{monitoramento}/{tipo}/{id}','JornalImpressoController@extrair');
	Route::get('jornal-impresso/noticia/editar/{id}','JornalImpressoController@editar');
	Route::get('jornal-impresso/conteudo/{id_noticia}/monitoramento/{id_monitoramento}','JornalImpressoController@destacaConteudo');
	Route::get('jornal-impresso/{id}/remover','JornalImpressoController@remover');
	Route::get('jornal-impresso/emissoras','JornalImpressoController@loadEmissoras');

	Route::get('jornal-impresso/pendentes/listar','JornalImpressoController@listarPendentes');
	Route::match(array('GET', 'POST'),'jornal-impresso/uploads','JornalImpressoController@upload');
	Route::get('jornal-impresso/noticia/{id}','JornalImpressoController@detalhes');
	Route::post('jornal-impresso/upload','JornalImpressoController@uploadFiles');

	Route::get('noticias/estatisticas/areas','NoticiaController@estatisticasArea');
	Route::get('noticia/{id}/vinculo/remover','NoticiaController@removerVinculoAssincrono');
	Route::get('noticia/{id}/vinculo/excluir','NoticiaController@removerVinculo');
	Route::get('noticia/{id}/tipo/{tipo}/cliente/{cliente}/sentimento/{sentimento}/atualizar','NoticiaController@atualizarSentimento');
	Route::get('noticia/{id}/tipo/{tipo}/clientes', 'NoticiaController@clientesParciais');
	Route::post('noticia/sentimento/atualizar', 'NoticiaController@atualizarSentimentoAssincrono');

	Route::post('noticia-impressa/upload','NoticiaImpressaController@upload'); 

	//Route::resource('noticia-impressa','NoticiaImpressaController');
	Route::get('noticia-impressa/cadastrar','NoticiaImpressaController@cadastrar');  // Rota Antiga
	Route::get('noticia-impressa/cliente/{cliente}/copiar/{id}','NoticiaImpressaController@copiar'); // Rota Antiga
	Route::get('noticia-impressa/cliente/{cliente}/editar/{id}','NoticiaImpressaController@editar'); // Rota Antiga
	Route::post('noticia-impressa/upload','NoticiaImpressaController@upload'); 

	Route::resource('noticia-impressa','NoticiaImpressaController');
	Route::match(array('GET', 'POST'),'noticias/impresso','NoticiaImpressaController@index');
	Route::get('noticia/impresso/novo','NoticiaImpressaController@cadastrar');
	Route::get('noticia/impresso/fonte/sessoes/{id}','NoticiaImpressaController@getSecoes');
	Route::get('noticia-impressa/{id}/editar','NoticiaImpressaController@editar');
	Route::get('noticia-impressa/{id}/excluir','NoticiaImpressaController@excluir');
	Route::get('noticia-impressa/imagem/download/{id}','NoticiaImpressaController@getImagem');
	Route::get('noticias/impresso/limpar','NoticiaImpressaController@limpar');
	Route::get('noticia/impressa/imagem-path/{id}','NoticiaImpressaController@getImagemView');
	Route::get('noticia/impresso/atualiza-retorno','NoticiaImpressaController@calcularValorRetornoImpresso');
	Route::get('noticia/impresso/retorno','NoticiaImpressaController@retorno');
	
	Route::resource('noticia-radio','NoticiaRadioController');
	Route::match(array('GET', 'POST'),'noticias/radio','NoticiaRadioController@index');
	Route::get('noticia-radio/{id}/editar','NoticiaRadioController@editar');
	Route::get('noticia-radio/{id}/excluir','NoticiaRadioController@excluir');

	Route::match(array('GET', 'POST'),'radio/emissoras','EmissoraController@listar');

	Route::get('radio/noticias/cadastrar','NoticiaRadioController@cadastrar');
	Route::get('radio/noticias/{id}/editar','NoticiaRadioController@editar');
	Route::get('radio/noticias/{id}/remover','NoticiaRadioController@remover');
	Route::get('radio/noticias/{id}/cliente/{cliente}/editar','NoticiaRadioController@editar');
	Route::get('radio/noticias/{id}/cliente/{cliente}/remover','NoticiaRadioController@remover');
	Route::get('radio/noticias/{id}/download','NoticiaRadioController@download');
	Route::get('radio/noticias/estatisticas','NoticiaRadioController@getEstatisticas');
	Route::get('radio/noticias/retorno','NoticiaRadioController@retorno');
	Route::get('radio/noticias/atualizar-valores','NoticiaRadioController@calcularValorRetornoRadio');
	Route::get('radio/adjacentes/{id}/monitoramento/{monitoramento}','NoticiaRadioController@getDadosAudio');
	
	Route::get('noticia/radio/limpar-filtros','NoticiaRadioController@limparFiltrosRadio');
	Route::get('radio/conteudo/{id_noticia}/monitoramento/{id_monitoramento}','NoticiaRadioController@destacaConteudo');
	Route::get('noticia/radio/clientes/{noticia}','NoticiaRadioController@clientes');
	Route::post('radio/noticias/inserir','NoticiaRadioController@inserir');
	Route::post('radio/noticias/{id}/atualizar','NoticiaRadioController@atualizar');
	Route::post('radio/noticias/upload','NoticiaRadioController@upload');
	Route::post('noticia-radio/upload','NoticiaRadioController@upload');

	//Route::match(array('GET', 'POST'),'buscar-web','JornalWebController@index');
	Route::get('noticia/web/importar-imagens','NoticiaWebController@copiaImagens');
	Route::get('noticia/web/importar-imagem/{id}','NoticiaWebController@copiaImagemIndividual');
	Route::get('noticia/web/valores','NoticiaWebController@valores');
	Route::get('noticia/web/novo','NoticiaWebController@create');
	Route::get('noticia/web/{id}/editar','NoticiaWebController@edit');
	Route::get('noticia/web/{id}/excluir','NoticiaWebController@excluir');
	Route::post('noticia/web/excluir-lote','NoticiaWebController@excluirLote');
	Route::get('noticia/web/{id}/ver','NoticiaWebController@show');
	Route::get('noticia/web/dashboard','JornalWebController@dashboard');
	Route::get('noticia/web/detalhes/{id}','NoticiaWebController@detalhes');
	Route::get('noticia/web/clientes/{noticia}','NoticiaWebController@clientes');
	Route::get('noticia/web/estatisticas/{id}','NoticiaWebController@getEstatisticas');
	Route::get('noticia/web/{id}/reprint','NoticiaWebController@reprint');
	Route::get('noticia-web/imagem/download/{id}','NoticiaWebController@getImagem');
	Route::get('fonte-web/{id}/valores/{local}','NoticiaWebController@getValores');
	Route::post('noticia/web/prints/recuperar','NoticiaWebController@printsRecuperar');
	Route::get('web/conteudo/{id_noticia}/monitoramento/{id_monitoramento}','NoticiaWebController@destacaConteudo');
	Route::post('noticia-web/upload','NoticiaWebController@upload');
	Route::match(array('GET', 'POST'),'noticia/web','NoticiaWebController@index');
	Route::match(array('GET', 'POST'),'noticia/web/coletas','NoticiaWebController@coletas');
	Route::match(array('GET', 'POST'),'noticia/web/monitoramento','NoticiaWebController@monitoramento');
	Route::match(array('GET', 'POST'),'noticia/web/prints','NoticiaWebController@prints');
	Route::match(array('GET', 'POST'),'buscar-web','NoticiaWebController@index');
	Route::resource('noticia-web','NoticiaWebController');
	Route::get('noticia/web/atualiza-retorno','NoticiaWebController@calcularValorRetornoWeb');
	Route::get('noticia/web/retorno','NoticiaWebController@retorno');
	Route::get('noticia/web/print/{id}','NoticiaWebController@getPrint');
	Route::get('noticia/web/print','NoticiaWebController@getPrintS3');
	Route::get('noticia/web/limpar-filtros','NoticiaWebController@limparFiltrosWeb');

	Route::get('noticia/impresso/clientes/{noticia}','NoticiaImpressaController@clientes');
	
	Route::get('jornal-web','JornalWebController@index');
	Route::get('jornal-web/estatisticas','JornalWebController@estatisticas');
	Route::get('jornal-web/cadastrar','JornalWebController@cadastrar');
	Route::get('jornal-web/fontes','JornalWebController@fontes');
	Route::get('jornal-web/listar','JornalWebController@listar');
	Route::get('jornal-web/noticia/{id}','JornalWebController@detalhes');
	Route::get('jornal-web/noticia/estatisticas/{id}','JornalWebController@getEstatisticas');

	Route::match(array('GET', 'POST'),'monitoramentos','MonitoramentoController@index');
	Route::match(array('GET', 'POST'),'monitoramento/listar','MonitoramentoController@listar');
	Route::match(array('GET', 'POST'),'monitoramento/exportacao/web','MonitoramentoController@exportacaoWeb');
	Route::get('monitoramento/cliente/{cliente}','MonitoramentoController@buscar');
	Route::get('monitoramento/novo','MonitoramentoController@novo');
	Route::get('monitoramento/executar','MonitoramentoController@executar');
	Route::get('monitoramento/{id}/executar','MonitoramentoController@executar');
	Route::get('monitoramento/{id}/historico','MonitoramentoController@historico');
	Route::get('monitoramento/{id}/atualizar-status','MonitoramentoController@atualizarStatus');
	Route::get('monitoramento/{id}/noticias','MonitoramentoController@noticias');
	Route::get('monitoramento/{id}/todas-noticias','MonitoramentoController@noticiasMonitoramento');
	Route::get('monitoramento/{id}/editar','MonitoramentoController@editar');
	Route::get('monitoramento/{id}/excluir','MonitoramentoController@excluir');
	Route::get('monitoramento/cliente/{id}','MonitoramentoController@getMonitoramentoCliente');
	Route::get('monitoramento/limpar/{id}','MonitoramentoController@limparMonitoramento');
	Route::get('monitoramento/clonar/{id}','MonitoramentoController@clonar');
	Route::get('monitoramento/qualidade','MonitoramentoController@qualidade');
	Route::post('monitoramento/update','MonitoramentoController@update');
	Route::post('monitoramento/filtrar/conteudo','MonitoramentoController@getConteudo');
	Route::post('monitoramento/filtrar','MonitoramentoController@filtrar');
	Route::post('monitoramento/filtrar/impresso','MonitoramentoController@filtrarImpresso');
	Route::post('monitoramento/filtrar/radio','MonitoramentoController@filtrarRadio');
	Route::post('monitoramento/filtrar/tv','MonitoramentoController@filtrarTv');
	Route::post('monitoramento/create','MonitoramentoController@create');

	Route::get('monitoramento/executar/web/{grupo}','MonitoramentoController@executarWeb');
	Route::get('monitoramento/executar/radio','MonitoramentoController@executarRadio');
	Route::get('monitoramento/executar/instagram','MonitoramentoController@executarInstagram');
	Route::get('monitoramento/executar/facebook','MonitoramentoController@executarFacebook');
	Route::get('monitoramento/executar/impresso','MonitoramentoController@executarImpresso');
	Route::get('monitoramento/executar/tv','MonitoramentoController@executarTv');
	Route::get('monitoramento/cliente/{id_cliente}/{flag}','MonitoramentoController@getMonitoramento');
	Route::get('monitoramento/{id_monitoramento}/fontes','MonitoramentoController@getFontesMonitoramento');
	Route::get('monitoramento/{tipo}/emissoras/{monitoramento}','MonitoramentoController@loadEmissoras');

	Route::get('online','UserController@online');

	Route::get('php','HomeController@php');

	Route::post('pauta','PautaController@store');
	Route::match(array('GET', 'POST'),'pautas','PautaController@index');
	Route::get('pauta/cadastrar','PautaController@cadastrar');
	Route::get('pauta/{id}/vincular','PautaController@vincular');
	Route::get('pauta/{id}/remover','PautaController@remover');
	Route::post('pauta/vincular','PautaController@vincularNoticia');
	Route::post('pauta/desvincular','PautaController@desvincularNoticia');

	Route::match(array('GET', 'POST'),'emissoras','EmissoraController@index');
	Route::match(array('GET', 'POST'),'noticia/radio/coletas','EmissoraController@coletas');
	Route::match(array('GET', 'POST'),'noticia/radio/monitoramento','NoticiaRadioController@monitoramento');
	Route::get('noticia/radio/{monitoramento}/extrair/{id}','NoticiaRadioController@extrair');
	Route::get('emissoras/{tipo}/novo','EmissoraController@novo');
	Route::get('emissoras/radio/limpar','EmissoraController@limpar');
	Route::get('emissora/radio/{id}/segundo', 'EmissoraController@valorSegundo');
	Route::get('radio/emissora/{id}/horarios','EmissoraController@horarios');
	Route::post('radio/emissora/horario/atualizar','EmissoraController@atualizarHorarios');
	Route::post('radio/emissora/horario/adicionar','EmissoraController@adicionarHorarios');
	Route::get('radio/emissora/horario/excluir/{horario}','EmissoraController@excluirHorario');
	Route::get('radio/arquivos/detalhes/{id}','EmissoraController@detalhes');
	Route::get('radio/dashboard','NoticiaRadioController@dashboard');

	Route::get('radio/estatisticas','NoticiaRadioController@estatisticas');
	Route::match(array('GET', 'POST'),'radios','NoticiaRadioController@index');

	Route::match(array('GET', 'POST'),'radio/emissoras/programas','ProgramaController@index');
	Route::get('radio/emissoras/programas/novo','ProgramaController@novo');
	Route::get('radio/emissoras/programas/editar/{id}','ProgramaController@editar');
	Route::get('radio/programa/{id}/dados', 'ProgramaController@dadosPrograma');

	Route::get('tags','TagController@index');
	Route::get('tags/cadastrar','TagController@cadastrar');
	Route::get('tags/{id}/remover','TagController@destroy');

	Route::get('tv/noticias/limpar-filtros', 'NoticiaTvController@limparFiltrosTv');
	Route::get('tv/dashboard','NoticiaTvController@dashboard');
	Route::match(array('GET', 'POST'),'tv/emissoras','EmissoraTvController@index');
	Route::get('tv/emissoras/editar/{id}','EmissoraTvController@editar');
	Route::get('tv/emissoras/novo','EmissoraTvController@novo');
	Route::get('tv/emissora/{id}/programas','EmissoraTvController@programas');
	Route::get('tv/emissoras/limpar','EmissoraTvController@limpar');
	Route::get('tv/emissora/{id}/gravacao/atualiza','EmissoraTvController@atualizaGravacao');
	Route::get('tv/emissora/{id}/horarios','EmissoraTvController@horarios');
	Route::post('tv/emissoras/horario/adicionar','EmissoraTvController@adicionarHorarios');
	Route::post('tv/emissoras/adicionar','EmissoraTvController@adicionar');
	Route::post('tv/emissoras/atualizar','EmissoraTvController@atualizar');

	Route::match(array('GET', 'POST'),'tv/emissoras/programas','ProgramaTvController@index');
	Route::get('tv/emissoras/programas/limpar','ProgramaTvController@limpar');
	Route::get('tv/emissoras/programas/novo','ProgramaTvController@novo');
	Route::get('tv/emissoras/programas/editar/{id}','ProgramaTvController@editar');
	Route::get('tv/emissora/programas/{id}/horarios','ProgramaTvController@horarios');
	Route::get('tv/emissora/programa/{id}/gravacao/atualiza','ProgramaTvController@atualizaGravacao');
	Route::get('tv/emissora/horario/excluir/{horario}','ProgramaTvController@excluirHorario');
	Route::post('tv/emissoras/programa/horario/adicionar','ProgramaTvController@adicionarHorarios');
	Route::post('tv/emissoras/programas/adicionar','ProgramaTvController@adicionar');
	Route::post('tv/emissoras/programas/atualizar','ProgramaTvController@atualizar');

	Route::get('tv/estatisticas','NoticiaTvController@estatisticas');
	Route::get('tv/noticias/retorno','NoticiaTvController@retorno');
	Route::get('tv/noticias/atualizar-valores','NoticiaTvController@calcularValorRetornoRadio');

	Route::get('tv/decupagem','NoticiaTvController@decupagem');

	Route::get('tv/decupar','NoticiaTvController@decupar');
	Route::get('tv/videos/estatisticas','VideosController@getEstatisticas');
	Route::get('tv/noticias/estatisticas','NoticiaTvController@getEstatisticas');
	Route::get('tv/noticias/cadastrar','NoticiaTvController@cadastrar');
	Route::get('noticia-tv/decupagem/listar','NoticiaTvController@listarArquivos');
	Route::get('tv/noticias/{id}/editar','NoticiaTvController@editar');
	Route::get('tv/noticias/{id}/remover','NoticiaTvController@remover');
	Route::get('noticia/tv/{id}/excluir','NoticiaTvController@remover');
	Route::get('tv/noticias/{id}/cliente/{cliente}/editar','NoticiaTvController@editar');
	Route::get('tv/noticias/{id}/cliente/{cliente}/remover','NoticiaTvController@remover');
	Route::post('noticia-tv/upload','NoticiaTvController@upload');
	Route::post('tv/noticias/inserir','NoticiaTvController@inserir');
	Route::post('tv/noticias/{id}/atualizar','NoticiaTvController@atualizar');
	Route::post('noticia_tv/decupagem/salvar','NoticiaTvController@salvarDecugem');
	Route::post('noticia_tv/decupagem/processar','NoticiaTvController@processar');
	Route::post('tv/noticias/upload','NoticiaTvController@upload');
	Route::post('tv/decupagem/upload','NoticiaTvController@uploadWord');
	Route::get('tv/conteudo/{id_noticia}/monitoramento/{id_monitoramento}','NoticiaTvController@destacaConteudo');

	/* Notícas TV */
	Route::match(array('GET', 'POST'),'noticia/tv/coletas','VideosController@index');
	Route::match(array('GET', 'POST'),'noticia/tv/monitoramento','NoticiaTvController@monitoramento');
	Route::match(array('GET', 'POST'),'noticias/tv','NoticiaTvController@index');
	Route::get('tv/video/detalhes/{id}','VideosController@detalhes');
	Route::get('noticia/tv/{monitoramento}/extrair/{id}','NoticiaTvController@extrair');
	Route::get('noticia/tv/{id}/editar','NoticiaTvController@editar');
	Route::get('noticia/tv/clientes/{noticia}','NoticiaTvController@clientes');

	Route::get('cliente/get/json','ClientController@json');
	Route::get('client/accounts/facebook/{cliente}','ClientController@getFacebookAccounts');
	Route::get('client/hashtags/{cliente}','ClientController@getHashtags');
	Route::get('client/emails/{cliente}','ClientController@emails');


	Route::get('configuracoes','ConfiguracoesController@index');
	Route::post('configuracoes/flag-regras/atualizar','ConfiguracoesController@atualizarFlag');
	Route::post('configuracoes/cliente/selecionar','ConfiguracoesController@selecionarCliente');
	Route::post('configuracoes/periodo/selecionar','ConfiguracoesController@selecionarPeriodo');

	Route::get('email/situacao/{id}','EmailController@atualizarSituacao');

	Route::get('permissoes','PermissaoController@index');
	Route::get('permissoes/{id}/users','PermissaoController@users');
	Route::get('permissoes/{id}/perfis','PermissaoController@perfis');

	Route::get('perfis','RoleController@index');
	Route::get('perfil/novo','RoleController@create');

	Route::get('pdf','RelatorioController@pdf');
	Route::match(array('GET', 'POST'),'relatorios','ClienteController@gerarRelatorios');
	Route::get('relatorios/unificado','RelatorioController@unificado');
	Route::get('relatorios/clipping','RelatorioController@clipping');
	Route::get('relatorios/clipping/{arquivo}','RelatorioController@getClipping');


	Route::match(array('GET', 'POST'),'noticias','ClienteController@noticias');

	Route::get('perfil','UserController@perfil');

	Route::get('usuarios','UserController@index');
	Route::get('usuario/historico/{id}','UserController@historico');
	Route::get('usuarios/excluir/{id}','UserController@excluirUsuarioCliente');
	Route::post('usuario/cliente/cadastrar','UserController@usuarioCliente');

	Route::get('role/permissions/{role}','RoleController@permissions');
	Route::post('role/permission/{role}','RoleController@addPermission');

	Route::get('transcricao','ProcessamentoController@radios');
	Route::get('transcricao/baixar/{pasta}','ProcessamentoController@baixar');
	Route::get('transcricao/processar/{pasta}','ProcessamentoController@processar');
	Route::get('transcricao/audios/{emissora}','ProcessamentoController@audios');
	Route::get('processamento','ProcessamentoController@index');

	Route::match(array('GET', 'POST'),'relatorios','RelatorioController@index');
	Route::get('relatorios/{tipo}/pdf/{id}','RelatorioController@pdfIndividual');

	Route::match(array('GET', 'POST'),'relatorios/teste','RelatorioController@teste');

	Route::get('leitura','RelatorioController@word');

	Route::get('dashboard/grafico-midias', 'HomeController@graficoMidias');

	Route::get('files/{file_name}', function($file_name = null)
	{
	    $path = storage_path().'/'.'app'.'/public/'.$file_name;
	    if (file_exists($path)) {
	        return Response::download($path);
	    }
	});
});