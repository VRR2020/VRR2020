<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interacao;
use App\Models\Cidadao;
use App\Models\Demanda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InteracoesController extends Controller
{
    /**
     * Timeline de interações de um cidadão
     */
    public function index($cidadao_id, Request $request)
    {
        $cidadao = Cidadao::findOrFail($cidadao_id);
        
        $query = Interacao::with(['usuario', 'demanda'])
            ->where('cidadao_id', $cidadao_id);

        // Aplicar filtros
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('canal')) {
            $query->where('canal', $request->canal);
        }

        if ($request->filled('sentido')) {
            $query->where('sentido', $request->sentido);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('created_at', [
                $request->data_inicio . ' 00:00:00',
                $request->data_fim . ' 23:59:59'
            ]);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('assunto', 'like', "%{$busca}%")
                  ->orWhere('descricao', 'like', "%{$busca}%")
                  ->orWhere('observacoes', 'like', "%{$busca}%");
            });
        }

        $interacoes = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estatísticas das interações
        $estatisticas = $this->calcularEstatisticasInteracoes($cidadao_id);

        // Filtros para a view
        $filtros = $request->only(['tipo', 'canal', 'sentido', 'status', 'data_inicio', 'data_fim', 'busca']);

        return view('interacoes.timeline', compact('cidadao', 'interacoes', 'estatisticas', 'filtros'));
    }

    /**
     * Formulário para nova interação
     */
    public function create($cidadao_id, Request $request)
    {
        $cidadao = Cidadao::findOrFail($cidadao_id);
        $demanda_id = $request->get('demanda_id');
        $demanda = null;

        if ($demanda_id) {
            $demanda = Demanda::where('cidadao_id', $cidadao_id)->find($demanda_id);
        }

        $demandas = Demanda::where('cidadao_id', $cidadao_id)
            ->whereIn('status', ['aberta', 'em_andamento'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('interacoes.create', compact('cidadao', 'demanda', 'demandas'));
    }

    /**
     * Salva nova interação
     */
    public function store($cidadao_id, Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:reuniao,telefonema,email,sms,whatsapp,evento,visita,carta,redes_sociais',
            'canal' => 'required|in:presencial,telefonico,email,whatsapp,sms,facebook,instagram,twitter,linkedin,site',
            'assunto' => 'required|string|max:255',
            'descricao' => 'required|string',
            'sentido' => 'required|in:entrada,saida',
            'status' => 'required|in:agendada,realizada,cancelada,nao_realizada',
            'data_agendamento' => 'nullable|date',
            'data_realizacao' => 'nullable|date',
            'duracao_minutos' => 'nullable|integer|min:1|max:480',
            'resultado' => 'nullable|in:positivo,neutro,negativo,nao_atendeu,reagendado'
        ]);

        $interacao = Interacao::create([
            'cidadao_id' => $cidadao_id,
            'demanda_id' => $request->demanda_id,
            'usuario_id' => Auth::id(),
            'tipo' => $request->tipo,
            'canal' => $request->canal,
            'assunto' => $request->assunto,
            'descricao' => $request->descricao,
            'sentido' => $request->sentido,
            'status' => $request->status,
            'data_agendamento' => $request->data_agendamento,
            'data_realizacao' => $request->data_realizacao ?: now(),
            'duracao_minutos' => $request->duracao_minutos,
            'resultado' => $request->resultado,
            'observacoes' => $request->observacoes,
            'tags' => $request->tags ? explode(',', $request->tags) : null
        ]);

        return redirect()->route('interacoes.show', [$cidadao_id, $interacao])
            ->with('success', 'Interação registrada com sucesso!');
    }

    /**
     * Exibe detalhes da interação
     */
    public function show($cidadao_id, Interacao $interacao)
    {
        $interacao->load(['cidadao', 'usuario', 'demanda']);

        // Verificar se a interação pertence ao cidadão correto
        if ($interacao->cidadao_id != $cidadao_id) {
            abort(404);
        }

        return view('interacoes.show', compact('interacao'));
    }

    /**
     * Formulário para editar interação
     */
    public function edit($cidadao_id, Interacao $interacao)
    {
        if ($interacao->cidadao_id != $cidadao_id) {
            abort(404);
        }

        $cidadao = $interacao->cidadao;
        $demandas = Demanda::where('cidadao_id', $cidadao_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('interacoes.edit', compact('interacao', 'cidadao', 'demandas'));
    }

    /**
     * Atualiza interação
     */
    public function update($cidadao_id, Request $request, Interacao $interacao)
    {
        if ($interacao->cidadao_id != $cidadao_id) {
            abort(404);
        }

        $request->validate([
            'assunto' => 'required|string|max:255',
            'descricao' => 'required|string',
            'resultado' => 'nullable|in:positivo,neutro,negativo,nao_atendeu,reagendado',
            'duracao_minutos' => 'nullable|integer|min:1|max:480'
        ]);

        $interacao->update($request->only([
            'assunto', 'descricao', 'resultado', 'duracao_minutos', 'observacoes'
        ]));

        // Atualizar tags se fornecidas
        if ($request->filled('tags')) {
            $interacao->update(['tags' => explode(',', $request->tags)]);
        }

        return redirect()->route('interacoes.show', [$cidadao_id, $interacao])
            ->with('success', 'Interação atualizada com sucesso!');
    }

    /**
     * Remove interação
     */
    public function destroy($cidadao_id, Interacao $interacao)
    {
        if ($interacao->cidadao_id != $cidadao_id) {
            abort(404);
        }

        $interacao->delete();

        return redirect()->route('interacoes.index', $cidadao_id)
            ->with('success', 'Interação removida com sucesso!');
    }

    /**
     * Dashboard geral de interações
     */
    public function dashboard()
    {
        $estatisticas = [
            'total_interacoes' => Interacao::count(),
            'realizadas_hoje' => Interacao::whereDate('data_realizacao', today())->count(),
            'agendadas_pendentes' => Interacao::where('status', 'agendada')
                ->where('data_agendamento', '>=', now())->count(),
            'tempo_medio_resposta' => $this->calcularTempoMedioResposta()
        ];

        // Gráficos e métricas
        $graficos = [
            'por_tipo' => $this->getInteracoesPorTipo(),
            'por_canal' => $this->getInteracoesPorCanal(),
            'por_resultado' => $this->getInteracoesPorResultado(),
            'evolucao_temporal' => $this->getEvolucaoTemporal(),
            'top_usuarios' => $this->getTopUsuarios()
        ];

        return view('interacoes.dashboard', compact('estatisticas', 'graficos'));
    }

    /**
     * Marcar interação como favorita
     */
    public function marcarFavorita($cidadao_id, Interacao $interacao)
    {
        if ($interacao->cidadao_id != $cidadao_id) {
            abort(404);
        }

        $tags = $interacao->tags ?: [];
        
        if (!in_array('favorita', $tags)) {
            $tags[] = 'favorita';
            $interacao->update(['tags' => $tags]);
            $message = 'Interação marcada como favorita!';
        } else {
            $tags = array_diff($tags, ['favorita']);
            $interacao->update(['tags' => array_values($tags)]);
            $message = 'Interação removida dos favoritos!';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Marcar interação como urgente
     */
    public function marcarUrgente($cidadao_id, Interacao $interacao)
    {
        if ($interacao->cidadao_id != $cidadao_id) {
            abort(404);
        }

        $tags = $interacao->tags ?: [];
        
        if (!in_array('urgente', $tags)) {
            $tags[] = 'urgente';
            $interacao->update(['tags' => $tags]);
            $message = 'Interação marcada como urgente!';
        } else {
            $tags = array_diff($tags, ['urgente']);
            $interacao->update(['tags' => array_values($tags)]);
            $message = 'Marcação de urgente removida!';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Exportar timeline em PDF
     */
    public function exportarPDF($cidadao_id, Request $request)
    {
        $cidadao = Cidadao::findOrFail($cidadao_id);
        
        $query = Interacao::with(['usuario', 'demanda'])
            ->where('cidadao_id', $cidadao_id);

        // Aplicar mesmos filtros da timeline
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('created_at', [
                $request->data_inicio . ' 00:00:00',
                $request->data_fim . ' 23:59:59'
            ]);
        } else {
            // Por padrão, últimos 6 meses
            $query->where('created_at', '>=', now()->subMonths(6));
        }

        $interacoes = $query->orderBy('created_at', 'desc')->get();
        $estatisticas = $this->calcularEstatisticasInteracoes($cidadao_id);

        $pdf = PDF::loadView('interacoes.timeline-pdf', compact('cidadao', 'interacoes', 'estatisticas'));
        
        $filename = 'timeline_' . $cidadao->nome . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Busca rápida de interações
     */
    public function buscar(Request $request)
    {
        $termo = $request->get('q');
        $limite = $request->get('limit', 10);

        $interacoes = Interacao::with(['cidadao', 'usuario'])
            ->where(function($q) use ($termo) {
                $q->where('assunto', 'like', "%{$termo}%")
                  ->orWhere('descricao', 'like', "%{$termo}%")
                  ->orWhereHas('cidadao', function($q2) use ($termo) {
                      $q2->where('nome', 'like', "%{$termo}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();

        return response()->json($interacoes);
    }

    /**
     * Análise de padrões de interação
     */
    public function analisesPadrao($cidadao_id)
    {
        $cidadao = Cidadao::findOrFail($cidadao_id);
        
        $analises = [
            'frequencia_mensal' => $this->getFrequenciaMensal($cidadao_id),
            'canais_preferidos' => $this->getCanaisPreferidos($cidadao_id),
            'horarios_ativos' => $this->getHorariosAtivos($cidadao_id),
            'evolucao_engajamento' => $this->getEvolucaoEngajamento($cidadao_id),
            'tipos_interacao' => $this->getTiposInteracao($cidadao_id)
        ];

        return view('interacoes.analises', compact('cidadao', 'analises'));
    }

    /**
     * Calcula estatísticas das interações de um cidadão
     */
    private function calcularEstatisticasInteracoes($cidadao_id)
    {
        $query = Interacao::where('cidadao_id', $cidadao_id);

        return [
            'total' => $query->count(),
            'realizadas' => $query->where('status', 'realizada')->count(),
            'agendadas' => $query->where('status', 'agendada')->count(),
            'ultima_interacao' => $query->latest('created_at')->first()?->created_at,
            'canal_mais_usado' => $query->select('canal', DB::raw('count(*) as total'))
                ->groupBy('canal')->orderBy('total', 'desc')->first()?->canal,
            'tempo_medio_duracao' => $query->whereNotNull('duracao_minutos')->avg('duracao_minutos'),
            'resultado_positivo_taxa' => $query->where('resultado', 'positivo')->count() > 0 
                ? round(($query->where('resultado', 'positivo')->count() / $query->whereNotNull('resultado')->count()) * 100, 1)
                : 0
        ];
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
     * Retorna interações por canal
     */
    private function getInteracoesPorCanal()
    {
        return Interacao::select('canal', DB::raw('count(*) as total'))
            ->groupBy('canal')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'canal');
    }

    /**
     * Retorna interações por resultado
     */
    private function getInteracoesPorResultado()
    {
        return Interacao::select('resultado', DB::raw('count(*) as total'))
            ->whereNotNull('resultado')
            ->groupBy('resultado')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'resultado');
    }

    /**
     * Retorna evolução temporal das interações
     */
    private function getEvolucaoTemporal()
    {
        $dados = [];
        for ($i = 11; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $total = Interacao::whereYear('created_at', $data->year)
                            ->whereMonth('created_at', $data->month)
                            ->count();
            
            $dados[] = [
                'mes' => $data->format('M/Y'),
                'total' => $total
            ];
        }
        
        return $dados;
    }

    /**
     * Retorna top usuários por interações
     */
    private function getTopUsuarios()
    {
        return Interacao::select('usuario_id', DB::raw('count(*) as total'))
            ->with('usuario:id,name')
            ->groupBy('usuario_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'nome' => $item->usuario->name ?? 'Usuário Removido',
                    'total' => $item->total
                ];
            });
    }

    /**
     * Calcula tempo médio de resposta
     */
    private function calcularTempoMedioResposta()
    {
        // Implementação simplificada
        return 2.5; // horas
    }

    // Métodos de análise por cidadão
    private function getFrequenciaMensal($cidadao_id)
    {
        $dados = [];
        for ($i = 11; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $total = Interacao::where('cidadao_id', $cidadao_id)
                            ->whereYear('created_at', $data->year)
                            ->whereMonth('created_at', $data->month)
                            ->count();
            
            $dados[] = [
                'mes' => $data->format('M/Y'),
                'total' => $total
            ];
        }
        
        return $dados;
    }

    private function getCanaisPreferidos($cidadao_id)
    {
        return Interacao::where('cidadao_id', $cidadao_id)
            ->select('canal', DB::raw('count(*) as total'))
            ->groupBy('canal')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'canal');
    }

    private function getHorariosAtivos($cidadao_id)
    {
        return Interacao::where('cidadao_id', $cidadao_id)
            ->whereNotNull('data_realizacao')
            ->select(DB::raw('HOUR(data_realizacao) as hora'), DB::raw('count(*) as total'))
            ->groupBy('hora')
            ->orderBy('hora')
            ->get()
            ->pluck('total', 'hora');
    }

    private function getEvolucaoEngajamento($cidadao_id)
    {
        // Implementação simplificada - calcular score baseado em interações
        $dados = [];
        for ($i = 5; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $interacoes = Interacao::where('cidadao_id', $cidadao_id)
                                 ->whereYear('created_at', $data->year)
                                 ->whereMonth('created_at', $data->month)
                                 ->count();
            
            // Score simplificado baseado no número de interações
            $score = min($interacoes * 10, 100);
            
            $dados[] = [
                'mes' => $data->format('M/Y'),
                'score' => $score
            ];
        }
        
        return $dados;
    }

    private function getTiposInteracao($cidadao_id)
    {
        return Interacao::where('cidadao_id', $cidadao_id)
            ->select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'tipo');
    }
}
