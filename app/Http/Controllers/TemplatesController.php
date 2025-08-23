<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemplateMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemplatesController extends Controller
{
    /**
     * Lista todos os templates
     */
    public function index(Request $request)
    {
        $query = TemplateMessage::with('usuario');

        // Aplicar filtros
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('canal')) {
            $query->porCanal($request->canal);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === 'true');
        }

        if ($request->filled('busca')) {
            $query->buscar($request->busca);
        }

        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        
        if ($orderBy === 'uso') {
            $orderBy = 'uso_contador';
        }

        $templates = $query->orderBy($orderBy, $orderDirection)->paginate(20);

        // Estatísticas rápidas
        $estatisticas = [
            'total' => TemplateMessage::count(),
            'ativos' => TemplateMessage::where('ativo', true)->count(),
            'inativos' => TemplateMessage::where('ativo', false)->count(),
            'mais_usado' => TemplateMessage::orderBy('uso_contador', 'desc')->first()
        ];

        $filtros = $request->only(['tipo', 'canal', 'categoria', 'ativo', 'busca', 'order_by', 'order_direction']);

        return view('templates.index', compact('templates', 'estatisticas', 'filtros'));
    }

    /**
     * Formulário para criar novo template
     */
    public function create()
    {
        $variaveisPadrao = TemplateMessage::getVariaveisPadrao();
        
        return view('templates.create', compact('variaveisPadrao'));
    }

    /**
     * Salva novo template
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:templates_messages,nome',
            'tipo' => 'required|in:welcome,follow_up,lembrete,convite,newsletter,pesquisa,agradecimento,confirmacao,cancelamento,personalizado',
            'canal' => 'required|in:email,sms,whatsapp,push,todos',
            'categoria' => 'required|in:administrativo,marketing,suporte,eventos,politico,social',
            'assunto' => 'nullable|string|max:255',
            'conteudo' => 'required|string',
            'descricao' => 'nullable|string'
        ]);

        $template = TemplateMessage::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'tipo' => $request->tipo,
            'canal' => $request->canal,
            'categoria' => $request->categoria,
            'assunto' => $request->assunto,
            'conteudo' => $request->conteudo,
            'variaveis_disponiveis' => $request->variaveis_personalizadas ? json_decode($request->variaveis_personalizadas, true) : null,
            'configuracoes' => $request->configuracoes ? json_decode($request->configuracoes, true) : null,
            'tags' => $request->tags ? explode(',', $request->tags) : null,
            'ativo' => $request->has('ativo'),
            'usuario_id' => Auth::id()
        ]);

        return redirect()->route('templates.show', $template)
            ->with('success', 'Template criado com sucesso!');
    }

    /**
     * Exibe detalhes do template
     */
    public function show(TemplateMessage $template)
    {
        $template->load('usuario');
        
        // Estatísticas de uso
        $estatisticas = $template->getEstatisticasUso();
        
        // Variáveis disponíveis
        $variaveisDisponiveis = $template->getTodasVariaveis();
        
        // Preview com dados de exemplo
        $dadosExemplo = [
            'cidadao' => (object)[
                'nome' => 'João Silva',
                'email' => 'joao@email.com',
                'telefone' => '(11) 99999-9999',
                'bairro' => 'Centro',
                'idade' => 35
            ],
            'sistema' => [
                'data_hoje' => now()->format('d/m/Y'),
                'hora_atual' => now()->format('H:i'),
                'ano' => now()->year,
                'mes' => now()->format('m'),
                'dia' => now()->format('d')
            ]
        ];
        
        $preview = $template->getPreview($dadosExemplo);

        return view('templates.show', compact('template', 'estatisticas', 'variaveisDisponiveis', 'preview'));
    }

    /**
     * Formulário para editar template
     */
    public function edit(TemplateMessage $template)
    {
        $variaveisPadrao = TemplateMessage::getVariaveisPadrao();
        
        return view('templates.edit', compact('template', 'variaveisPadrao'));
    }

    /**
     * Atualiza template
     */
    public function update(Request $request, TemplateMessage $template)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:templates_messages,nome,' . $template->id,
            'tipo' => 'required|in:welcome,follow_up,lembrete,convite,newsletter,pesquisa,agradecimento,confirmacao,cancelamento,personalizado',
            'canal' => 'required|in:email,sms,whatsapp,push,todos',
            'categoria' => 'required|in:administrativo,marketing,suporte,eventos,politico,social',
            'assunto' => 'nullable|string|max:255',
            'conteudo' => 'required|string',
            'descricao' => 'nullable|string'
        ]);

        $template->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'tipo' => $request->tipo,
            'canal' => $request->canal,
            'categoria' => $request->categoria,
            'assunto' => $request->assunto,
            'conteudo' => $request->conteudo,
            'variaveis_disponiveis' => $request->variaveis_personalizadas ? json_decode($request->variaveis_personalizadas, true) : null,
            'configuracoes' => $request->configuracoes ? json_decode($request->configuracoes, true) : null,
            'tags' => $request->tags ? explode(',', $request->tags) : null,
            'ativo' => $request->has('ativo')
        ]);

        return redirect()->route('templates.show', $template)
            ->with('success', 'Template atualizado com sucesso!');
    }

    /**
     * Remove template
     */
    public function destroy(TemplateMessage $template)
    {
        // Verificar se não há agendamentos usando este template
        if ($template->agendamentos()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir template que possui agendamentos associados.');
        }

        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template excluído com sucesso!');
    }

    /**
     * Ativa/desativa template
     */
    public function toggleStatus(TemplateMessage $template)
    {
        $template->update(['ativo' => !$template->ativo]);

        $status = $template->ativo ? 'ativado' : 'desativado';
        
        return redirect()->back()->with('success', "Template {$status} com sucesso!");
    }

    /**
     * Clona template
     */
    public function clonar(TemplateMessage $template)
    {
        $clone = $template->clonar();

        return redirect()->route('templates.edit', $clone)
            ->with('success', 'Template clonado com sucesso! Edite os dados conforme necessário.');
    }

    /**
     * Dashboard de templates
     */
    public function dashboard()
    {
        $estatisticas = [
            'total_templates' => TemplateMessage::count(),
            'templates_ativos' => TemplateMessage::where('ativo', true)->count(),
            'mais_usado' => TemplateMessage::orderBy('uso_contador', 'desc')->first(),
            'criados_mes' => TemplateMessage::whereMonth('created_at', now()->month)->count()
        ];

        // Gráficos
        $graficos = [
            'por_tipo' => $this->getTemplatesPorTipo(),
            'por_canal' => $this->getTemplatesPorCanal(),
            'mais_usados' => $this->getTemplatesMaisUsados(),
            'criacao_tempo' => $this->getTemplatesCriacaoTempo()
        ];

        return view('templates.dashboard', compact('estatisticas', 'graficos'));
    }

    /**
     * Biblioteca de templates
     */
    public function biblioteca()
    {
        $templatesPadrao = $this->getTemplatesPadrao();
        
        return view('templates.biblioteca', compact('templatesPadrao'));
    }

    /**
     * Instala template da biblioteca
     */
    public function instalarTemplate(Request $request)
    {
        $request->validate([
            'template_key' => 'required|string',
            'personalizar' => 'boolean'
        ]);

        $templatesPadrao = $this->getTemplatesPadrao();
        $templateKey = $request->template_key;

        if (!isset($templatesPadrao[$templateKey])) {
            return redirect()->back()->with('error', 'Template não encontrado.');
        }

        $dadosTemplate = $templatesPadrao[$templateKey];

        $template = TemplateMessage::create([
            'nome' => $dadosTemplate['nome'],
            'descricao' => $dadosTemplate['descricao'],
            'tipo' => $dadosTemplate['tipo'],
            'canal' => $dadosTemplate['canal'],
            'categoria' => $dadosTemplate['categoria'],
            'assunto' => $dadosTemplate['assunto'],
            'conteudo' => $dadosTemplate['conteudo'],
            'usuario_id' => Auth::id(),
            'ativo' => true
        ]);

        if ($request->personalizar) {
            return redirect()->route('templates.edit', $template)
                ->with('success', 'Template instalado! Personalize conforme necessário.');
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template instalado com sucesso!');
    }

    /**
     * Preview de template com dados específicos
     */
    public function preview(Request $request, TemplateMessage $template)
    {
        $dados = $request->get('dados', []);
        $preview = $template->getPreview($dados);

        return response()->json($preview);
    }

    /**
     * Exportar template
     */
    public function exportar(TemplateMessage $template)
    {
        $dados = [
            'template' => $template->toArray(),
            'metadata' => [
                'exportado_em' => now()->toISOString(),
                'exportado_por' => Auth::user()->name ?? 'Sistema',
                'versao_sistema' => '1.0'
            ]
        ];

        $nomeArquivo = 'template_' . $template->id . '_' . now()->format('Y-m-d') . '.json';

        return response()->json($dados)
            ->header('Content-Disposition', 'attachment; filename="' . $nomeArquivo . '"');
    }

    /**
     * Importar template
     */
    public function importar(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:json',
            'substituir_existente' => 'boolean'
        ]);

        try {
            $conteudo = file_get_contents($request->file('arquivo')->getRealPath());
            $dados = json_decode($conteudo, true);

            if (!isset($dados['template'])) {
                throw new \Exception('Arquivo de template inválido.');
            }

            $dadosTemplate = $dados['template'];
            
            // Verificar se já existe
            $existente = TemplateMessage::where('nome', $dadosTemplate['nome'])->first();
            
            if ($existente && !$request->substituir_existente) {
                return redirect()->back()
                    ->with('error', 'Template com este nome já existe. Marque a opção para substituir.');
            }

            if ($existente) {
                $existente->update([
                    'descricao' => $dadosTemplate['descricao'],
                    'tipo' => $dadosTemplate['tipo'],
                    'canal' => $dadosTemplate['canal'],
                    'categoria' => $dadosTemplate['categoria'],
                    'assunto' => $dadosTemplate['assunto'],
                    'conteudo' => $dadosTemplate['conteudo'],
                    'usuario_id' => Auth::id()
                ]);
                
                $template = $existente;
            } else {
                $template = TemplateMessage::create([
                    'nome' => $dadosTemplate['nome'],
                    'descricao' => $dadosTemplate['descricao'],
                    'tipo' => $dadosTemplate['tipo'],
                    'canal' => $dadosTemplate['canal'],
                    'categoria' => $dadosTemplate['categoria'],
                    'assunto' => $dadosTemplate['assunto'],
                    'conteudo' => $dadosTemplate['conteudo'],
                    'usuario_id' => Auth::id(),
                    'ativo' => true
                ]);
            }

            return redirect()->route('templates.show', $template)
                ->with('success', 'Template importado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao importar template: ' . $e->getMessage());
        }
    }

    /**
     * Retorna templates por tipo
     */
    private function getTemplatesPorTipo()
    {
        return TemplateMessage::select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'tipo');
    }

    /**
     * Retorna templates por canal
     */
    private function getTemplatesPorCanal()
    {
        return TemplateMessage::select('canal', DB::raw('count(*) as total'))
            ->groupBy('canal')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'canal');
    }

    /**
     * Retorna templates mais usados
     */
    private function getTemplatesMaisUsados()
    {
        return TemplateMessage::orderBy('uso_contador', 'desc')
            ->limit(10)
            ->get(['nome', 'uso_contador'])
            ->pluck('uso_contador', 'nome');
    }

    /**
     * Retorna criação de templates ao longo do tempo
     */
    private function getTemplatesCriacaoTempo()
    {
        $dados = [];
        for ($i = 11; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $total = TemplateMessage::whereYear('created_at', $data->year)
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
     * Retorna templates padrão para biblioteca
     */
    private function getTemplatesPadrao()
    {
        return [
            'welcome_email' => [
                'nome' => 'Boas-vindas por E-mail',
                'descricao' => 'Template de boas-vindas para novos cidadãos cadastrados',
                'tipo' => 'welcome',
                'canal' => 'email',
                'categoria' => 'administrativo',
                'assunto' => 'Bem-vindo(a) ao nosso gabinete, {{nome}}!',
                'conteudo' => "Olá {{nome}},\n\nSeja muito bem-vindo(a) ao nosso gabinete!\n\nEstamos muito felizes em tê-lo(a) como parte da nossa comunidade. Nosso compromisso é trabalhar sempre em prol dos interesses dos cidadãos do bairro {{bairro}} e de toda nossa região.\n\nAtravés deste canal, você receberá informações sobre nossas atividades, projetos em andamento e poderá registrar suas demandas e sugestões.\n\nConte sempre conosco!\n\nAtenciosamente,\nEquipe do Gabinete"
            ],
            'follow_up_demanda' => [
                'nome' => 'Follow-up de Demanda',
                'descricao' => 'Template para acompanhamento de demandas em andamento',
                'tipo' => 'follow_up',
                'canal' => 'whatsapp',
                'categoria' => 'suporte',
                'assunto' => 'Atualização sobre sua solicitação',
                'conteudo' => "Olá {{nome}}!\n\nVenho trazer uma atualização sobre sua demanda registrada em {{data_demanda}}:\n\n*{{demanda_titulo}}*\nProtocolo: {{demanda_protocolo}}\nStatus atual: {{demanda_status}}\n\nEstamos trabalhando para resolver sua solicitação o mais breve possível. Em caso de dúvidas, entre em contato conosco.\n\nObrigado!"
            ],
            'convite_evento' => [
                'nome' => 'Convite para Evento',
                'descricao' => 'Template para convidar cidadãos para eventos e reuniões',
                'tipo' => 'convite',
                'canal' => 'email',
                'categoria' => 'eventos',
                'assunto' => 'Você está convidado(a): {{evento_nome}}',
                'conteudo' => "Prezado(a) {{nome}},\n\nTenho o prazer de convidá-lo(a) para participar do evento:\n\n*{{evento_nome}}*\n\nData: {{evento_data}}\nHorário: {{evento_horario}}\nLocal: {{evento_local}}\n\n{{evento_descricao}}\n\nSua presença é muito importante para nós!\n\nPara confirmar sua participação, responda este e-mail ou entre em contato pelo telefone {{gabinete_telefone}}.\n\nAguardamos você!\n\nCordialmente,\nEquipe do Gabinete"
            ]
        ];
    }
}
