<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('cliente/validaCpf', 'ClienteController@validaCpf');
Route::get('cliente/buscarClientes', 'ClienteController@buscarClientes');
Route::get('cliente/getAreasCliente', 'ClienteController@getAreasCliente');

Route::get('estado/{id_estado}/cidades', 'EstadoController@getCidades');

Route::get('emissora/buscarEmissoras', 'EmissoraController@buscarEmissoras');

Route::get('programa/buscarProgramas', 'ProgramaController@buscarProgramas');
