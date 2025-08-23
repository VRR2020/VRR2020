@extends('layouts.crm')

@section('title', 'Segmentação Inteligente - CRM Legislativo')
@section('page-title', 'Segmentação de Cidadãos')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">
                <i class="bi bi-funnel me-2"></i>
                Segmentação Inteligente
            </h2>
            <div>
                <a href="{{ route('segmentacao.sugestoes') }}" class="btn btn-outline-info me-2">
                    <i class="bi bi-lightbulb me-1"></i>
                    Sugestões IA
                </a>
                <a href="{{ route('segmentacao.analise-comportamento') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-graph-up-arrow me-1"></i>
                    Análise Comportamental
                </a>
                <a href="{{ route('segmentacao.construtor') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Construtor Avançado
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Gerais -->
<div class="row mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-primary mb-1">{{ number_format($estatisticas['total_cidadaos']) }}</h5>
                <small class="text-muted">Total de Cidadãos</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-success mb-1">{{ $estatisticas['total_bairros'] }}</h5>
                <small class="text-muted">Bairros Diferentes</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-warning mb-1">{{ $estatisticas['total_tags'] }}</h5>
                <small class="text-muted">Tags Únicas</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-info mb-1">{{ $estatisticas['segmentos_ativos'] }}</h5>
                <small class="text-muted">Segmentos Pré-definidos</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtro Rápido -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-lightning me-2"></i>
            Filtro Rápido
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('segmentacao.processar') }}" method="POST" id="filtroRapido">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <label for="status_rapido" class="form-label">Status</label>
                    <select class="form-select" id="status_rapido" name="filtros[0][campo]" onchange="setFiltroRapido(0, 'status', this.value)">
                        <option value="">Todos</option>
                        <option value="lead">Lead</option>
                        <option value="engajado">Engajado</option>
                        <option value="ativo">Ativo</option>
                        <option value="apoiador">Apoiador</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-3">
                    <label for="bairro_rapido" class="form-label">Bairro</label>
                    <select class="form-select" id="bairro_rapido" name="filtros[1][campo]" onchange="setFiltroRapido(1, 'bairro', this.value)">
                        <option value="">Todos</option>
                        <option value="Centro">Centro</option>
                        <option value="Vila Nova">Vila Nova</option>
                        <option value="Jardim das Flores">Jardim das Flores</option>
                        <option value="Bela Vista">Bela Vista</option>
                        <option value="São João">São João</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-3">
                    <label for="engajamento_rapido" class="form-label">Engajamento</label>
                    <select class="form-select" id="engajamento_rapido" name="filtros[2][campo]" onchange="setFiltroRapido(2, 'nivel_engajamento', this.value)">
                        <option value="">Todos</option>
                        <option value="baixo">Baixo</option>
                        <option value="medio">Médio</option>
                        <option value="alto">Alto</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>
                        Filtrar
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="limparFiltros()">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
            
            <!-- Campos ocultos para o filtro -->
            <input type="hidden" name="filtros[0][operador]" value="=">
            <input type="hidden" name="filtros[0][valor]" id="status_valor">
            <input type="hidden" name="filtros[1][operador]" value="=">
            <input type="hidden" name="filtros[1][valor]" id="bairro_valor">
            <input type="hidden" name="filtros[2][operador]" value="=">
            <input type="hidden" name="filtros[2][valor]" id="engajamento_valor">
        </form>
    </div>
</div>

<!-- Segmentos Pré-definidos -->
<div class="row">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="bi bi-collection me-2"></i>
            Segmentos Pré-definidos
        </h5>
    </div>
    
    @foreach($segmentosPredefinidos as $key => $segmento)
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card h-100 segmento-card" data-segmento="{{ $key }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="card-title mb-0">{{ $segmento['nome'] }}</h6>
                        <span class="badge bg-primary">{{ number_format($segmento['total']) }}</span>
                    </div>
                    
                    <p class="card-text text-muted small">{{ $segmento['descricao'] }}</p>
                    
                    <div class="mb-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" 
                                 style="width: {{ min(($segmento['total'] / $estatisticas['total_cidadaos']) * 100, 100) }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ number_format(($segmento['total'] / $estatisticas['total_cidadaos']) * 100, 1) }}% do total
                        </small>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="btn-group w-100">
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="aplicarSegmento('{{ $key }}')">
                            <i class="bi bi-eye me-1"></i>
                            Visualizar
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" 
                                onclick="criarCampanha('{{ $key }}')">
                            <i class="bi bi-send me-1"></i>
                            Campanha
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" 
                                onclick="exportarSegmento('{{ $key }}')">
                            <i class="bi bi-download me-1"></i>
                            Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Ações em Lote -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <button type="button" class="btn btn-outline-primary w-100" onclick="abrirConstrutorAvancado()">
                            <i class="bi bi-sliders me-2"></i>
                            Construtor Avançado
                        </button>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="button" class="btn btn-outline-success w-100" onclick="criarCampanhaPersonalizada()">
                            <i class="bi bi-megaphone me-2"></i>
                            Campanha Personalizada
                        </button>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="button" class="btn btn-outline-info w-100" onclick="analisarTendencias()">
                            <i class="bi bi-graph-up me-2"></i>
                            Análise de Tendências
                        </button>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="button" class="btn btn-outline-warning w-100" onclick="importarSegmento()">
                            <i class="bi bi-upload me-2"></i>
                            Importar Segmento
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Insights Automatizados -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-robot me-2"></i>
                    Insights Automatizados
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Oportunidade Detectada</h6>
                            <p class="mb-2">15% dos seus apoiadores não receberam contato nos últimos 30 dias.</p>
                            <button class="btn btn-sm btn-outline-info">Criar Campanha de Reengajamento</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Atenção Necessária</h6>
                            <p class="mb-2">Bairro Centro tem 23 demandas em aberto há mais de 15 dias.</p>
                            <button class="btn btn-sm btn-outline-warning">Visualizar Demandas</button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle me-2"></i>Sucesso Identificado</h6>
                            <p class="mb-2">Vila Nova teve aumento de 40% no engajamento este mês.</p>
                            <button class="btn btn-sm btn-outline-success">Replicar Estratégia</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="alert alert-primary">
                            <h6><i class="bi bi-lightbulb me-2"></i>Sugestão Inteligente</h6>
                            <p class="mb-2">Jovens de 18-25 anos respondem melhor via WhatsApp.</p>
                            <button class="btn btn-sm btn-outline-primary">Ajustar Canais</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.segmento-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.segmento-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.progress {
    background-color: #f8f9fa;
}

.alert {
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #0dcaf0;
}

.alert-warning {
    border-left-color: #ffc107;
}

.alert-success {
    border-left-color: #198754;
}

.alert-primary {
    border-left-color: #0d6efd;
}
</style>
@endsection

@section('scripts')
<script>
// Configurar filtro rápido
function setFiltroRapido(index, campo, valor) {
    document.querySelector(`input[name="filtros[${index}][campo]"]`).value = campo;
    document.getElementById(`${campo.split('_')[0] || campo}_valor`).value = valor;
}

function limparFiltros() {
    document.getElementById('filtroRapido').reset();
    document.querySelectorAll('input[type="hidden"]').forEach(input => {
        if (input.name.includes('[valor]')) {
            input.value = '';
        }
    });
}

// Ações dos segmentos
function aplicarSegmento(segmento) {
    // Buscar configuração do segmento e aplicar filtros
    const segmentos = @json($segmentosPredefinidos);
    const config = segmentos[segmento];
    
    if (config && config.filtros) {
        // Criar formulário dinâmico com os filtros do segmento
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("segmentacao.processar") }}';
        
        // CSRF Token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        // Adicionar filtros
        config.filtros.forEach((filtro, index) => {
            ['campo', 'operador', 'valor'].forEach(prop => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `filtros[${index}][${prop}]`;
                input.value = Array.isArray(filtro[prop]) ? filtro[prop].join(',') : filtro[prop];
                form.appendChild(input);
            });
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function criarCampanha(segmento) {
    // Redirecionar para criação de campanha com segmento pré-selecionado
    window.location.href = `{{ route('agendamentos.create') }}?segmento=${segmento}`;
}

function exportarSegmento(segmento) {
    // Implementar exportação
    alert(`Exportar segmento: ${segmento}`);
}

// Ações rápidas
function abrirConstrutorAvancado() {
    window.location.href = '{{ route("segmentacao.construtor") }}';
}

function criarCampanhaPersonalizada() {
    window.location.href = '{{ route("agendamentos.create") }}';
}

function analisarTendencias() {
    window.location.href = '{{ route("segmentacao.analise-comportamento") }}';
}

function importarSegmento() {
    alert('Funcionalidade de importação será implementada em breve.');
}

// Clique nos cards dos segmentos
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.segmento-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Não aplicar se clicou em um botão
            if (e.target.closest('button') || e.target.closest('.btn-group')) {
                return;
            }
            
            const segmento = this.getAttribute('data-segmento');
            aplicarSegmento(segmento);
        });
    });
});
</script>
@endsection
