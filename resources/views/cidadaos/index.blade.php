@extends('layouts.crm')

@section('title', 'Cidadãos - CRM Legislativo')
@section('page-title', 'Gestão de Cidadãos')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">
                <i class="bi bi-people me-2"></i>
                Cidadãos Cadastrados
            </h2>
            <div>
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-file-earmark-arrow-up me-1"></i>
                    Importar CSV
                </button>
                <a href="{{ route('cidadaos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Novo Cidadão
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('cidadaos.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label for="busca" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="busca" name="busca" 
                       value="{{ $filtros['busca'] }}" placeholder="Nome, CPF, email...">
            </div>
            
            <div class="col-12 col-md-3">
                <label for="bairro" class="form-label">Bairro</label>
                <select class="form-select" id="bairro" name="bairro">
                    <option value="">Todos os bairros</option>
                    <option value="Centro" {{ $filtros['bairro'] == 'Centro' ? 'selected' : '' }}>Centro</option>
                    <option value="Vila Nova" {{ $filtros['bairro'] == 'Vila Nova' ? 'selected' : '' }}>Vila Nova</option>
                    <option value="Jardim das Flores" {{ $filtros['bairro'] == 'Jardim das Flores' ? 'selected' : '' }}>Jardim das Flores</option>
                    <option value="Bela Vista" {{ $filtros['bairro'] == 'Bela Vista' ? 'selected' : '' }}>Bela Vista</option>
                    <option value="São João" {{ $filtros['bairro'] == 'São João' ? 'selected' : '' }}>São João</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="lead" {{ $filtros['status'] == 'lead' ? 'selected' : '' }}>Lead</option>
                    <option value="engajado" {{ $filtros['status'] == 'engajado' ? 'selected' : '' }}>Engajado</option>
                    <option value="ativo" {{ $filtros['status'] == 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="apoiador" {{ $filtros['status'] == 'apoiador' ? 'selected' : '' }}>Apoiador</option>
                    <option value="inativo" {{ $filtros['status'] == 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label for="tags" class="form-label">Tags</label>
                <input type="text" class="form-control" id="tags" name="tags" 
                       value="{{ $filtros['tags'] }}" placeholder="educação, saúde...">
            </div>
            
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-funnel"></i>
                    Filtrar
                </button>
                <a href="{{ route('cidadaos.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-primary mb-1">0</h5>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-success mb-1">0</h5>
                <small class="text-muted">Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-warning mb-1">0</h5>
                <small class="text-muted">Leads</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-info mb-1">0</h5>
                <small class="text-muted">Este Mês</small>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Cidadãos -->
<div class="card">
    <div class="card-body">
        @if($cidadaos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Bairro</th>
                            <th>Status</th>
                            <th>Engajamento</th>
                            <th>Cadastro</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cidadaos as $cidadao)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ substr($cidadao->nome, 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $cidadao->nome }}</strong>
                                        @if($cidadao->tags)
                                            <br>
                                            @foreach($cidadao->tags as $tag)
                                                <span class="badge bg-light text-dark">{{ $tag }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $cidadao->email }}</td>
                            <td>{{ $cidadao->telefone_formatado }}</td>
                            <td>{{ $cidadao->bairro }}</td>
                            <td>
                                <span class="badge bg-{{ $cidadao->status == 'ativo' ? 'success' : ($cidadao->status == 'lead' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($cidadao->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $cidadao->nivel_engajamento == 'alto' ? 'success' : ($cidadao->nivel_engajamento == 'medio' ? 'warning' : 'secondary') }}" 
                                         style="width: {{ $cidadao->nivel_engajamento == 'alto' ? '100' : ($cidadao->nivel_engajamento == 'medio' ? '60' : '30') }}%">
                                        {{ ucfirst($cidadao->nivel_engajamento) }}
                                    </div>
                                </div>
                            </td>
                            <td>{{ $cidadao->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('cidadaos.show', $cidadao->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Perfil">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('cidadaos.edit', $cidadao->id) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-success" title="Nova Interação">
                                        <i class="bi bi-chat-plus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhum cidadão encontrado</h5>
                <p class="text-muted">Comece cadastrando o primeiro cidadão ou importe uma planilha.</p>
                <a href="{{ route('cidadaos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Cadastrar Primeiro Cidadão
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Importaç��o -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importar Planilha de Cidadãos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cidadaos.importar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="arquivo" class="form-label">Selecionar Arquivo CSV</label>
                        <input type="file" class="form-control" id="arquivo" name="arquivo" accept=".csv" required>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Formato da Planilha</h6>
                        <p class="mb-2">A planilha deve conter as seguintes colunas (nesta ordem):</p>
                        <ul class="mb-0">
                            <li>Nome (obrigatório)</li>
                            <li>CPF (obrigatório)</li>
                            <li>Email (obrigatório)</li>
                            <li>Telefone (obrigatório)</li>
                            <li>Bairro (obrigatório)</li>
                            <li>Idade (opcional)</li>
                            <li>Endereço (opcional)</li>
                            <li>Profissão (opcional)</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <a href="#" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-download me-1"></i>
                            Baixar Modelo de Planilha
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>
                        Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
}
</style>
@endsection
