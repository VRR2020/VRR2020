<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemandasController extends Controller
{
    /**
     * Lista todas as demandas
     */
    public function index(Request $request)
    {
        $filtros = [
            'status' => $request->get('status'),
            'tipo' => $request->get('tipo'),
            'urgencia' => $request->get('urgencia'),
            'bairro' => $request->get('bairro'),
            'data_inicio' => $request->get('data_inicio'),
            'data_fim' => $request->get('data_fim')
        ];

        // Dados mockados para desenvolvimento
        $demandas = collect([]);
        
        return view('demandas.index', compact('demandas', 'filtros'));
    }

    /**
     * Formulário para criar nova demanda
     */
    public function create()
    {
        return view('demandas.create');
    }

    /**
     * Salva nova demanda
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo' => 'required|in:individual,coletiva',
            'categoria' => 'required|string|max:100',
            'urgencia' => 'required|in:baixa,media,alta,critica',
            'cidadao_id' => 'required|integer',
            'bairro' => 'required|string|max:100',
        ]);

        // TODO: Implementar salvamento no banco
        
        return redirect()->route('demandas.index')
            ->with('success', 'Demanda registrada com sucesso!');
    }

    /**
     * Exibe detalhes da demanda
     */
    public function show($id)
    {
        // TODO: Buscar demanda no banco
        $demanda = (object) [
            'id' => $id,
            'titulo' => 'Solicitação de reparo na rua',
            'descricao' => 'Buraco na rua precisa ser reparado urgentemente.',
            'tipo' => 'individual',
            'categoria' => 'infraestrutura',
            'urgencia' => 'alta',
            'status' => 'em_andamento',
            'cidadao_nome' => 'Jo��o Silva',
            'bairro' => 'Centro',
            'created_at' => now(),
            'updated_at' => now()
        ];

        return view('demandas.show', compact('demanda'));
    }

    /**
     * Atualiza status da demanda
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aberta,em_andamento,resolvida,cancelada',
            'observacoes' => 'nullable|string'
        ]);

        // TODO: Implementar atualização no banco
        
        return redirect()->route('demandas.show', $id)
            ->with('success', 'Status atualizado com sucesso!');
    }

    /**
     * Dashboard específico de demandas
     */
    public function dashboard()
    {
        $estatisticas = [
            'total' => 0,
            'abertas' => 0,
            'em_andamento' => 0,
            'resolvidas' => 0,
            'por_categoria' => [],
            'por_bairro' => [],
            'tempo_medio_resolucao' => 0
        ];

        return view('demandas.dashboard', compact('estatisticas'));
    }
}
