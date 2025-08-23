<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // Dados mockados para desenvolvimento
        $cidadaos = collect([]);
        
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
        ]);

        // TODO: Implementar salvamento no banco
        
        return redirect()->route('cidadaos.index')
            ->with('success', 'Cidadão cadastrado com sucesso!');
    }

    /**
     * Exibe perfil completo do cidadão
     */
    public function show($id)
    {
        // TODO: Buscar cidadão no banco
        $cidadao = (object) [
            'id' => $id,
            'nome' => 'João Silva',
            'cpf' => '123.456.789-00',
            'email' => 'joao@email.com',
            'telefone' => '(11) 99999-9999',
            'bairro' => 'Centro',
            'idade' => 35,
            'tags' => ['educação', 'infraestrutura'],
            'created_at' => now()
        ];

        return view('cidadaos.show', compact('cidadao'));
    }

    /**
     * Formulário para editar cidadão
     */
    public function edit($id)
    {
        // TODO: Buscar cidadão no banco
        $cidadao = (object) [
            'id' => $id,
            'nome' => 'João Silva',
            'cpf' => '123.456.789-00',
            'email' => 'joao@email.com',
            'telefone' => '(11) 99999-9999',
            'bairro' => 'Centro',
            'idade' => 35,
        ];

        return view('cidadaos.edit', compact('cidadao'));
    }

    /**
     * Atualiza dados do cidadão
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'telefone' => 'required|string|max:20',
            'bairro' => 'required|string|max:100',
            'idade' => 'nullable|integer|min:16|max:120',
        ]);

        // TODO: Implementar atualização no banco
        
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
