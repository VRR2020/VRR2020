<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Página inicial redireciona para o dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard principal do CRM Legislativo
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/dashboard/metrics', 'DashboardController@metrics')->name('dashboard.metrics');

// Gestão de Cidadãos (Leads Políticos)
Route::resource('cidadaos', 'CidadaosController');
Route::post('cidadaos/importar', 'CidadaosController@importar')->name('cidadaos.importar');

// Gestão de Demandas
Route::resource('demandas', 'DemandasController');
Route::get('demandas-dashboard', 'DemandasController@dashboard')->name('demandas.dashboard');
Route::patch('demandas/{id}/status', 'DemandasController@updateStatus')->name('demandas.status');

// Histórico de Interações
Route::get('cidadaos/{id}/interacoes', 'InteracoesController@index')->name('interacoes.index');
Route::post('cidadaos/{id}/interacoes', 'InteracoesController@store')->name('interacoes.store');

// Relatórios
Route::get('relatorios', 'RelatoriosController@index')->name('relatorios.index');
Route::get('relatorios/export/{tipo}', 'RelatoriosController@export')->name('relatorios.export');

// Manter rota antiga para compatibilidade
Route::resource('customers', 'CustomersController');
