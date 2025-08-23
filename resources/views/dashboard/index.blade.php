@extends('layouts.crm')

@section('title', 'Dashboard - CRM Legislativo')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Métricas Principais -->
    <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="card metric-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-0">Total de Cidadãos</h6>
                        <h2 class="text-white mb-0">{{ number_format($metrics['total_cidadaos']) }}</h2>
                    </div>
                    <div class="text-white-50">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <small class="text-white-50">
                    @if($metrics['crescimento_cidadaos'] >= 0)
                        <i class="bi bi-arrow-up"></i> +{{ $metrics['crescimento_cidadaos'] }}%
                    @else
                        <i class="bi bi-arrow-down"></i> {{ $metrics['crescimento_cidadaos'] }}%
                    @endif
                    vs mês anterior
                </small>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="card metric-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-0">Demandas Abertas</h6>
                        <h2 class="text-white mb-0">{{ number_format($metrics['demandas_abertas']) }}</h2>
                    </div>
                    <div class="text-white-50">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <small class="text-white-50">
                    @if($metrics['crescimento_demandas'] >= 0)
                        <i class="bi bi-arrow-up"></i> +{{ $metrics['crescimento_demandas'] }}%
                    @else
                        <i class="bi bi-arrow-down"></i> {{ $metrics['crescimento_demandas'] }}%
                    @endif
                    vs mês anterior
                </small>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="card metric-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-0">Demandas Resolvidas</h6>
                        <h2 class="text-white mb-0">{{ number_format($metrics['demandas_resolvidas']) }}</h2>
                    </div>
                    <div class="text-white-50">
                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <small class="text-white-50">
                    Taxa de resolução: {{ $metrics['demandas_resolvidas'] > 0 ? round(($metrics['demandas_resolvidas'] / ($metrics['demandas_resolvidas'] + $metrics['demandas_abertas'])) * 100, 1) : 0 }}%
                </small>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3 mb-4">
        <div class="card metric-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-0">Interações este Mês</h6>
                        <h2 class="text-white mb-0">{{ number_format($metrics['interacoes_mes']) }}</h2>
                    </div>
                    <div class="text-white-50">
                        <i class="bi bi-chat-dots" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <small class="text-white-50">
                    @if($metrics['crescimento_interacoes'] >= 0)
                        <i class="bi bi-arrow-up"></i> +{{ $metrics['crescimento_interacoes'] }}%
                    @else
                        <i class="bi bi-arrow-down"></i> {{ $metrics['crescimento_interacoes'] }}%
                    @endif
                    vs mês anterior
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Gráfico de Demandas por Categoria -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Demandas por Categoria
                </h5>
            </div>
            <div class="card-body">
                <canvas id="demandasCategoriaChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Cidadãos por Bairro -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-map me-2"></i>
                    Cidadãos por Bairro
                </h5>
            </div>
            <div class="card-body">
                <canvas id="cidadaosBairroChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pipeline de Relacionamento -->
    <div class="col-12 col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel me-2"></i>
                    Pipeline de Relacionamento
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-1">Leads</h6>
                            <h4 class="text-primary mb-0">{{ $graficos['pipeline']['leads'] }}</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-1">Engajados</h6>
                            <h4 class="text-info mb-0">{{ $graficos['pipeline']['engajados'] }}</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-1">Ativos</h6>
                            <h4 class="text-warning mb-0">{{ $graficos['pipeline']['ativos'] }}</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-1">Apoiadores</h6>
                            <h4 class="text-success mb-0">{{ $graficos['pipeline']['apoiadores'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('cidadaos.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>
                        Cadastrar Cidadão
                    </a>
                    <a href="{{ route('demandas.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nova Demanda
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i>
                        Importar Planilha
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Atividade Recente -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Atividade Recente
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($graficos['atividade_recente'] as $atividade)
                        <div class="list-group-item d-flex justify-content-between align-items-start border-0">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">{{ $atividade['descricao'] }}</div>
                                <small class="text-muted">{{ $atividade['detalhes'] }}</small>
                            </div>
                            <small class="text-muted">{{ $atividade['created_at']->diffForHumans() }}</small>
                        </div>
                    @empty
                        <div class="list-group-item border-0">
                            <p class="text-muted mb-0">Nenhuma atividade recente encontrada.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Importação -->
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
                        <div class="form-text">
                            Formato esperado: Nome, CPF, Email, Telefone, Bairro, Idade
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados do PHP para JavaScript
    const demandasCategoria = @json($graficos['demandas_categoria']);
    const cidadaosBairro = @json($graficos['cidadaos_bairro']);

    // Gráfico de Demandas por Categoria
    const ctxCategoria = document.getElementById('demandasCategoriaChart').getContext('2d');

    if (Object.keys(demandasCategoria).length > 0) {
        new Chart(ctxCategoria, {
            type: 'doughnut',
            data: {
                labels: Object.keys(demandasCategoria).map(cat => {
                    // Formatação de categorias
                    const formatacao = {
                        'infraestrutura': 'Infraestrutura',
                        'saude': 'Saúde',
                        'educacao': 'Educação',
                        'seguranca': 'Segurança',
                        'meio_ambiente': 'Meio Ambiente',
                        'assistencia_social': 'Assistência Social',
                        'transporte': 'Transporte',
                        'habitacao': 'Habitação',
                        'cultura': 'Cultura',
                        'esporte': 'Esporte',
                        'outros': 'Outros'
                    };
                    return formatacao[cat] || cat;
                }),
                datasets: [{
                    data: Object.values(demandasCategoria),
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#4facfe',
                        '#43e97b',
                        '#fa709a',
                        '#fee140',
                        '#24fe41',
                        '#9354eb',
                        '#ec8cff'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } else {
        // Exibir mensagem quando não há dados
        ctxCategoria.fillText('Nenhuma demanda cadastrada', 150, 150);
    }

    // Gráfico de Cidadãos por Bairro
    const ctxBairro = document.getElementById('cidadaosBairroChart').getContext('2d');

    if (Object.keys(cidadaosBairro).length > 0) {
        new Chart(ctxBairro, {
            type: 'bar',
            data: {
                labels: Object.keys(cidadaosBairro),
                datasets: [{
                    label: 'Cidadãos',
                    data: Object.values(cidadaosBairro),
                    backgroundColor: '#667eea'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } else {
        // Exibir mensagem quando não há dados
        ctxBairro.fillText('Nenhum cidadão cadastrado', 150, 150);
    }

    // Carregar métricas adicionais via AJAX
    loadAdditionalMetrics();
});

function loadAdditionalMetrics() {
    axios.get('{{ route("dashboard.metrics") }}')
        .then(function (response) {
            // Atualizar elementos com métricas adicionais se necessário
            console.log('Métricas adicionais carregadas:', response.data);
        })
        .catch(function (error) {
            console.error('Erro ao carregar métricas:', error);
        });
}
</script>
@endsection
