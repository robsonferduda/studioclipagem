<?php

use App\Classes\FbHashtag;
use App\Classes\FbTerm;
use App\Classes\Rule as ClassesRule;
use App\FbPagePost;
use App\Media;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/politica-de-privacidade', function () { return view('politica-de-privacidade'); });
Route::get('/termos-de-servico', function () { return view('termos-de-servico'); });

Auth::routes();

// Route::resource('client', 'ClientController');
Route::resource('tag', 'TagController');
Route::resource('notification', 'NotificacaoController');
Route::resource('usuario', 'UserController');
Route::resource('email', 'EmailController');
Route::resource('emissora', 'EmissoraController');
Route::resource('programa', 'ProgramaController');

Route::get('areas','AreaController@index');
Route::get('areas/cadastrar','AreaController@cadastrar');
Route::get('areas/{id}/editar','AreaController@editar');
Route::get('areas/{id}/remover','AreaController@remover');

Route::get('assessorias/clientes','AssessoriaController@clientes');

Route::post('alterar-data','HomeController@atualizarData');
Route::get('inicio/estatisticas','HomeController@estatisticas');

Route::get('import','FonteWebController@importacaoNova');


Route::post('areas/inserir','AreaController@inserir');
Route::post('areas/{id}/atualizar','AreaController@atualizar');

Route::post('areas/cliente/cadastrar','AreaController@cadastrarAreaCliente');

Route::get('boletins','BoletimController@index');
Route::get('boletim/{id}','BoletimController@detalhes');
Route::get('boletim/{id}/enviar','BoletimController@enviar');
Route::get('boletim/{id}/outlook','BoletimController@outlook');
Route::get('boletim/{id}/visualizar','BoletimController@visualizar');
Route::post('boletim/enviar/lista','BoletimController@enviarLista');

Route::resource('cliente','ClienteController');
Route::match(array('GET', 'POST'),'clientes','ClienteController@index');

Route::match(array('GET', 'POST'),'coletas','ColetaController@index');

Route::match(array('GET', 'POST'),'emissoras/{tipo}','EmissoraController@listar');
Route::get('emissora/{id}/gravacao/atualiza','EmissoraController@atualizaGravacao');
Route::post('emissora/{tipo}/adicionar','EmissoraController@store');
Route::post('emissora/{tipo}/atualizar','EmissoraController@update');
Route::post('emissoras/horario/adicionar','EmissoraController@adicionarHorarios');

Route::match(array('GET', 'POST'),'programas/{tipo}','ProgramaController@index');
Route::get('programa/{tipo}/novo','ProgramaController@novo');

Route::get('email/cliente/excluir/{id}','EmailController@excluir');
Route::post('email/cliente/cadastrar','EmailController@cadastrar');

Route::get('estado/{id}/cidades','EstadoController@getCidades');

Route::get('exportar/atualizar','ExportarController@atualizar');
Route::match(array('GET', 'POST'),'exportar/{log?}','ExportarController@index');
Route::match(array('GET', 'POST'),'importar','ExportarController@importar');
Route::post('teste','ExportarController@teste');

Route::match(array('GET', 'POST'),'fonte-impresso/listar','FonteImpressoController@listar');

Route::get('fonte-impresso/cadastrar','FonteImpressoController@cadastrar');
Route::get('fonte-impresso/{id}/editar','FonteImpressoController@editar');
Route::get('fonte-impresso/{id}/excluir','FonteImpressoController@excluir');
Route::get('fonte-impresso/{id}/sessao','FonteImpressoController@sessao');
Route::get('fonte-impresso/secao/excluir/{id}','FonteImpressoController@excluirSecao');
Route::post('fonte-impresso','FonteImpressoController@inserir');
Route::post('fonte-impresso/secao','FonteImpressoController@secao');
Route::post('fonte-impresso/{id}/atualizar','FonteImpressoController@atualizar');

Route::get('jornal-impresso/edicao/{edicao}/paginas','JornalImpressoController@paginas');
Route::match(array('GET', 'POST'),'jornal-impresso/paginas','JornalImpressoController@todasPaginas');

Route::get('fontes','FonteController@index');
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

Route::get('fonte-web/atualizar-estado','FonteWebController@atualizarEstado');
Route::get('fonte-web/atualizar-valor','FonteWebController@atualizarValor');

Route::get('fonte-web/totais/semana/{id}','FonteWebController@getSemanaColetas');
ROute::post('fonte-web/inconsistencias/editar','FonteWebController@editarInconsistencia');
Route::match(array('GET', 'POST'),'fonte-web/listar','FonteWebController@index');
Route::resource('fonte-web','FonteWebController');

Route::get('jornal-impresso/web/download/{id}','JornalImpressoController@getPdf');
Route::get('impresso','JornalImpressoController@dashboard');
Route::get('impresso/limpar','FonteImpressoController@limpar');
Route::get('impresso/coleta/estatisticas','JornalImpressoController@estatisticas');
Route::match(array('GET', 'POST'),'jornal-impresso/web','JornalImpressoController@web');
Route::match(array('GET', 'POST'),'jornal-impresso/processamento','JornalImpressoController@processamento');
Route::match(array('GET', 'POST'),'jornal-impresso/monitoramento','JornalImpressoController@monitoramento');
Route::match(array('GET', 'POST'),'jornal-impresso/noticias','JornalImpressoController@index');
Route::match(array('GET', 'POST'),'impresso/noticias','JornalImpressoController@index');

Route::post('jornal-impresso/monitoramento/{cliente}/listar','JornalImpressoController@listarMonitoramento');
Route::get('jornal-impresso/processar','JornalImpressoController@processar');
Route::get('jornal-impresso/noticia/extrair/{tipo}/{id}','JornalImpressoController@extrair');
Route::get('jornal-impresso/noticia/editar/{id}','JornalImpressoController@editar');

Route::get('jornal-impresso/pendentes/listar','JornalImpressoController@listarPendentes');
Route::get('jornal-impresso/upload','JornalImpressoController@upload');
Route::get('jornal-impresso/noticia/{id}','JornalImpressoController@detalhes');
Route::post('jornal-impresso/upload','JornalImpressoController@uploadFiles');

Route::post('noticia-impressa/upload','NoticiaImpressaController@upload'); 
Route::resource('noticia-impressa','NoticiaImpressaController');

//Route::resource('noticia-impressa','NoticiaImpressaController');
Route::get('noticia-impressa/cadastrar','NoticiaImpressaController@cadastrar');  // Rota Antiga
Route::get('noticia-impressa/cliente/{cliente}/copiar/{id}','NoticiaImpressaController@copiar'); // Rota Antiga
Route::get('noticia-impressa/cliente/{cliente}/editar/{id}','NoticiaImpressaController@editar'); // Rota Antiga
Route::post('noticia-impressa/upload','NoticiaImpressaController@upload'); 

Route::get('noticia/impresso/cadastrar','NoticiaImpressaController@cadastrar');


//Route::match(array('GET', 'POST'),'buscar-web','JornalWebController@index');

Route::match(array('GET', 'POST'),'buscar-web','NoticiaWebController@index');
Route::get('noticia/web','NoticiaWebController@index');
Route::get('noticia/web/cadastrar','NoticiaWebController@cadastrar');
Route::get('noticia/web/dashboard','NoticiaWebController@dashboard');
Route::get('noticia/web/detalhes/{id}','NoticiaWebController@detalhes');
Route::get('noticia/web/estatisticas/{id}','NoticiaWebController@getEstatisticas');
Route::resource('noticia-web','NoticiaWebController');

Route::get('jornal-web','JornalWebController@index');
Route::get('jornal-web/estatisticas','JornalWebController@estatisticas');
Route::get('jornal-web/cadastrar','JornalWebController@cadastrar');
Route::get('jornal-web/fontes','JornalWebController@fontes');
Route::get('jornal-web/listar','JornalWebController@listar');
Route::get('jornal-web/noticia/{id}','JornalWebController@detalhes');
Route::get('jornal-web/noticia/estatisticas/{id}','JornalWebController@getEstatisticas');

Route::match(array('GET', 'POST'),'monitoramento','MonitoramentoController@index');
Route::match(array('GET', 'POST'),'monitoramento/listar','MonitoramentoController@listar');
Route::get('monitoramento/cliente/{cliente}','MonitoramentoController@buscar');
Route::get('monitoramento/novo','MonitoramentoController@novo');
Route::get('monitoramento/executar','MonitoramentoController@executar');
Route::get('monitoramento/{id}/atualizar-status','MonitoramentoController@atualizarStatus');
Route::get('monitoramento/{id}/noticias','MonitoramentoController@noticias');
Route::get('monitoramento/cliente/{id}','MonitoramentoController@getMonitoramentoCliente');
Route::post('monitoramento/filtrar/conteudo','MonitoramentoController@getConteudo');
Route::post('monitoramento/filtrar','MonitoramentoController@filtrar');

Route::post('pauta','PautaController@store');
Route::match(array('GET', 'POST'),'pautas','PautaController@index');
Route::get('pauta/cadastrar','PautaController@cadastrar');
Route::get('pauta/{id}/vincular','PautaController@vincular');
Route::get('pauta/{id}/remover','PautaController@remover');
Route::post('pauta/vincular','PautaController@vincularNoticia');
Route::post('pauta/desvincular','PautaController@desvincularNoticia');

Route::match(array('GET', 'POST'),'emissoras','EmissoraController@index');
Route::match(array('GET', 'POST'),'radio/arquivos','EmissoraController@arquivos');
Route::match(array('GET', 'POST'),'radio/noticias','NoticiaRadioController@index');
Route::get('emissoras/{tipo}/novo','EmissoraController@novo');
Route::get('radio/emissora/{id}/horarios','EmissoraController@horarios');
Route::post('radio/emissora/horario/atualizar','EmissoraController@atualizarHorarios');
Route::get('radio/emissora/horario/excluir/{horario}','EmissoraController@excluirHorario');
Route::get('radio/arquivos/detalhes/{id}','EmissoraController@detalhes');
Route::get('radio/dashboard','NoticiaRadioController@dashboard');

Route::get('radio/estatisticas','NoticiaRadioController@estatisticas');
Route::match(array('GET', 'POST'),'radios','NoticiaRadioController@index');

Route::get('radio/noticias/cadastrar','NoticiaRadioController@cadastrar');
Route::get('radio/noticias/{id}/editar','NoticiaRadioController@editar');
Route::get('radio/noticias/{id}/remover','NoticiaRadioController@remover');
Route::get('radio/noticias/{id}/cliente/{cliente}/editar','NoticiaRadioController@editar');
Route::get('radio/noticias/{id}/cliente/{cliente}/remover','NoticiaRadioController@remover');
Route::get('radio/noticias/{id}/download','NoticiaRadioController@download');
Route::get('radio/noticias/estatisticas','NoticiaRadioController@getEstatisticas');
Route::post('radio/noticias/inserir','NoticiaRadioController@inserir');
Route::post('radio/noticias/{id}/atualizar','NoticiaRadioController@atualizar');
Route::post('radio/noticias/upload','NoticiaRadioController@upload');

Route::get('tags','TagController@index');
Route::get('tags/cadastrar','TagController@cadastrar');
Route::get('tags/{id}/remover','TagController@destroy');

Route::get('tv/dashboard','NoticiaTvController@dashboard');
Route::get('tv/emissoras','EmissoraTvController@index');
Route::get('tv/emissoras/editar/{id}','EmissoraTvController@editar');
Route::get('tv/emissoras/novo','EmissoraTvController@novo');
Route::get('tv/emissoras/programas/{id}','EmissoraTvController@programas');
Route::post('tv/emissoras/adicionar','EmissoraTvController@adicionar');
Route::post('tv/emissoras/atualizar','EmissoraTvController@atualizar');

Route::get('tv/emissoras/programas','ProgramaTvController@index');
Route::get('tv/emissoras/programas/novo','ProgramaTvController@novo');
Route::get('tv/emissoras/programas/editar/{id}','ProgramaTvController@editar');
Route::get('tv/emissora/programas/{id}/horarios','ProgramaTvController@horarios');
Route::post('tv/emissoras/horario/adicionar','ProgramaTvController@adicionarHorarios');
Route::post('tv/emissora/horario/atualizar','ProgramaTvController@atualizarHorarios');
Route::post('tv/emissoras/programas/adicionar','ProgramaTvController@adicionar');
Route::post('tv/emissoras/programas/atualizar','ProgramaTvController@atualizar');

Route::get('tv/estatisticas','NoticiaTvController@estatisticas');

Route::match(array('GET', 'POST'),'tv/noticias','NoticiaTvController@index');
Route::get('tv/decupagem','NoticiaTvController@decupagem');

Route::get('tv/decupar','NoticiaTvController@decupar');
Route::get('tv/videos/estatisticas','VideosController@getEstatisticas');
Route::get('tv/noticias/estatisticas','NoticiaTvController@getEstatisticas');
Route::get('tv/noticias/cadastrar','NoticiaTvController@cadastrar');
Route::get('noticia-tv/decupagem/listar','NoticiaTvController@listarArquivos');
Route::get('tv/noticias/{id}/editar','NoticiaTvController@editar');
Route::get('tv/noticias/{id}/remover','NoticiaTvController@remover');
Route::get('tv/noticias/{id}/cliente/{cliente}/editar','NoticiaTvController@editar');
Route::get('tv/noticias/{id}/cliente/{cliente}/remover','NoticiaTvController@remover');
Route::post('tv/noticias/inserir','NoticiaTvController@inserir');
Route::post('tv/noticias/{id}/atualizar','NoticiaTvController@atualizar');
Route::post('noticia_tv/decupagem/salvar','NoticiaTvController@salvarDecugem');
Route::post('noticia_tv/decupagem/processar','NoticiaTvController@processar');
Route::post('tv/noticias/upload','NoticiaTvController@upload');
Route::post('tv/decupagem/upload','NoticiaTvController@uploadWord');

Route::match(array('GET', 'POST'),'tv/videos','VideosController@index');
Route::get('tv/video/detalhes/{id}','VideosController@detalhes');

Route::get('cliente/get/json','ClientController@json');
Route::get('client/accounts/facebook/{cliente}','ClientController@getFacebookAccounts');
Route::get('client/hashtags/{cliente}','ClientController@getHashtags');
Route::get('client/emails/{cliente}','ClientController@emails');
Route::post('cliente/selecionar','ClientController@selecionar');
Route::get('cliente/paginas-associadas/{client}','ClientController@connectedtPages');

Route::post('cliente/area/adicionar','ClienteController@adicionarArea');

Route::get('configuracoes','ConfiguracoesController@index');
Route::post('configuracoes/flag-regras/atualizar','ConfiguracoesController@atualizarFlag');
Route::post('configuracoes/cliente/selecionar','ConfiguracoesController@selecionarCliente');
Route::post('configuracoes/periodo/selecionar','ConfiguracoesController@selecionarPeriodo');

Route::get('email/situacao/{id}','EmailController@atualizarSituacao');

Route::get('permissoes','PermissaoController@index');
Route::get('permissoes/{id}/users','PermissaoController@users');
Route::get('permissoes/{id}/perfis','PermissaoController@perfis');
Route::get('perfis','RoleController@index');

Route::get('pdf','RelatorioController@pdf');

Route::get('perfil','UserController@perfil');

Route::get('usuarios','UserController@index');
Route::get('usuarios/excluir/{id}','UserController@excluir');

Route::get('role/permissions/{role}','RoleController@permissions');
Route::post('role/permission/{role}','RoleController@addPermission');

Route::get('transcricao','ProcessamentoController@radios');
Route::get('transcricao/baixar/{pasta}','ProcessamentoController@baixar');
Route::get('transcricao/processar/{pasta}','ProcessamentoController@processar');
Route::get('transcricao/audios/{emissora}','ProcessamentoController@audios');
Route::get('processamento','ProcessamentoController@index');

Route::match(array('GET', 'POST'),'relatorios','RelatorioController@index');

Route::get('leitura','RelatorioController@word');

Route::get('files/{file_name}', function($file_name = null)
{
    $path = storage_path().'/'.'app'.'/public/'.$file_name;
    if (file_exists($path)) {
        return Response::download($path);
    }
});
