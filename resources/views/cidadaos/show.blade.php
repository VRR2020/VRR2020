@extends('layouts.crm')

@section('title', 'Perfil do Cidadão - CRM Legislativo')
@section('page-title', 'Perfil do Cidadão')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="avatar-large me-3">
                    {{ substr($cidadao->nome, 0, 1) }}
                </div>
                <div>
                    <h2 class="h4 mb-1">{{ $cidadao->nome }}</h2>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $cidadao->status == 'ativo' ? 'success' : ($cidadao->status == 'lead' ? 'warning' : 'secondary') }} me-2">
                            {{ ucfirst($cidadao->status) }}
                        </span>
                        <small class="text-muted">Cadastrado em {{ $cidadao->created_at->format('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('cidadaos.edit', $cidadao->id) }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-pencil me-1"></i>
                    Editar
                </a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#novaInteracaoModal">
                    <i class="bi bi-chat-plus me-1"></i>
                    Nova Interação
                </button>
                <a href="{{ route('cidadaos.index') }}" class="btn btn-outline-secondary ms-2">
                    <i class="bi bi-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informações Principais -->
    <div class="col-12 col-lg-8 mb-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-vcard me-2"></i>
                    Informações Pessoais
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label text-muted">CPF</label>
                        <p class="mb-0">{{ $cidadao->cpf }}</p>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label text-muted">Email</label>
                        <p class="mb-0">
                            <a href="mailto:{{ $cidadao->email }}" class="text-decoration-none">
                                {{ $cidadao->email }}
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label text-muted">Telefone</label>
                        <p class="mb-0">
                            <a href="tel:{{ $cidadao->telefone }}" class="text-decoration-none">
                                {{ $cidadao->telefone }}
                            </a>
                        </p>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label text-muted">Bairro</label>
                        <p class="mb-0">{{ $cidadao->bairro }}</p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label text-muted">Idade</label>
                        <p class="mb-0">{{ $cidadao->idade ?? 'Não informado' }}</p>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label text-muted">Nível de Engajamento</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-{{ $cidadao->nivel_engajamento == 'alto' ? 'success' : ($cidadao->nivel_engajamento == 'medio' ? 'warning' : 'secondary') }}" 
                                 style="width: {{ $cidadao->nivel_engajamento == 'alto' ? '100' : ($cidadao->nivel_engajamento == 'medio' ? '60' : '30') }}%">
                                {{ ucfirst($cidadao->nivel_engajamento) }}
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($cidadao->tags)
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Tags</label>
                        <div>
                            @foreach($cidadao->tags as $tag)
                                <span class="badge bg-primary me-1">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Interesses Políticos -->
        @if($cidadao->interesses_politicos)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-heart me-2"></i>
                    Interesses Políticos
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($cidadao->interesses_politicos as $interesse)
                        <div class="col-12 col-md-6 col-lg-4 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <span>{{ ucfirst(str_replace('_', ' ', $interesse)) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <!-- Histórico de Interações -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Histórico de Interações
                </h5>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#novaInteracaoModal">
                    <i class="bi bi-plus"></i>
                    Nova
                </button>
            </div>
            <div class="card-body">
                <!-- Timeline de Interações -->
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">
                                <i class="bi bi-person-plus me-1"></i>
                                Cidadão cadastrado
                            </h6>
                            <p class="timeline-description">
                                Primeiro cadastro no sistema
                            </p>
                            <small class="text-muted">{{ $cidadao->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    
                    <!-- Exemplo de outras interações -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">
                                <i class="bi bi-telephone me-1"></i>
                                Ligação telefônica
                            </h6>
                            <p class="timeline-description">
                                Contato para acompanhamento de demanda sobre iluminação pública
                            </p>
                            <small class="text-muted">Exemplo - 15/12/2023 14:30</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">
                                <i class="bi bi-envelope me-1"></i>
                                E-mail enviado
                            </h6>
                            <p class="timeline-description">
                                Newsletter mensal do gabinete
                            </p>
                            <small class="text-muted">Exemplo - 01/12/2023 09:00</small>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-down me-1"></i>
                        Carregar Mais
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-12 col-lg-4">
        <!-- Métricas Rápidas -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Métricas
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-primary mb-0">0</h4>
                        <small class="text-muted">Demandas</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-0">0</h4>
                        <small class="text-muted">Interações</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-warning mb-0">0</h4>
                        <small class="text-muted">Em Aberto</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info mb-0">0</h4>
                        <small class="text-muted">Resolvidas</small>
                    </div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">Score de Engajamento</small>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-gradient" style="width: 65%">65</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Redes Sociais -->
        @if($cidadao->redes_sociais)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-share me-2"></i>
                    Redes Sociais
                </h5>
            </div>
            <div class="card-body">
                @if(isset($cidadao->redes_sociais['facebook']))
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-facebook text-primary me-2"></i>
                    <a href="{{ $cidadao->redes_sociais['facebook'] }}" target="_blank" class="text-decoration-none">
                        Facebook
                    </a>
                </div>
                @endif
                
                @if(isset($cidadao->redes_sociais['instagram']))
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-instagram text-danger me-2"></i>
                    <a href="{{ $cidadao->redes_sociais['instagram'] }}" target="_blank" class="text-decoration-none">
                        Instagram
                    </a>
                </div>
                @endif
                
                @if(isset($cidadao->redes_sociais['whatsapp']))
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-whatsapp text-success me-2"></i>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cidadao->redes_sociais['whatsapp']) }}" 
                       target="_blank" class="text-decoration-none">
                        WhatsApp
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Observações -->
        @if($cidadao->observacoes)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-chat-square-text me-2"></i>
                    Observações
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $cidadao->observacoes }}</p>
            </div>
        </div>
        @endif
        
        <!-- Ações Rápidas -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#novaDemandaModal">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nova Demanda
                    </button>
                    <button type="button" class="btn btn-outline-success">
                        <i class="bi bi-calendar-plus me-1"></i>
                        Agendar Reunião
                    </button>
                    <button type="button" class="btn btn-outline-info">
                        <i class="bi bi-envelope me-1"></i>
                        Enviar E-mail
                    </button>
                    <button type="button" class="btn btn-outline-warning">
                        <i class="bi bi-whatsapp me-1"></i>
                        Enviar WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nova Interação -->
<div class="modal fade" id="novaInteracaoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Interação com {{ $cidadao->nome }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('interacoes.store', $cidadao->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Interação</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="reuniao">Reunião</option>
                                <option value="telefonema">Telefonema</option>
                                <option value="email">E-mail</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="evento">Evento</option>
                                <option value="visita">Visita</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="canal" class="form-label">Canal</label>
                            <select class="form-select" id="canal" name="canal" required>
                                <option value="">Selecione...</option>
                                <option value="presencial">Presencial</option>
                                <option value="telefonico">Telefônico</option>
                                <option value="email">E-mail</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="site">Site</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assunto" class="form-label">Assunto</label>
                        <input type="text" class="form-control" id="assunto" name="assunto" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="4" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="sentido" class="form-label">Sentido</label>
                            <select class="form-select" id="sentido" name="sentido" required>
                                <option value="entrada">Entrada (Cidadão contatou)</option>
                                <option value="saida">Saída (Gabinete contatou)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="resultado" class="form-label">Resultado</label>
                            <select class="form-select" id="resultado" name="resultado">
                                <option value="">Selecione...</option>
                                <option value="positivo">Positivo</option>
                                <option value="neutro">Neutro</option>
                                <option value="negativo">Negativo</option>
                                <option value="nao_atendeu">Não Atendeu</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Interação</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 2rem;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline:before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 0.95rem;
}

.timeline-description {
    margin-bottom: 5px;
    font-size: 0.9rem;
    color: #6c757d;
}
</style>
@endsection
