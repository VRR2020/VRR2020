<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agendamento;
use App\Models\TemplateMessage;
use App\Models\Cidadao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AgendamentosController extends Controller
{
    /**
     * Lista todos os agendamentos
     */
    public function index(Request $request)
    {
        $query = Agendamento::with(['cidadao', 'usuario', 'template']);

        // Aplicar filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('canal')) {
            $query->where('canal', $request->canal);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('prioridade')) {
            $query->where('prioridade', $request->prioridade);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_agendamento', [
                $request->data_inicio . ' 00:00:00',
                $request->data_fim . ' 23:59:59'
            ]);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('titulo', 'like', "%{$busca}%")
                  ->orWhere('conteudo', 'like', "%{$busca}%")
                  ->orWhereHas('cidadao', function($q2) use ($busca) {
                      $q2->where('nome', 'like', "%{$busca}%")
                         ->orWhere('email', 'like', "%{$busca}%");
                  });
            });
        }

        $agendamentos = $query->orderBy('data_agendamento', 'desc')->paginate(20);

        // Estatísticas rápidas
        $estatisticas = $this->getEstatisticasRapidas();

        $filtros = $request->only(['status', 'canal', 'tipo', 'prioridade', 'data_inicio', 'data_fim', 'busca']);

        return view('agendamentos.index', compact('agendamentos', 'estatisticas', 'filtros'));
    }

    /**
     * Formulário para criar novo agendamento
     */
    public function create(Request $request)
    {
        $cidadao_id = $request->get('cidadao_id');
        $cidadao = null;

        if ($cidadao_id) {
            $cidadao = Cidadao::find($cidadao_id);
        }

        $templates = TemplateMessage::ativo()->orderBy('nome')->get();
        $cidadaos = Cidadao::orderBy('nome')->limit(100)->get();

        return view('agendamentos.create', compact('templates', 'cidadaos', 'cidadao'));
    }

    /**
     * Salva novo agendamento
     */
    public function store(Request $request)
    {
        $request->validate([
            'cidadao_id' => 'required|exists:cidadaos,id',
            'tipo' => 'required|in:lembrete,follow_up,convite,informativo,pesquisa,campanha',
            'canal' => 'required|in:email,sms,whatsapp,push,interno',
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'data_agendamento' => 'required|date|after:now',
            'prioridade' => 'required|in:baixa,normal,alta,urgente',
            'max_tentativas' => 'integer|min:1|max:10',
            'intervalo_tentativas' => 'integer|min:5|max:1440'
        ]);

        $agendamento = Agendamento::create([
            'cidadao_id' => $request->cidadao_id,
            'usuario_id' => Auth::id(),
            'template_id' => $request->template_id,
            'tipo' => $request->tipo,
            'canal' => $request->canal,
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'data_agendamento' => $request->data_agendamento,
            'prioridade' => $request->prioridade,
            'max_tentativas' => $request->max_tentativas ?? 3,
            'intervalo_tentativas' => $request->intervalo_tentativas ?? 60,
            'dados_dinamicos' => $request->dados_dinamicos ? json_decode($request->dados_dinamicos, true) : null,
            'tags' => $request->tags ? explode(',', $request->tags) : null,
            'observacoes' => $request->observacoes
        ]);

        return redirect()->route('agendamentos.show', $agendamento)
            ->with('success', 'Agendamento criado com sucesso!');
    }

    /**
     * Exibe detalhes do agendamento
     */
    public function show(Agendamento $agendamento)
    {
        $agendamento->load(['cidadao', 'usuario', 'template']);
        
        // Processar conteúdo com variáveis
        $conteudoProcessado = $agendamento->processarConteudo();

        return view('agendamentos.show', compact('agendamento', 'conteudoProcessado'));
    }

    /**
     * Formulário para editar agendamento
     */
    public function edit(Agendamento $agendamento)
    {
        $templates = TemplateMessage::ativo()->orderBy('nome')->get();
        $cidadaos = Cidadao::orderBy('nome')->limit(100)->get();

        return view('agendamentos.edit', compact('agendamento', 'templates', 'cidadaos'));
    }

    /**
     * Atualiza agendamento
     */
    public function update(Request $request, Agendamento $agendamento)
    {
        // Só permite editar se ainda não foi enviado
        if ($agendamento->status !== Agendamento::STATUS_AGENDADO) {
            return redirect()->back()->with('error', 'Não é possível editar agendamento já processado.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'data_agendamento' => 'required|date',
            'prioridade' => 'required|in:baixa,normal,alta,urgente'
        ]);

        $agendamento->update($request->only([
            'titulo', 'conteudo', 'data_agendamento', 'prioridade', 'observacoes'
        ]));

        return redirect()->route('agendamentos.show', $agendamento)
            ->with('success', 'Agendamento atualizado com sucesso!');
    }

    /**
     * Cancela agendamento
     */
    public function destroy(Agendamento $agendamento)
    {
        if ($agendamento->status !== Agendamento::STATUS_AGENDADO) {
            return redirect()->back()->with('error', 'Não é possível cancelar agendamento já processado.');
        }

        $agendamento->update([
            'status' => Agendamento::STATUS_CANCELADO,
            'observacoes' => ($agendamento->observacoes ?? '') . "\nCancelado em " . now()->format('d/m/Y H:i')
        ]);

        return redirect()->route('agendamentos.index')
            ->with('success', 'Agendamento cancelado com sucesso!');
    }

    /**
     * Dashboard de agendamentos
     */
    public function dashboard()
    {
        $estatisticas = [
            'total_agendados' => Agendamento::where('status', Agendamento::STATUS_AGENDADO)->count(),
            'total_enviados' => Agendamento::where('status', Agendamento::STATUS_ENVIADO)->count(),
            'total_falhados' => Agendamento::where('status', Agendamento::STATUS_FALHADO)->count(),
            'atrasados' => Agendamento::atrasados()->count(),
            'proximas_24h' => Agendamento::where('status', Agendamento::STATUS_AGENDADO)
                ->whereBetween('data_agendamento', [now(), now()->addDay()])
                ->count()
        ];

        // Gráficos
        $graficos = [
            'por_canal' => $this->getAgendamentosPorCanal(),
            'por_status' => $this->getAgendamentosPorStatus(),
            'timeline' => $this->getTimelineAgendamentos(),
            'performance' => $this->getPerformanceTemplates()
        ];

        return view('agendamentos.dashboard', compact('estatisticas', 'graficos'));
    }

    /**
     * Reagenda um agendamento
     */
    public function reagendar(Request $request, Agendamento $agendamento)
    {
        $request->validate([
            'nova_data' => 'required|date|after:now',
            'motivo' => 'required|string|max:255'
        ]);

        $agendamento->reagendar($request->nova_data, $request->motivo);

        return redirect()->back()->with('success', 'Agendamento reagendado com sucesso!');
    }

    /**
     * Envia agendamento manualmente
     */
    public function enviarManual(Agendamento $agendamento)
    {
        if ($agendamento->status !== Agendamento::STATUS_AGENDADO) {
            return redirect()->back()->with('error', 'Agendamento não pode ser enviado no status atual.');
        }

        try {
            // Aqui seria implementada a lógica de envio
            // Por enquanto, apenas marca como enviado
            $agendamento->marcarComoEnviado();

            return redirect()->back()->with('success', 'Mensagem enviada com sucesso!');
        } catch (\Exception $e) {
            $agendamento->marcarComoFalhado($e->getMessage());
            return redirect()->back()->with('error', 'Falha ao enviar mensagem: ' . $e->getMessage());
        }
    }

    /**
     * Agendamento em lote
     */
    public function agendamentoLote(Request $request)
    {
        $request->validate([
            'cidadaos' => 'required|array',
            'cidadaos.*' => 'exists:cidadaos,id',
            'template_id' => 'required|exists:templates_messages,id',
            'data_agendamento' => 'required|date|after:now',
            'canal' => 'required|in:email,sms,whatsapp,push',
            'tipo' => 'required|in:lembrete,follow_up,convite,informativo,pesquisa,campanha'
        ]);

        $template = TemplateMessage::find($request->template_id);
        $contador = 0;

        foreach ($request->cidadaos as $cidadao_id) {
            Agendamento::create([
                'cidadao_id' => $cidadao_id,
                'usuario_id' => Auth::id(),
                'template_id' => $template->id,
                'tipo' => $request->tipo,
                'canal' => $request->canal,
                'titulo' => $template->nome,
                'conteudo' => $template->conteudo,
                'data_agendamento' => $request->data_agendamento,
                'prioridade' => $request->prioridade ?? 'normal'
            ]);
            $contador++;
        }

        $template->incrementarUso();

        return redirect()->route('agendamentos.index')
            ->with('success', "Agendados {$contador} envios com sucesso!");
    }

    /**
     * Retorna estatísticas rápidas
     */
    private function getEstatisticasRapidas()
    {
        return [
            'total' => Agendamento::count(),
            'agendados' => Agendamento::where('status', Agendamento::STATUS_AGENDADO)->count(),
            'enviados' => Agendamento::where('status', Agendamento::STATUS_ENVIADO)->count(),
            'falhados' => Agendamento::where('status', Agendamento::STATUS_FALHADO)->count(),
            'atrasados' => Agendamento::atrasados()->count()
        ];
    }

    /**
     * Retorna agendamentos por canal
     */
    private function getAgendamentosPorCanal()
    {
        return Agendamento::select('canal', DB::raw('count(*) as total'))
            ->groupBy('canal')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'canal');
    }

    /**
     * Retorna agendamentos por status
     */
    private function getAgendamentosPorStatus()
    {
        return Agendamento::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'status');
    }

    /**
     * Retorna timeline de agendamentos
     */
    private function getTimelineAgendamentos()
    {
        $dados = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = now()->subDays($i);
            $total = Agendamento::whereDate('created_at', $data)->count();
            
            $dados[] = [
                'data' => $data->format('d/m'),
                'total' => $total
            ];
        }
        
        return $dados;
    }

    /**
     * Retorna performance dos templates
     */
    private function getPerformanceTemplates()
    {
        return TemplateMessage::withCount(['agendamentos'])
            ->having('agendamentos_count', '>', 0)
            ->orderBy('agendamentos_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($template) {
                return [
                    'nome' => $template->nome,
                    'usos' => $template->agendamentos_count,
                    'taxa_sucesso' => $template->calcularTaxaSucesso()
                ];
            });
    }

    /**
     * API para buscar templates por canal
     */
    public function templatesPorCanal(Request $request)
    {
        $canal = $request->get('canal');
        
        $templates = TemplateMessage::ativo()
            ->porCanal($canal)
            ->orderBy('nome')
            ->get(['id', 'nome', 'descricao', 'tipo']);

        return response()->json($templates);
    }

    /**
     * API para preview de template
     */
    public function previewTemplate(Request $request)
    {
        $template = TemplateMessage::find($request->template_id);
        $cidadao = Cidadao::find($request->cidadao_id);

        if (!$template || !$cidadao) {
            return response()->json(['error' => 'Template ou cidadão não encontrado'], 404);
        }

        $dados = [
            'cidadao' => $cidadao,
            'sistema' => [
                'data_hoje' => now()->format('d/m/Y'),
                'hora_atual' => now()->format('H:i'),
                'ano' => now()->year,
                'mes' => now()->format('m'),
                'dia' => now()->format('d')
            ]
        ];

        $preview = $template->getPreview($dados);

        return response()->json($preview);
    }
}
