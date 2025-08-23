<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cidadao;
use App\Models\Demanda;
use App\Models\Interacao;
use Illuminate\Support\Facades\DB;

class SegmentacaoController extends Controller
{
    /**
     * Dashboard de segmentação
     */
    public function index()
    {
        // Segmentos pré-definidos
        $segmentosPredefinidos = $this->getSegmentosPredefinidos();
        
        // Estatísticas gerais
        $estatisticas = [
            'total_cidadaos' => Cidadao::count(),
            'total_bairros' => Cidadao::distinct('bairro')->count(),
            'total_tags' => $this->getTotalTagsUnicas(),
            'segmentos_ativos' => count($segmentosPredefinidos)
        ];

        return view('segmentacao.index', compact('segmentosPredefinidos', 'estatisticas'));
    }

    /**
     * Construtor de segmentação avançada
     */
    public function construtor()
    {
        $opcoesFiltros = $this->getOpcoesFiltros();
        
        return view('segmentacao.construtor', compact('opcoesFiltros'));
    }

    /**
     * Processa segmentação customizada
     */
    public function processar(Request $request)
    {
        $filtros = $request->get('filtros', []);
        $operadores = $request->get('operadores', []);
        
        $query = Cidadao::query();
        
        // Aplicar filtros dinâmicos
        foreach ($filtros as $index => $filtro) {
            if (empty($filtro['campo']) || empty($filtro['valor'])) {
                continue;
            }
            
            $operador = $operadores[$index] ?? 'and';
            $metodo = $operador === 'or' ? 'orWhere' : 'where';
            
            $this->aplicarFiltro($query, $filtro, $metodo);
        }
        
        $cidadaos = $query->paginate(20);
        
        // Estatísticas do resultado
        $estatisticasResultado = $this->calcularEstatisticasSegmento($query);
        
        return view('segmentacao.resultado', compact('cidadaos', 'estatisticasResultado', 'filtros'));
    }

    /**
     * Salva segmento personalizado
     */
    public function salvarSegmento(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'filtros' => 'required|array',
            'publico' => 'boolean'
        ]);

        // Aqui seria implementado o salvamento do segmento
        // Por enquanto, apenas retorna sucesso
        
        return response()->json([
            'success' => true,
            'message' => 'Segmento salvo com sucesso!',
            'segmento_id' => time() // ID simulado
        ]);
    }

    /**
     * Sugestões inteligentes de segmentação
     */
    public function sugestoes()
    {
        $sugestoes = [
            'engajamento_alto' => $this->getSugestaoEngajamentoAlto(),
            'demandas_abertas' => $this->getSugestaoDemandasAbertas(),
            'novos_cadastros' => $this->getSugestaoNovosCadastros(),
            'inativos' => $this->getSugestaoInativos(),
            'por_categoria' => $this->getSugestoesPorCategoria()
        ];

        return view('segmentacao.sugestoes', compact('sugestoes'));
    }

    /**
     * Análise de comportamento
     */
    public function analiseComportamento()
    {
        $analises = [
            'jornada_cidadao' => $this->getJornadaCidadao(),
            'padroes_interacao' => $this->getPadroesInteracao(),
            'sazonalidade' => $this->getSazonalidade(),
            'abandono' => $this->getAnaliseAbandono()
        ];

        return view('segmentacao.analise-comportamento', compact('analises'));
    }

    /**
     * Exportar segmento
     */
    public function exportar(Request $request)
    {
        $formato = $request->get('formato', 'csv');
        $filtros = $request->get('filtros', []);
        
        $query = Cidadao::query();
        
        // Aplicar filtros
        foreach ($filtros as $filtro) {
            if (!empty($filtro['campo']) && !empty($filtro['valor'])) {
                $this->aplicarFiltro($query, $filtro, 'where');
            }
        }
        
        $cidadaos = $query->get();
        
        switch ($formato) {
            case 'csv':
                return $this->exportarCSV($cidadaos);
            case 'pdf':
                return $this->exportarPDF($cidadaos);
            case 'excel':
                return $this->exportarExcel($cidadaos);
            default:
                return redirect()->back()->with('error', 'Formato de exportação inválido.');
        }
    }

    /**
     * API: Busca de cidadãos para seleção
     */
    public function buscarCidadaos(Request $request)
    {
        $termo = $request->get('q');
        $limite = $request->get('limit', 10);
        
        $cidadaos = Cidadao::where('nome', 'like', "%{$termo}%")
            ->orWhere('email', 'like', "%{$termo}%")
            ->orWhere('bairro', 'like', "%{$termo}%")
            ->limit($limite)
            ->get(['id', 'nome', 'email', 'bairro']);
        
        return response()->json($cidadaos);
    }

    /**
     * API: Opções dinâmicas para filtros
     */
    public function opcoesFiltro(Request $request)
    {
        $campo = $request->get('campo');
        
        switch ($campo) {
            case 'bairro':
                $opcoes = Cidadao::distinct()->pluck('bairro')->filter();
                break;
            case 'status':
                $opcoes = collect(Cidadao::getStatusOptions())->keys();
                break;
            case 'nivel_engajamento':
                $opcoes = collect(Cidadao::getEngajamentoOptions())->keys();
                break;
            case 'origem_cadastro':
                $opcoes = Cidadao::distinct()->pluck('origem_cadastro')->filter();
                break;
            case 'tags':
                $opcoes = $this->getTagsDisponiveis();
                break;
            default:
                $opcoes = [];
        }
        
        return response()->json($opcoes);
    }

    /**
     * Segmentos pré-definidos
     */
    private function getSegmentosPredefinidos()
    {
        return [
            'leads_ativos' => [
                'nome' => 'Leads Ativos',
                'descricao' => 'Cidadãos em status de lead com interações recentes',
                'total' => Cidadao::where('status', 'lead')
                    ->whereHas('interacoes', function($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    })->count(),
                'filtros' => [
                    ['campo' => 'status', 'operador' => '=', 'valor' => 'lead'],
                    ['campo' => 'interacoes_recentes', 'operador' => '>=', 'valor' => '30_dias']
                ]
            ],
            'apoiadores_engajados' => [
                'nome' => 'Apoiadores Engajados',
                'descricao' => 'Cidadãos apoiadores com alto nível de engajamento',
                'total' => Cidadao::where('status', 'apoiador')
                    ->where('nivel_engajamento', 'alto')->count(),
                'filtros' => [
                    ['campo' => 'status', 'operador' => '=', 'valor' => 'apoiador'],
                    ['campo' => 'nivel_engajamento', 'operador' => '=', 'valor' => 'alto']
                ]
            ],
            'demandas_pendentes' => [
                'nome' => 'Com Demandas Pendentes',
                'descricao' => 'Cidadãos com demandas em aberto',
                'total' => Cidadao::whereHas('demandas', function($q) {
                    $q->whereIn('status', ['aberta', 'em_andamento']);
                })->count(),
                'filtros' => [
                    ['campo' => 'demandas_status', 'operador' => 'in', 'valor' => ['aberta', 'em_andamento']]
                ]
            ],
            'sem_interacao' => [
                'nome' => 'Sem Interação (90 dias)',
                'descricao' => 'Cidadãos sem interação nos últimos 90 dias',
                'total' => Cidadao::whereDoesntHave('interacoes', function($q) {
                    $q->where('created_at', '>=', now()->subDays(90));
                })->count(),
                'filtros' => [
                    ['campo' => 'ultima_interacao', 'operador' => '<', 'valor' => '90_dias']
                ]
            ],
            'novos_mes' => [
                'nome' => 'Novos Cadastros (Este Mês)',
                'descricao' => 'Cidadãos cadastrados no mês atual',
                'total' => Cidadao::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'filtros' => [
                    ['campo' => 'created_at', 'operador' => '>=', 'valor' => now()->startOfMonth()]
                ]
            ],
            'por_bairro' => [
                'nome' => 'Por Bairro Prioritário',
                'descricao' => 'Cidadãos dos bairros com mais demandas',
                'total' => Cidadao::whereIn('bairro', $this->getBairrosPrioritarios())->count(),
                'filtros' => [
                    ['campo' => 'bairro', 'operador' => 'in', 'valor' => $this->getBairrosPrioritarios()]
                ]
            ]
        ];
    }

    /**
     * Opções de filtros disponíveis
     */
    private function getOpcoesFiltros()
    {
        return [
            'dados_pessoais' => [
                'nome' => 'Dados Pessoais',
                'campos' => [
                    'nome' => ['label' => 'Nome', 'tipo' => 'texto'],
                    'email' => ['label' => 'E-mail', 'tipo' => 'texto'],
                    'telefone' => ['label' => 'Telefone', 'tipo' => 'texto'],
                    'cpf' => ['label' => 'CPF', 'tipo' => 'texto'],
                    'idade' => ['label' => 'Idade', 'tipo' => 'numero'],
                    'bairro' => ['label' => 'Bairro', 'tipo' => 'selecao'],
                    'profissao' => ['label' => 'Profissão', 'tipo' => 'texto']
                ]
            ],
            'engajamento' => [
                'nome' => 'Engajamento',
                'campos' => [
                    'status' => ['label' => 'Status', 'tipo' => 'selecao'],
                    'nivel_engajamento' => ['label' => 'Nível de Engajamento', 'tipo' => 'selecao'],
                    'origem_cadastro' => ['label' => 'Origem do Cadastro', 'tipo' => 'selecao'],
                    'score_engajamento' => ['label' => 'Score de Engajamento', 'tipo' => 'numero']
                ]
            ],
            'atividade' => [
                'nome' => 'Atividade',
                'campos' => [
                    'created_at' => ['label' => 'Data de Cadastro', 'tipo' => 'data'],
                    'ultima_interacao' => ['label' => 'Última Interação', 'tipo' => 'data'],
                    'total_interacoes' => ['label' => 'Total de Interações', 'tipo' => 'numero'],
                    'total_demandas' => ['label' => 'Total de Demandas', 'tipo' => 'numero']
                ]
            ],
            'interesses' => [
                'nome' => 'Interesses e Tags',
                'campos' => [
                    'interesses_politicos' => ['label' => 'Interesses Políticos', 'tipo' => 'multipla_selecao'],
                    'tags' => ['label' => 'Tags', 'tipo' => 'multipla_selecao']
                ]
            ],
            'demandas' => [
                'nome' => 'Demandas',
                'campos' => [
                    'demandas_status' => ['label' => 'Status das Demandas', 'tipo' => 'selecao'],
                    'demandas_categoria' => ['label' => 'Categoria das Demandas', 'tipo' => 'selecao'],
                    'demandas_urgencia' => ['label' => 'Urgência das Demandas', 'tipo' => 'selecao']
                ]
            ]
        ];
    }

    /**
     * Aplica filtro específico na query
     */
    private function aplicarFiltro($query, $filtro, $metodo = 'where')
    {
        $campo = $filtro['campo'];
        $operador = $filtro['operador'] ?? '=';
        $valor = $filtro['valor'];

        switch ($campo) {
            case 'ultima_interacao':
                if (str_contains($valor, '_dias')) {
                    $dias = intval(str_replace('_dias', '', $valor));
                    $data = now()->subDays($dias);
                    
                    if ($operador === '<') {
                        $query->$metodo(function($q) use ($data) {
                            $q->whereDoesntHave('interacoes', function($q2) use ($data) {
                                $q2->where('created_at', '>=', $data);
                            });
                        });
                    }
                }
                break;
                
            case 'score_engajamento':
                // Implementar cálculo de score dinâmico
                break;
                
            case 'total_interacoes':
                $query->$metodo(function($q) use ($operador, $valor) {
                    $q->withCount('interacoes')
                      ->having('interacoes_count', $operador, $valor);
                });
                break;
                
            case 'total_demandas':
                $query->$metodo(function($q) use ($operador, $valor) {
                    $q->withCount('demandas')
                      ->having('demandas_count', $operador, $valor);
                });
                break;
                
            case 'demandas_status':
                $query->$metodo(function($q) use ($operador, $valor) {
                    $valores = is_array($valor) ? $valor : [$valor];
                    $q->whereHas('demandas', function($q2) use ($valores) {
                        $q2->whereIn('status', $valores);
                    });
                });
                break;
                
            case 'tags':
                if (is_array($valor)) {
                    foreach ($valor as $tag) {
                        $query->$metodo('tags', 'like', "%\"{$tag}\"%");
                    }
                } else {
                    $query->$metodo('tags', 'like', "%\"{$valor}\"%");
                }
                break;
                
            case 'interesses_politicos':
                if (is_array($valor)) {
                    foreach ($valor as $interesse) {
                        $query->$metodo('interesses_politicos', 'like', "%\"{$interesse}\"%");
                    }
                } else {
                    $query->$metodo('interesses_politicos', 'like', "%\"{$valor}\"%");
                }
                break;
                
            default:
                if ($operador === 'like') {
                    $query->$metodo($campo, 'like', "%{$valor}%");
                } elseif ($operador === 'in' && is_array($valor)) {
                    $query->$metodo($campo, $operador, $valor);
                } else {
                    $query->$metodo($campo, $operador, $valor);
                }
                break;
        }
    }

    /**
     * Calcula estatísticas do segmento
     */
    private function calcularEstatisticasSegmento($query)
    {
        $queryClone = clone $query;
        
        return [
            'total' => $queryClone->count(),
            'por_status' => $queryClone->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->pluck('total', 'status'),
            'por_bairro' => $queryClone->select('bairro', DB::raw('count(*) as total'))
                ->groupBy('bairro')->orderBy('total', 'desc')->limit(5)
                ->pluck('total', 'bairro'),
            'por_engajamento' => $queryClone->select('nivel_engajamento', DB::raw('count(*) as total'))
                ->groupBy('nivel_engajamento')->pluck('total', 'nivel_engajamento'),
            'idade_media' => $queryClone->whereNotNull('idade')->avg('idade'),
            'cadastros_recentes' => $queryClone->where('created_at', '>=', now()->subDays(30))->count()
        ];
    }

    /**
     * Exporta para CSV
     */
    private function exportarCSV($cidadaos)
    {
        $filename = 'segmento_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];
        
        $callback = function() use ($cidadaos) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho
            fputcsv($file, [
                'ID', 'Nome', 'Email', 'Telefone', 'Bairro', 'Status', 
                'Engajamento', 'Data Cadastro'
            ]);
            
            // Dados
            foreach ($cidadaos as $cidadao) {
                fputcsv($file, [
                    $cidadao->id,
                    $cidadao->nome,
                    $cidadao->email,
                    $cidadao->telefone,
                    $cidadao->bairro,
                    $cidadao->status,
                    $cidadao->nivel_engajamento,
                    $cidadao->created_at->format('d/m/Y')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Retorna total de tags únicas
     */
    private function getTotalTagsUnicas()
    {
        $tags = Cidadao::whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->filter();
            
        return $tags->count();
    }

    /**
     * Retorna tags disponíveis
     */
    private function getTagsDisponiveis()
    {
        return Cidadao::whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->filter()
            ->values()
            ->take(50); // Limitar para performance
    }

    /**
     * Retorna bairros prioritários (com mais demandas)
     */
    private function getBairrosPrioritarios()
    {
        return Demanda::select('bairro', DB::raw('count(*) as total'))
            ->groupBy('bairro')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->pluck('bairro')
            ->toArray();
    }

    // Métodos auxiliares para sugestões inteligentes
    private function getSugestaoEngajamentoAlto()
    {
        return [
            'titulo' => 'Cidadãos com Alto Engajamento',
            'descricao' => 'Foque nos seus apoiadores mais ativos',
            'total' => Cidadao::where('nivel_engajamento', 'alto')->count(),
            'acao_sugerida' => 'Envie conteúdo exclusivo ou convites especiais'
        ];
    }

    private function getSugestaoDemandasAbertas()
    {
        return [
            'titulo' => 'Acompanhar Demandas em Aberto',
            'descricao' => 'Cidadãos aguardando resolução de demandas',
            'total' => Cidadao::whereHas('demandas', function($q) {
                $q->whereIn('status', ['aberta', 'em_andamento']);
            })->count(),
            'acao_sugerida' => 'Envie atualizações sobre o andamento'
        ];
    }

    private function getSugestaoNovosCadastros()
    {
        return [
            'titulo' => 'Novos Cadastros (Últimos 7 dias)',
            'descricao' => 'Dê as boas-vindas aos novos cidadãos',
            'total' => Cidadao::where('created_at', '>=', now()->subDays(7))->count(),
            'acao_sugerida' => 'Envie mensagem de boas-vindas'
        ];
    }

    private function getSugestaoInativos()
    {
        return [
            'titulo' => 'Reativar Cidadãos Inativos',
            'descricao' => 'Cidadãos sem interação há mais de 60 dias',
            'total' => Cidadao::whereDoesntHave('interacoes', function($q) {
                $q->where('created_at', '>=', now()->subDays(60));
            })->count(),
            'acao_sugerida' => 'Campanha de reativação'
        ];
    }

    private function getSugestoesPorCategoria()
    {
        $categorias = ['educacao', 'saude', 'infraestrutura', 'seguranca'];
        $sugestoes = [];

        foreach ($categorias as $categoria) {
            $total = Cidadao::whereJsonContains('interesses_politicos', $categoria)->count();
            if ($total > 0) {
                $sugestoes[] = [
                    'titulo' => "Interessados em " . ucfirst($categoria),
                    'total' => $total,
                    'acao_sugerida' => "Envie conteúdo específico sobre {$categoria}"
                ];
            }
        }

        return $sugestoes;
    }

    // Métodos para análise de comportamento (implementação básica)
    private function getJornadaCidadao()
    {
        return [
            'cadastro_to_primeira_interacao' => 5.2, // dias médios
            'primeira_interacao_to_demanda' => 12.8,
            'demanda_to_resolucao' => 15.5,
            'taxa_conversao_lead_apoiador' => 23.5 // %
        ];
    }

    private function getPadroesInteracao()
    {
        return [
            'canal_preferido' => 'whatsapp',
            'dia_semana_mais_ativo' => 'terça-feira',
            'horario_pico' => '14h-16h',
            'tempo_resposta_medio' => 2.3 // horas
        ];
    }

    private function getSazonalidade()
    {
        return [
            'mes_mais_ativo' => 'março',
            'periodo_baixa_atividade' => 'dezembro-janeiro',
            'picos_demanda' => ['início do ano', 'meio do ano']
        ];
    }

    private function getAnaliseAbandono()
    {
        return [
            'taxa_abandono_30_dias' => 15.2, // %
            'principais_motivos' => ['falta de resposta', 'mudança de endereço'],
            'ponto_critico' => 'após 45 dias sem interação'
        ];
    }
}
