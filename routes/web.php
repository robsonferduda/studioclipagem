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

Route::post('areas/inserir','AreaController@inserir');
Route::post('areas/{id}/atualizar','AreaController@atualizar');

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
Route::get('emissora/{id}/transcricao/atualiza','EmissoraController@atualizaTranscricao');
Route::post('emissora/{tipo}/adicionar','EmissoraController@store');
Route::post('emissora/{tipo}/atualizar','EmissoraController@update');
Route::post('emissoras/horario/adicionar','EmissoraController@adicionarHorarios');

Route::match(array('GET', 'POST'),'programas/{tipo}','ProgramaController@index');
Route::get('programa/{tipo}/novo','ProgramaController@novo');

Route::get('estado/{id}/cidades','EstadoController@getCidades');

Route::get('fonte-impresso/listar','JornalImpressoController@listar');
Route::get('fonte-impresso/cadastrar','JornalImpressoController@cadastrar');
Route::get('fonte-impresso/{id}/editar','JornalImpressoController@editar');

Route::get('fontes','FonteController@index');
Route::get('fonte-web/coletas/{origem}/listar/{id_fonte}','FonteWebController@listarColetas');
Route::get('fonte-web/coletas/{id}','FonteWebController@coletas');
Route::get('fonte-web/relatorios','FonteWebController@relatorios');
Route::get('fonte-web/importar','FonteWebController@importar');
Route::get('fonte-web/estatisticas/{id}','FonteWebController@estatisticas');
Route::get('fonte-web/totais/semana/{id}','FonteWebController@getSemanaColetas');
Route::match(array('GET', 'POST'),'fonte-web/listar','FonteWebController@listar');
Route::resource('fonte-web','FonteWebController');

Route::get('impresso','JornalImpressoController@index');
Route::match(array('GET', 'POST'),'jornal-impresso/processamento','JornalImpressoController@processamento');
Route::match(array('GET', 'POST'),'jornal-impresso/monitoramento','JornalImpressoController@monitoramento');
Route::get('jornal-impresso/processar','JornalImpressoController@processar');
Route::get('jornal-impresso/pendentes/listar','JornalImpressoController@listarPendentes');
Route::get('jornal-impresso/upload','JornalImpressoController@upload');
Route::get('jornal-impresso/noticia/{id}','JornalImpressoController@detalhes');
Route::post('jornal-impresso/upload','JornalImpressoController@uploadFiles');

Route::get('noticia-impressa/cadastrar','NoticiaImpressaController@cadastrar');
Route::get('noticia-impressa/cliente/{cliente}/copiar/{id}','NoticiaImpressaController@copiar');
Route::get('noticia-impressa/cliente/{cliente}/editar/{id}','NoticiaImpressaController@editar');
Route::resource('noticia-impressa','NoticiaImpressaController');
Route::post('noticia-impressa/upload','NoticiaImpressaController@upload');

Route::post('jornal-impresso/inserir','JornalImpressoController@inserir');
Route::post('jornal-impresso/{id}/atualizar','JornalImpressoController@atualizar');
Route::get('jornal-impresso/{id}/remover','JornalImpressoController@remover');

Route::match(array('GET', 'POST'),'buscar-impresso','JornalImpressoController@index');
Route::match(array('GET', 'POST'),'buscar-web','JornalWebController@index');

Route::get('jornal-web','JornalWebController@index');
Route::get('jornal-web/cadastrar','JornalWebController@cadastrar');
Route::get('jornal-web/fontes','JornalWebController@fontes');
Route::get('jornal-web/listar','JornalWebController@listar');
Route::get('jornal-web/noticia/{id}','JornalWebController@detalhes');
Route::get('jornal-web/noticia/estatisticas/{id}','JornalWebController@estatisticas');

Route::match(array('GET', 'POST'),'monitoramento','MonitoramentoController@index');
Route::match(array('GET', 'POST'),'monitoramento/listar','MonitoramentoController@listar');
Route::get('monitoramento/adicionar','MonitoramentoController@adicionar');
Route::get('monitoramento/executar','MonitoramentoController@executar');
Route::get('monitoramento/{id}/atualizar-status','MonitoramentoController@atualizarStatus');
Route::get('monitoramento/{id}/noticias','MonitoramentoController@noticias');
Route::get('monitoramento/cliente/{id}','MonitoramentoController@getMonitoramentoCliente');

Route::match(array('GET', 'POST'),'emissoras','EmissoraController@index');
Route::get('emissoras/{tipo}/novo','EmissoraController@novo');
Route::get('radio/emissora/{id}/horarios','EmissoraController@horarios');

Route::post('pauta','PautaController@store');
Route::match(array('GET', 'POST'),'pautas','PautaController@index');
Route::get('pauta/cadastrar','PautaController@cadastrar');
Route::get('pauta/{id}/vincular','PautaController@vincular');
Route::get('pauta/{id}/remover','PautaController@remover');
Route::post('pauta/vincular','PautaController@vincularNoticia');
Route::post('pauta/desvincular','PautaController@desvincularNoticia');

Route::get('radios','NoticiaRadioController@dashboard');
Route::match(array('GET', 'POST'),'radio/noticias','NoticiaRadioController@index');
Route::get('radio/noticias/cadastrar','NoticiaRadioController@cadastrar');
Route::get('radio/noticias/{id}/editar','NoticiaRadioController@editar');
Route::get('radio/noticias/{id}/remover','NoticiaRadioController@remover');
Route::get('radio/noticias/{id}/download','NoticiaRadioController@download');
Route::get('radio/noticias/estatisticas','NoticiaRadioController@estatisticas');
Route::post('radio/noticias/inserir','NoticiaRadioController@inserir');
Route::post('radio/noticias/{id}/atualizar','NoticiaRadioController@atualizar');
Route::post('radio/noticias/upload','NoticiaRadioController@upload');

Route::get('tags','TagController@index');
Route::get('tags/cadastrar','TagController@cadastrar');
Route::get('tags/{id}/remover','TagController@destroy');

Route::get('tv','NoticiaTvController@dashboard');
Route::get('tv/decupagem','NoticiaTvController@decupagem');
Route::get('tv/noticias/estatisticas','NoticiaTvController@estatisticas');
Route::get('tv/noticias/cadastrar','NoticiaTvController@cadastrar');
Route::get('noticia-tv/decupagem/listar','NoticiaTvController@listarArquivos');
Route::match(array('GET', 'POST'),'tv/noticias','NoticiaTvController@index');
Route::post('noticia_tv/decupagem/salvar','NoticiaTvController@salvarDecugem');
Route::post('noticia_tv/decupagem/processar','NoticiaTvController@processar');
Route::post('noticia_tv/upload','NoticiaTvController@upload');

Route::get('cliente/get/json','ClientController@json');
Route::get('client/accounts/facebook/{cliente}','ClientController@getFacebookAccounts');
Route::get('client/hashtags/{cliente}','ClientController@getHashtags');
Route::get('client/emails/{cliente}','ClientController@emails');
Route::post('cliente/selecionar','ClientController@selecionar');
Route::get('cliente/paginas-associadas/{client}','ClientController@connectedtPages');

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
