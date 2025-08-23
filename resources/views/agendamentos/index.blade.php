@extends('layouts.crm')

@section('title', 'Agendamentos - CRM Legislativo')
@section('page-title', 'Follow-up e Agendamentos')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">
                <i class="bi bi-calendar-event me-2"></i>
                Sistema de Follow-up
            </h2>
            <div>
                <a href="{{ route('agendamentos.dashboard') }}" class="btn btn-outline-info me-2">
                    <i class="bi bi-graph-up me-1"></i>
                    Dashboard
                </a>
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#agendamentoLoteModal">
                    <i class="bi bi-send-plus me-1"></i>
                    Agendamento em Lote
                </button>
                <a href="{{ route('agendamentos.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i>
                    Novo Agendamento
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
                <h5 class="text-warning mb-1">{{ $estatisticas['agendados'] }}</h5>
                <small class="text-muted">Agendados</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-success mb-1">{{ $estatisticas['enviados'] }}</h5>
                <small class="text-muted">Enviados</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-danger mb-1">{{ $estatisticas['falhados'] }}</h5>
                <small class="text-muted">Falhados</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h5 class="text-warning mb-1">{{ $estatisticas['atrasados'] }}</h5>
                <small class="text-muted">Atrasados</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('agendamentos.index') }}" class="row g-3">
            <div class="col-12 col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="agendado" {{ $filtros['status'] == 'agendado' ? 'selected' : '' }}>Agendado</option>
                    <option value="enviado" {{ $filtros['status'] == 'enviado' ? 'selected' : '' }}>Enviado</option>
                    <option value="falhado" {{ $filtros['status'] == 'falhado' ? 'selected' : '' }}>Falhado</option>
                    <option value="cancelado" {{ $filtros['status'] == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
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
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label for="tipo" class="form-label">Tipo</label>
                <select class="form-select" id="tipo" name="tipo">
                    <option value="">Todos</option>
                    <option value="follow_up" {{ $filtros['tipo'] == 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                    <option value="lembrete" {{ $filtros['tipo'] == 'lembrete' ? 'selected' : '' }}>Lembrete</option>
                    <option value="convite" {{ $filtros['tipo'] == 'convite' ? 'selected' : '' }}>Convite</option>
                    <option value="campanha" {{ $filtros['tipo'] == 'campanha' ? 'selected' : '' }}>Campanha</option>
                </select>
            </div>
            
            <div class="col-12 col-md-2">
                <label for="data_inicio" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ $filtros['data_inicio'] }}">
            </div>
            
            <div class="col-12 col-md-2">
                <label for="data_fim" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ $filtros['data_fim'] }}">
            </div>
            
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-funnel"></i>
                    Filtrar
                </button>
                <a href="{{ route('agendamentos.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Agendamentos -->
<div class="card">
    <div class="card-body">
        @if($agendamentos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Cidadão</th>
                            <th>Título</th>
                            <th>Canal</th>
                            <th>Data Agendamento</th>
                            <th>Status</th>
                            <th>Prioridade</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendamentos as $agendamento)
                        <tr class="{{ $agendamento->atrasado ? 'table-warning' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ substr($agendamento->cidadao->nome, 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $agendamento->cidadao->nome }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $agendamento->cidadao->bairro }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $agendamento->titulo }}</strong>
                                    <br>
                                    <span class="badge bg-light text-dark">
                                        <i class="{{ $agendamento->tipo_icone }} me-1"></i>
                                        {{ ucfirst($agendamento->tipo) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <i class="bi {{ $agendamento->canal_icone }} me-1"></i>
                                {{ ucfirst($agendamento->canal) }}
                            </td>
                            <td>
                                {{ $agendamento->data_agendamento->format('d/m/Y H:i') }}
                                @if($agendamento->atrasado)
                                    <br><small class="text-danger">Atrasado</small>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $agendamento->status_classe }}">
                                    {{ ucfirst($agendamento->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $agendamento->prioridade_cor }}">
                                    {{ ucfirst($agendamento->prioridade) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('agendamentos.show', $agendamento) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if($agendamento->status === 'agendado')
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="enviarManual({{ $agendamento->id }})" title="Enviar Agora">
                                            <i class="bi bi-send"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" data-bs-target="#reagendarModal"
                                                data-agendamento-id="{{ $agendamento->id }}" title="Reagendar">
                                            <i class="bi bi-calendar-plus"></i>
                                        </button>
                                    @endif
                                    
                                    @if($agendamento->pode_reenviar)
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="reenviar({{ $agendamento->id }})" title="Reenviar">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Exibindo {{ $agendamentos->firstItem() }} a {{ $agendamentos->lastItem() }} 
                    de {{ $agendamentos->total() }} agendamentos
                </div>
                {{ $agendamentos->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhum agendamento encontrado</h5>
                <p class="text-muted">Crie seu primeiro agendamento de follow-up.</p>
                <a href="{{ route('agendamentos.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i>
                    Criar Agendamento
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal Reagendar -->
<div class="modal fade" id="reagendarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reagendar Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formReagendar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nova_data" class="form-label">Nova Data e Hora</label>
                        <input type="datetime-local" class="form-control" id="nova_data" name="nova_data" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo do Reagendamento</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Reagendar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Agendamento em Lote -->
<div class="modal fade" id="agendamentoLoteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agendamento em Lote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('agendamentos.lote') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="template_id_lote" class="form-label">Template</label>
                            <select class="form-select" id="template_id_lote" name="template_id" required>
                                <option value="">Selecione um template</option>
                                <!-- Preenchido via JavaScript -->
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="canal_lote" class="form-label">Canal</label>
                            <select class="form-select" id="canal_lote" name="canal" required>
                                <option value="">Selecione o canal</option>
                                <option value="email">E-mail</option>
                                <option value="sms">SMS</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="push">Push</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="data_agendamento_lote" class="form-label">Data e Hora</label>
                            <input type="datetime-local" class="form-control" id="data_agendamento_lote" name="data_agendamento" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="tipo_lote" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo_lote" name="tipo" required>
                                <option value="follow_up">Follow-up</option>
                                <option value="lembrete">Lembrete</option>
                                <option value="convite">Convite</option>
                                <option value="campanha">Campanha</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Selecionar Cidadãos</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <!-- Lista de cidadãos seria carregada aqui -->
                            <p class="text-muted">Carregando cidadãos...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Agendar em Lote</button>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal de reagendamento
    const reagendarModal = document.getElementById('reagendarModal');
    reagendarModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const agendamentoId = button.getAttribute('data-agendamento-id');
        const form = document.getElementById('formReagendar');
        form.action = `/agendamentos/${agendamentoId}/reagendar`;
    });

    // Configurar data mínima para reagendamento
    const novaDataInput = document.getElementById('nova_data');
    if (novaDataInput) {
        const agora = new Date();
        agora.setMinutes(agora.getMinutes() - agora.getTimezoneOffset());
        novaDataInput.min = agora.toISOString().slice(0, 16);
    }

    // Configurar data mínima para agendamento em lote
    const dataLoteInput = document.getElementById('data_agendamento_lote');
    if (dataLoteInput) {
        const agora = new Date();
        agora.setMinutes(agora.getMinutes() - agora.getTimezoneOffset());
        dataLoteInput.min = agora.toISOString().slice(0, 16);
    }
});

function enviarManual(agendamentoId) {
    if (confirm('Tem certeza que deseja enviar este agendamento agora?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/agendamentos/${agendamentoId}/enviar`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function reenviar(agendamentoId) {
    if (confirm('Tem certeza que deseja reenviar este agendamento?')) {
        // Implementar lógica de reenvio
        console.log('Reenviar agendamento:', agendamentoId);
    }
}
</script>
@endsection
