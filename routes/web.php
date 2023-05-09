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
Route::resource('hashtag', 'HashtagController');
Route::resource('notification', 'NotificacaoController');
Route::resource('usuario', 'UserController');
Route::resource('email', 'EmailController');
Route::resource('emissora', 'EmissoraController');

Route::get('areas','AreaController@index');
Route::get('areas/cadastrar','AreaController@cadastrar');
Route::get('areas/{id}/editar','AreaController@editar');
Route::get('areas/{id}/remover','AreaController@remover');

Route::post('alterar-data','HomeController@atualizarData');

Route::post('areas/inserir','AreaController@inserir');
Route::post('areas/{id}/atualizar','AreaController@atualizar');

Route::resource('cliente','ClienteController');

Route::post('emissoras/horario/adicionar','EmissoraController@adicionarHorarios');

Route::get('emissoras/programas','ProgramaController@index');

Route::get('estado/{id}/cidades','EstadoController@getCidades');

Route::get('fontes','FonteController@index');
Route::get('fonte-web/listar','FonteWebController@listar');
Route::get('fonte-web/estatisticas/{id}','FonteWebController@estatisticas');
Route::resource('fonte-web','FonteWebController');

Route::get('impresso','JornalImpressoController@index');
Route::match(array('GET', 'POST'),'jornal-impresso/processamento','JornalImpressoController@processamento');
Route::get('jornal-impresso/monitoramento','JornalImpressoController@monitoramento');
Route::get('jornal-impresso/processar','JornalImpressoController@processar');
Route::get('jornal-impresso/pendentes/listar','JornalImpressoController@listarPendentes');
Route::get('jornal-impresso/upload','JornalImpressoController@upload');
Route::get('jornal-impresso/noticia/{id}','JornalImpressoController@detalhes');
Route::post('jornal-impresso/upload','JornalImpressoController@uploadFiles');

Route::get('jornal-impresso/listar','JornalImpressoController@listar');
Route::get('jornal-impresso/cadastrar','JornalImpressoController@cadastrar');
Route::get('jornal-impresso/{id}/editar','JornalImpressoController@editar');

Route::get('noticia-impressa/cadastrar','NoticiaImpressaController@cadastrar');
Route::get('noticia-impressa/cliente/{cliente}/editar/{id}','NoticiaImpressaController@editar');

Route::post('jornal-impresso/inserir','JornalImpressoController@inserir');
Route::post('jornal-impresso/{id}/atualizar','JornalImpressoController@atualizar');
Route::get('jornal-impresso/{id}/remover','JornalImpressoController@remover');

Route::match(array('GET', 'POST'),'buscar-impresso','JornalImpressoController@index');
Route::match(array('GET', 'POST'),'buscar-web','JornalWebController@index');

Route::get('jornal-web','JornalWebController@index');
Route::get('jornal-web/listar','JornalWebController@listar');
Route::get('jornal-web/noticia/{id}','JornalWebController@detalhes');
Route::get('jornal-web/noticia/estatisticas/{id}','JornalWebController@estatisticas');

Route::get('monitoramento','MonitoramentoController@index');
Route::get('monitoramento/adicionar','MonitoramentoController@adicionar');
Route::get('monitoramento/executar','MonitoramentoController@executar');
Route::get('monitoramento/{id}/desabilitar','MonitoramentoController@desabilitar');
Route::get('monitoramento/{id}/noticias','MonitoramentoController@noticias');

Route::get('radios','RadioController@index');
Route::get('radio/emissora/{id}/transcricao/atualiza','RadioController@atualizaTranscricao');

Route::match(array('GET', 'POST'),'emissoras','RadioController@emissoras');
Route::get('radio/emissoras/novo','EmissoraController@novo');
Route::get('radio/emissora/{id}/horarios','EmissoraController@horarios');

Route::get('radio/noticias','NoticiaRadioController@index');
Route::get('radio/noticias/cadastrar','NoticiaRadioController@cadastrar');
Route::get('radio/noticias/{id}/editar','NoticiaRadioController@editar');
Route::post('radio/noticias/inserir','NoticiaRadioController@inserir');
Route::post('radio/noticias/{id}/atualizar','NoticiaRadioController@atualizar');
Route::get('radio/noticias/{id}/remover','NoticiaRadioController@remover');
Route::get('radio/noticias/{id}/download','NoticiaRadioController@download');

Route::get('tv','TvController@index');

Route::get('boletins','BoletimController@index');
Route::get('boletim/{id}','BoletimController@detalhes');
Route::get('boletim/{id}/enviar','BoletimController@enviar');
Route::get('boletim/{id}/outlook','BoletimController@outlook');
Route::get('boletim/{id}/visualizar','BoletimController@visualizar');
Route::post('boletim/enviar/lista','BoletimController@enviarLista');


Route::get('cliente/get/json','ClientController@json');
Route::get('clientes','ClientController@index')->name('clientes.index');
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
ROute::get('transcricao/audios/{emissora}','ProcessamentoController@audios');
Route::get('processamento','ProcessamentoController@index');

Route::get('files/{file_name}', function($file_name = null)
{
    $path = storage_path().'/'.'app'.'/public/'.$file_name;
    if (file_exists($path)) {
        return Response::download($path);
    }
});
