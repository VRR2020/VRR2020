<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cidadao;

class CidadaosController extends Controller
{
    /**
     * Lista todos os cidadãos cadastrados
     */
    public function index(Request $request)
    {
        // Filtros e busca
        $filtros = [
            'busca' => $request->get('busca'),
            'bairro' => $request->get('bairro'),
            'tags' => $request->get('tags'),
            'status' => $request->get('status')
        ];

        // Query base
        $query = Cidadao::query();

        // Aplicar filtros
        if (!empty($filtros['busca'])) {
            $query->buscar($filtros['busca']);
        }

        if (!empty($filtros['bairro'])) {
            $query->porBairro($filtros['bairro']);
        }

        if (!empty($filtros['status'])) {
            $query->porStatus($filtros['status']);
        }

        if (!empty($filtros['tags'])) {
            $query->comTag($filtros['tags']);
        }

        // Buscar cidadãos com paginação
        $cidadaos = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('cidadaos.index', compact('cidadaos', 'filtros'));
    }

    /**
     * Formulário para cadastrar novo cidadão
     */
    public function create()
    {
        return view('cidadaos.create');
    }

    /**
     * Salva novo cidadão
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:cidadaos',
            'email' => 'required|email|unique:cidadaos',
            'telefone' => 'required|string|max:20',
            'bairro' => 'required|string|max:100',
            'idade' => 'nullable|integer|min:16|max:120',
            'data_nascimento' => 'nullable|date',
            'profissao' => 'nullable|string|max:100',
            'renda_familiar' => 'nullable|string|max:50',
            'endereco' => 'nullable|string',
            'interesses_politicos' => 'nullable|array',
            'status' => 'nullable|in:lead,engajado,ativo,apoiador,inativo',
            'nivel_engajamento' => 'nullable|in:baixo,medio,alto',
            'origem_cadastro' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string',
            'redes_sociais' => 'nullable|array'
        ]);

        // Processar tags (converter string separada por vírgulas em array)
        $tags = [];
        if ($request->filled('tags')) {
            $tags = array_map('trim', explode(',', $request->tags));
            $tags = array_filter($tags); // Remove valores vazios
        }

        // Preparar dados para salvamento
        $dados = $request->only([
            'nome', 'cpf', 'email', 'telefone', 'bairro', 'endereco',
            'idade', 'data_nascimento', 'profissao', 'renda_familiar',
            'interesses_politicos', 'status', 'nivel_engajamento',
            'origem_cadastro', 'observacoes', 'redes_sociais'
        ]);

        // Adicionar tags processadas
        $dados['tags'] = $tags;

        // Definir valores padrão se não informados
        $dados['status'] = $dados['status'] ?? 'lead';
        $dados['nivel_engajamento'] = $dados['nivel_engajamento'] ?? 'baixo';

        // Criar o cidadão
        Cidadao::create($dados);

        return redirect()->route('cidadaos.index')
            ->with('success', 'Cidadão cadastrado com sucesso!');
    }

    /**
     * Exibe perfil completo do cidadão
     */
    public function show($id)
    {
        $cidadao = Cidadao::with(['demandas', 'interacoes', 'agendamentos'])
                          ->findOrFail($id);

        return view('cidadaos.show', compact('cidadao'));
    }

    /**
     * Formulário para editar cidadão
     */
    public function edit($id)
    {
        $cidadao = Cidadao::findOrFail($id);

        return view('cidadaos.edit', compact('cidadao'));
    }

    /**
     * Atualiza dados do cidadão
     */
    public function update(Request $request, $id)
    {
        $cidadao = Cidadao::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:cidadaos,cpf,' . $cidadao->id,
            'email' => 'required|email|unique:cidadaos,email,' . $cidadao->id,
            'telefone' => 'required|string|max:20',
            'bairro' => 'required|string|max:100',
            'idade' => 'nullable|integer|min:16|max:120',
            'data_nascimento' => 'nullable|date',
            'profissao' => 'nullable|string|max:100',
            'renda_familiar' => 'nullable|string|max:50',
            'endereco' => 'nullable|string',
            'interesses_politicos' => 'nullable|array',
            'status' => 'nullable|in:lead,engajado,ativo,apoiador,inativo',
            'nivel_engajamento' => 'nullable|in:baixo,medio,alto',
            'origem_cadastro' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string',
            'redes_sociais' => 'nullable|array'
        ]);

        // Processar tags
        $tags = [];
        if ($request->filled('tags')) {
            $tags = array_map('trim', explode(',', $request->tags));
            $tags = array_filter($tags);
        }

        // Preparar dados para atualização
        $dados = $request->only([
            'nome', 'cpf', 'email', 'telefone', 'bairro', 'endereco',
            'idade', 'data_nascimento', 'profissao', 'renda_familiar',
            'interesses_politicos', 'status', 'nivel_engajamento',
            'origem_cadastro', 'observacoes', 'redes_sociais'
        ]);

        $dados['tags'] = $tags;

        // Atualizar o cidadão
        $cidadao->update($dados);

        return redirect()->route('cidadaos.show', $id)
            ->with('success', 'Dados atualizados com sucesso!');
    }

    /**
     * Importa cidadãos via planilha CSV
     */
    public function importar(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        // TODO: Implementar importação CSV
        
        return redirect()->route('cidadaos.index')
            ->with('success', 'Planilha importada com sucesso!');
    }
}
