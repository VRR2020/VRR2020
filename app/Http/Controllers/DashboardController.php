<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cidadao;
use App\Models\Demanda;
use App\Models\Interacao;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard principal do CRM Legislativo
     */
    public function index()
    {
        // Métricas básicas
        $metrics = [
            'total_cidadaos' => Cidadao::count(),
            'demandas_abertas' => Demanda::whereIn('status', ['aberta', 'em_andamento'])->count(),
            'demandas_resolvidas' => Demanda::where('status', 'resolvida')->count(),
            'interacoes_mes' => Interacao::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->count(),
        ];

        // Calcular crescimento percentual
        $metrics['crescimento_cidadaos'] = $this->calcularCrescimento('cidadaos');
        $metrics['crescimento_demandas'] = $this->calcularCrescimento('demandas');
        $metrics['crescimento_interacoes'] = $this->calcularCrescimento('interacoes');

        // Dados para gráficos
        $graficos = [
            'demandas_categoria' => $this->getDemandaPorCategoria(),
            'cidadaos_bairro' => $this->getCidadaosPorBairro(),
            'pipeline' => $this->getPipelineData(),
            'atividade_recente' => $this->getAtividadeRecente()
        ];

        return view('dashboard.index', compact('metrics', 'graficos'));
    }

    /**
     * Retorna dados para gráficos e métricas em AJAX
     */
    public function metrics()
    {
        $data = [
            'demandas_mes' => $this->getDemandasPorMes(),
            'interacoes_tipo' => $this->getInteracoesPorTipo(),
            'engajamento_evolucao' => $this->getEvolucaoEngajamento(),
            'urgencia_demandas' => $this->getDemandasPorUrgencia()
        ];

        return response()->json($data);
    }

    /**
     * Calcula crescimento percentual em relação ao mês anterior
     */
    private function calcularCrescimento($tabela)
    {
        $mesAtual = DB::table($tabela)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $mesAnterior = DB::table($tabela)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($mesAnterior == 0) {
            return $mesAtual > 0 ? 100 : 0;
        }

        return round((($mesAtual - $mesAnterior) / $mesAnterior) * 100, 1);
    }

    /**
     * Retorna demandas agrupadas por categoria
     */
    private function getDemandaPorCategoria()
    {
        return Demanda::select('categoria', DB::raw('count(*) as total'))
            ->groupBy('categoria')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->pluck('total', 'categoria');
    }

    /**
     * Retorna cidadãos agrupados por bairro
     */
    private function getCidadaosPorBairro()
    {
        return Cidadao::select('bairro', DB::raw('count(*) as total'))
            ->groupBy('bairro')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->pluck('total', 'bairro');
    }

    /**
     * Retorna dados do pipeline de relacionamento
     */
    private function getPipelineData()
    {
        return [
            'leads' => Cidadao::where('status', 'lead')->count(),
            'engajados' => Cidadao::where('status', 'engajado')->count(),
            'ativos' => Cidadao::where('status', 'ativo')->count(),
            'apoiadores' => Cidadao::where('status', 'apoiador')->count()
        ];
    }

    /**
     * Retorna atividade recente do sistema
     */
    private function getAtividadeRecente()
    {
        $atividades = collect();

        // Cidadãos recentes
        $cidadaosRecentes = Cidadao::latest()
            ->limit(3)
            ->get()
            ->map(function($cidadao) {
                return [
                    'tipo' => 'cidadao_cadastrado',
                    'descricao' => $cidadao->nome . ' foi cadastrado(a)',
                    'detalhes' => 'Novo cidadão no bairro ' . $cidadao->bairro,
                    'created_at' => $cidadao->created_at
                ];
            });

        // Demandas recentes
        $demandasRecentes = Demanda::with('cidadao')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function($demanda) {
                return [
                    'tipo' => 'demanda_criada',
                    'descricao' => $demanda->cidadao->nome . ' cadastrou nova demanda',
                    'detalhes' => $demanda->titulo,
                    'created_at' => $demanda->created_at
                ];
            });

        // Interações recentes
        $interacoesRecentes = Interacao::with('cidadao')
            ->where('status', 'realizada')
            ->latest()
            ->limit(2)
            ->get()
            ->map(function($interacao) {
                return [
                    'tipo' => 'interacao_realizada',
                    'descricao' => 'Interação com ' . $interacao->cidadao->nome,
                    'detalhes' => $interacao->assunto,
                    'created_at' => $interacao->created_at
                ];
            });

        return $atividades
            ->merge($cidadaosRecentes)
            ->merge($demandasRecentes)
            ->merge($interacoesRecentes)
            ->sortByDesc('created_at')
            ->take(6)
            ->values();
    }

    /**
     * Retorna demandas por mês (últimos 6 meses)
     */
    private function getDemandasPorMes()
    {
        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $count = Demanda::whereYear('created_at', $data->year)
                           ->whereMonth('created_at', $data->month)
                           ->count();

            $meses[] = [
                'mes' => $data->format('M'),
                'total' => $count
            ];
        }

        return $meses;
    }

    /**
     * Retorna interações por tipo
     */
    private function getInteracoesPorTipo()
    {
        return Interacao::select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'tipo');
    }

    /**
     * Retorna evolução do engajamento
     */
    private function getEvolucaoEngajamento()
    {
        return Cidadao::select('nivel_engajamento', DB::raw('count(*) as total'))
            ->groupBy('nivel_engajamento')
            ->get()
            ->pluck('total', 'nivel_engajamento');
    }

    /**
     * Retorna demandas por urgência
     */
    private function getDemandasPorUrgencia()
    {
        return Demanda::select('urgencia', DB::raw('count(*) as total'))
            ->groupBy('urgencia')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'urgencia');
    }
}
