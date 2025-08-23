<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard principal do CRM Legislativo
     */
    public function index()
    {
        // Métricas básicas
        $metrics = [
            'total_cidadaos' => 0,
            'demandas_abertas' => 0,
            'demandas_resolvidas' => 0,
            'interacoes_mes' => 0,
        ];

        return view('dashboard.index', compact('metrics'));
    }

    /**
     * Retorna dados para gráficos e métricas em AJAX
     */
    public function metrics()
    {
        $data = [
            'cidadaos_mes' => [],
            'demandas_tipo' => [],
            'engajamento_bairros' => [],
            'pipeline_dados' => []
        ];

        return response()->json($data);
    }
}
