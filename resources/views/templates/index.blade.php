@extends('layouts.crm')

@section('title', 'Templates - CRM Legislativo')
@section('page-title', 'Templates de Mensagem')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">
                <i class="bi bi-file-text me-2"></i>
                Templates de Mensagem
            </h2>
            <div>
                <a href="{{ route('templates.biblioteca') }}" class="btn btn-outline-info me-2">
                    <i class="bi bi-collection me-1"></i>
                    Biblioteca
                </a>
                <a href="{{ route('templates.dashboard') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-graph-up me-1"></i>
                    Dashboard
                </a>
                <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#importarModal">
                    <i class="bi bi-upload me-1"></i>
                    Importar
                </button>
                <a href="{{ route('templates.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i>
                    Novo Template
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-primary mb-1">{{ $estatisticas['total'] }}</h5>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-success mb-1">{{ $estatisticas['ativos'] }}</h5>
                <small class="text-muted">Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-secondary mb-1">{{ $estatisticas['inativos'] }}</h5>
                <small class="text-muted">Inativos</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-warning mb-1">{{ $estatisticas['mais_usado']->uso_contador ?? 0 }}</h5>
                <small class="text-muted">Mais Usado</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('templates.index') }}" class="row g-3">
            <div class="col-12 col-md-2">
                <label for="tipo" class="form-label">Tipo</label>
                <select class="form-select" id="tipo" name="tipo">
                    <option value="">Todos</option>
                    <option value="welcome" {{ $filtros['tipo'] == 'welcome' ? 'selected' : '' }}>Boas-vindas</option>
                    <option value="follow_up" {{ $filtros['tipo'] == 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                    <option value="lembrete" {{ $filtros['tipo'] == 'lembrete' ? 'selected' : '' }}>Lembrete</option>
                    <option value="convite" {{ $filtros['tipo'] == 'convite' ? 'selected' : '' }}>Convite</option>
                    <option value="newsletter" {{ $filtros['tipo'] == 'newsletter' ? 'selected' : '' }}>Newsletter</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label for="canal" class="form-label">Canal</label>
                <select class="form-select" id="canal" name="canal">
                    <option value="">Todos</option>
                    <option value="email" {{ $filtros['canal'] == 'email' ? 'selected' : '' }}>E-mail</option>
                    <option value="sms" {{ $filtros['canal'] == 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="whatsapp" {{ $filtros['canal'] == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="push" {{ $filtros['canal'] == 'push' ? 'selected' : '' }}>Push</option>
                    <option value="todos" {{ $filtros['canal'] == 'todos' ? 'selected' : '' }}>Todos</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label for="categoria" class="form-label">Categoria</label>
                <select class="form-select" id="categoria" name="categoria">
                    <option value="">Todas</option>
                    <option value="administrativo" {{ $filtros['categoria'] == 'administrativo' ? 'selected' : '' }}>Administrativo</option>
                    <option value="marketing" {{ $filtros['categoria'] == 'marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="suporte" {{ $filtros['categoria'] == 'suporte' ? 'selected' : '' }}>Suporte</option>
                    <option value="eventos" {{ $filtros['categoria'] == 'eventos' ? 'selected' : '' }}>Eventos</option>
                    <option value="politico" {{ $filtros['categoria'] == 'politico' ? 'selected' : '' }}>Político</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label for="ativo" class="form-label">Status</label>
                <select class="form-select" id="ativo" name="ativo">
                    <option value="">Todos</option>
                    <option value="true" {{ $filtros['ativo'] == 'true' ? 'selected' : '' }}>Ativo</option>
                    <option value="false" {{ $filtros['ativo'] == 'false' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label for="order_by" class="form-label">Ordenar por</label>
                <select class="form-select" id="order_by" name="order_by">
                    <option value="created_at" {{ $filtros['order_by'] == 'created_at' ? 'selected' : '' }}>Data Criação</option>
                    <option value="nome" {{ $filtros['order_by'] == 'nome' ? 'selected' : '' }}>Nome</option>
                    <option value="uso" {{ $filtros['order_by'] == 'uso' ? 'selected' : '' }}>Mais Usado</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-funnel"></i>
                    Filtrar
                </button>
                <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Templates -->
<div class="row">
    @forelse($templates as $template)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi {{ $template->tipo_icone }} {{ $template->canal_cor }} me-2"></i>
                        <span class="badge bg-light text-dark">{{ ucfirst($template->tipo) }}</span>
                    </div>
                    <div>
                        @if($template->ativo)
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <h6 class="card-title">{{ $template->nome }}</h6>
                    
                    @if($template->descricao)
                        <p class="card-text text-muted small">{{ Str::limit($template->descricao, 100) }}</p>
                    @endif
                    
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="bi bi-layers me-1"></i>
                            {{ ucfirst($template->categoria) }}
                        </small>
                        <br>
                        <small class="text-muted">
                            <i class="bi bi-broadcast me-1"></i>
                            {{ ucfirst($template->canal) }}
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <strong>Uso:</strong> {{ $template->uso_contador }}x
                        </small>
                        <br>
                        <small class="text-muted">
                            <strong>Criado:</strong> {{ $template->created_at->format('d/m/Y') }}
                        </small>
                    </div>
                    
                    @if($template->tags)
                        <div class="mb-3">
                            @foreach($template->tags as $tag)
                                <span class="badge bg-light text-dark me-1">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <div class="btn-group w-100">
                        <a href="{{ route('templates.show', $template) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                            Ver
                        </a>
                        <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                            Editar
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form action="{{ route('templates.toggle', $template) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-toggle-{{ $template->ativo ? 'off' : 'on' }} me-2"></i>
                                            {{ $template->ativo ? 'Desativar' : 'Ativar' }}
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('templates.clonar', $template) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-files me-2"></i>
                                            Clonar
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <a href="{{ route('templates.exportar', $template) }}" class="dropdown-item">
                                        <i class="bi bi-download me-2"></i>
                                        Exportar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('templates.destroy', $template) }}" method="POST" 
                                          onsubmit="return confirm('Tem certeza que deseja excluir este template?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i>
                                            Excluir
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-text" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">Nenhum template encontrado</h5>
                    <p class="text-muted">Crie seu primeiro template ou explore nossa biblioteca.</p>
                    <a href="{{ route('templates.create') }}" class="btn btn-success me-2">
                        <i class="bi bi-plus-circle me-1"></i>
                        Criar Template
                    </a>
                    <a href="{{ route('templates.biblioteca') }}" class="btn btn-outline-info">
                        <i class="bi bi-collection me-1"></i>
                        Ver Biblioteca
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($templates->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $templates->links() }}
    </div>
@endif

<!-- Modal Importar Template -->
<div class="modal fade" id="importarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importar Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('templates.importar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="arquivo" class="form-label">Arquivo JSON do Template</label>
                        <input type="file" class="form-control" id="arquivo" name="arquivo" accept=".json" required>
                        <div class="form-text">
                            Selecione um arquivo JSON exportado anteriormente.
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="substituir_existente" name="substituir_existente" value="1">
                        <label class="form-check-label" for="substituir_existente">
                            Substituir template existente com o mesmo nome
                        </label>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit no change dos filtros principais
    const filtros = ['tipo', 'canal', 'categoria', 'ativo', 'order_by'];
    
    filtros.forEach(filtroId => {
        const elemento = document.getElementById(filtroId);
        if (elemento) {
            elemento.addEventListener('change', function() {
                // Auto-submit do formulário
                this.closest('form').submit();
            });
        }
    });
});
</script>
@endsection
