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
                    <i class="bi bi-arrow-down"></i> -5% vs mês anterior
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
                    <i class="bi bi-arrow-up"></i> +18% vs mês anterior
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
                    <i class="bi bi-arrow-up"></i> +25% vs mês anterior
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
                            <h4 class="text-primary mb-0">150</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-1">Engajados</h6>
                            <h4 class="text-info mb-0">89</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-1">Ativos</h6>
                            <h4 class="text-warning mb-0">45</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-1">Apoiadores</h6>
                            <h4 class="text-success mb-0">23</h4>
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
                    <div class="list-group-item d-flex justify-content-between align-items-start border-0">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">João Silva cadastrou nova demanda</div>
                            <small class="text-muted">Solicitação de reparo na Rua das Flores</small>
                        </div>
                        <small class="text-muted">2 min atrás</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-start border-0">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Maria Santos foi cadastrada</div>
                            <small class="text-muted">Nova cidadã no bairro Centro</small>
                        </div>
                        <small class="text-muted">15 min atrás</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-start border-0">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Demanda #123 foi resolvida</div>
                            <small class="text-muted">Limpeza da praça concluída</small>
                        </div>
                        <small class="text-muted">1 hora atrás</small>
                    </div>
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
    // Gráfico de Demandas por Categoria
    const ctxCategoria = document.getElementById('demandasCategoriaChart').getContext('2d');
    new Chart(ctxCategoria, {
        type: 'doughnut',
        data: {
            labels: ['Infraestrutura', 'Saúde', 'Educação', 'Segurança', 'Meio Ambiente'],
            datasets: [{
                data: [30, 25, 20, 15, 10],
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb',
                    '#4facfe',
                    '#43e97b'
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

    // Gráfico de Cidadãos por Bairro
    const ctxBairro = document.getElementById('cidadaosBairroChart').getContext('2d');
    new Chart(ctxBairro, {
        type: 'bar',
        data: {
            labels: ['Centro', 'Vila Nova', 'Jardim das Flores', 'Bela Vista', 'São João'],
            datasets: [{
                label: 'Cidadãos',
                data: [45, 38, 32, 28, 25],
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
});
</script>
@endsection
