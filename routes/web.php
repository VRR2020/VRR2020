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

// Rotas de autenticação
Route::get('/', 'AuthController@showLoginForm')->name('login');
Route::post('/login', 'AuthController@login')->name('login.post');
Route::post('/logout', 'AuthController@logout')->name('logout');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
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

    // Sistema de Follow-up e Agendamentos
    Route::resource('agendamentos', 'AgendamentosController');
    Route::get('agendamentos-dashboard', 'AgendamentosController@dashboard')->name('agendamentos.dashboard');
    Route::post('agendamentos/{agendamento}/reagendar', 'AgendamentosController@reagendar')->name('agendamentos.reagendar');
    Route::post('agendamentos/{agendamento}/enviar', 'AgendamentosController@enviarManual')->name('agendamentos.enviar');
    Route::post('agendamentos/lote', 'AgendamentosController@agendamentoLote')->name('agendamentos.lote');

    // API routes para agendamentos
    Route::get('api/templates/canal', 'AgendamentosController@templatesPorCanal')->name('api.templates.canal');
    Route::post('api/templates/preview', 'AgendamentosController@previewTemplate')->name('api.templates.preview');

    // Templates de Mensagem
    Route::resource('templates', 'TemplatesController');
    Route::get('templates-dashboard', 'TemplatesController@dashboard')->name('templates.dashboard');
    Route::post('templates/{template}/toggle', 'TemplatesController@toggleStatus')->name('templates.toggle');
    Route::post('templates/{template}/clonar', 'TemplatesController@clonar')->name('templates.clonar');
    Route::get('templates-biblioteca', 'TemplatesController@biblioteca')->name('templates.biblioteca');
    Route::post('templates/instalar', 'TemplatesController@instalarTemplate')->name('templates.instalar');
    Route::get('templates/{template}/exportar', 'TemplatesController@exportar')->name('templates.exportar');
    Route::post('templates/importar', 'TemplatesController@importar')->name('templates.importar');
    Route::post('templates/{template}/preview', 'TemplatesController@preview')->name('templates.preview');

    // Segmentação Inteligente
    Route::get('segmentacao', 'SegmentacaoController@index')->name('segmentacao.index');
    Route::get('segmentacao/construtor', 'SegmentacaoController@construtor')->name('segmentacao.construtor');
    Route::post('segmentacao/processar', 'SegmentacaoController@processar')->name('segmentacao.processar');
    Route::post('segmentacao/salvar', 'SegmentacaoController@salvarSegmento')->name('segmentacao.salvar');
    Route::get('segmentacao/sugestoes', 'SegmentacaoController@sugestoes')->name('segmentacao.sugestoes');
    Route::get('segmentacao/analise-comportamento', 'SegmentacaoController@analiseComportamento')->name('segmentacao.analise-comportamento');
    Route::post('segmentacao/exportar', 'SegmentacaoController@exportar')->name('segmentacao.exportar');

    // API routes para segmentação
    Route::get('api/segmentacao/cidadaos', 'SegmentacaoController@buscarCidadaos')->name('api.segmentacao.cidadaos');
    Route::get('api/segmentacao/opcoes-filtro', 'SegmentacaoController@opcoesFiltro')->name('api.segmentacao.opcoes');

    // Relatórios
    Route::get('relatorios', 'RelatoriosController@index')->name('relatorios.index');
    Route::get('relatorios/export/{tipo}', 'RelatoriosController@export')->name('relatorios.export');

    // Manter rota antiga para compatibilidade
    Route::resource('customers', 'CustomersController');
});
