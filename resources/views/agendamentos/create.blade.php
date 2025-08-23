@extends('layouts.crm')

@section('title', 'Novo Agendamento - CRM Legislativo')
@section('page-title', 'Criar Agendamento')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">
                <i class="bi bi-calendar-plus me-2"></i>
                Novo Agendamento de Follow-up
            </h2>
            <a href="{{ route('agendamentos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Voltar à Lista
            </a>
        </div>
    </div>
</div>

<form action="{{ route('agendamentos.store') }}" method="POST" id="formAgendamento">
    @csrf
    
    <div class="row">
        <!-- Formulário Principal -->
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Configurações do Agendamento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="cidadao_id" class="form-label">Cidadão <span class="text-danger">*</span></label>
                            <select class="form-select @error('cidadao_id') is-invalid @enderror" 
                                    id="cidadao_id" name="cidadao_id" required>
                                <option value="">Selecione o cidadão</option>
                                @foreach($cidadaos as $cidadao_option)
                                    <option value="{{ $cidadao_option->id }}" 
                                            {{ (old('cidadao_id') == $cidadao_option->id || ($cidadao && $cidadao->id == $cidadao_option->id)) ? 'selected' : '' }}>
                                        {{ $cidadao_option->nome }} - {{ $cidadao_option->bairro }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cidadao_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="canal" class="form-label">Canal <span class="text-danger">*</span></label>
                            <select class="form-select @error('canal') is-invalid @enderror" 
                                    id="canal" name="canal" required>
                                <option value="">Selecione o canal</option>
                                <option value="email" {{ old('canal') == 'email' ? 'selected' : '' }}>
                                    <i class="bi bi-envelope"></i> E-mail
                                </option>
                                <option value="sms" {{ old('canal') == 'sms' ? 'selected' : '' }}>
                                    <i class="bi bi-chat-text"></i> SMS
                                </option>
                                <option value="whatsapp" {{ old('canal') == 'whatsapp' ? 'selected' : '' }}>
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </option>
                                <option value="push" {{ old('canal') == 'push' ? 'selected' : '' }}>
                                    <i class="bi bi-bell"></i> Notificação Push
                                </option>
                                <option value="interno" {{ old('canal') == 'interno' ? 'selected' : '' }}>
                                    <i class="bi bi-house-door"></i> Sistema Interno
                                </option>
                            </select>
                            @error('canal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo') is-invalid @enderror" 
                                    id="tipo" name="tipo" required>
                                <option value="">Selecione o tipo</option>
                                <option value="follow_up" {{ old('tipo') == 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                <option value="lembrete" {{ old('tipo') == 'lembrete' ? 'selected' : '' }}>Lembrete</option>
                                <option value="convite" {{ old('tipo') == 'convite' ? 'selected' : '' }}>Convite</option>
                                <option value="informativo" {{ old('tipo') == 'informativo' ? 'selected' : '' }}>Informativo</option>
                                <option value="pesquisa" {{ old('tipo') == 'pesquisa' ? 'selected' : '' }}>Pesquisa</option>
                                <option value="campanha" {{ old('tipo') == 'campanha' ? 'selected' : '' }}>Campanha</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="prioridade" class="form-label">Prioridade <span class="text-danger">*</span></label>
                            <select class="form-select @error('prioridade') is-invalid @enderror" 
                                    id="prioridade" name="prioridade" required>
                                <option value="normal" {{ old('prioridade') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="baixa" {{ old('prioridade') == 'baixa' ? 'selected' : '' }}>Baixa</option>
                                <option value="alta" {{ old('prioridade') == 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="urgente" {{ old('prioridade') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            @error('prioridade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="data_agendamento" class="form-label">Data e Hora do Agendamento <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('data_agendamento') is-invalid @enderror" 
                                   id="data_agendamento" name="data_agendamento" value="{{ old('data_agendamento') }}" required>
                            @error('data_agendamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="template_id" class="form-label">Template (Opcional)</label>
                            <select class="form-select" id="template_id" name="template_id">
                                <option value="">Selecione um template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" 
                                            data-assunto="{{ $template->assunto }}"
                                            data-conteudo="{{ $template->conteudo }}"
                                            {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->nome }} ({{ ucfirst($template->canal) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Conteúdo da Mensagem -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-text me-2"></i>
                        Conteúdo da Mensagem
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título/Assunto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                               id="titulo" name="titulo" value="{{ old('titulo') }}" required maxlength="255">
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="conteudo" class="form-label">Conteúdo <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('conteudo') is-invalid @enderror" 
                                  id="conteudo" name="conteudo" rows="8" required>{{ old('conteudo') }}</textarea>
                        @error('conteudo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Use variáveis como {{nome}}, {{email}}, {{bairro}}, {{data_hoje}} para personalizar a mensagem.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações Internas</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" 
                                  rows="3">{{ old('observacoes') }}</textarea>
                        <div class="form-text">
                            Observações para uso interno da equipe.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
            <!-- Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>
                        Preview da Mensagem
                    </h5>
                </div>
                <div class="card-body">
                    <div id="preview-container">
                        <p class="text-muted">Selecione um cidadão e preencha o conteúdo para ver o preview.</p>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="atualizarPreview()">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Atualizar Preview
                    </button>
                </div>
            </div>
            
            <!-- Configurações Avançadas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-sliders me-2"></i>
                        Configurações Avançadas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="max_tentativas" class="form-label">Máximo de Tentativas</label>
                        <input type="number" class="form-control" id="max_tentativas" name="max_tentativas" 
                               value="{{ old('max_tentativas', 3) }}" min="1" max="10">
                        <div class="form-text">Número máximo de tentativas de envio em caso de falha.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="intervalo_tentativas" class="form-label">Intervalo entre Tentativas (minutos)</label>
                        <input type="number" class="form-control" id="intervalo_tentativas" name="intervalo_tentativas" 
                               value="{{ old('intervalo_tentativas', 60) }}" min="5" max="1440">
                        <div class="form-text">Tempo de espera entre tentativas de reenvio.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               value="{{ old('tags') }}" placeholder="tag1, tag2, tag3">
                        <div class="form-text">Tags para organização (separadas por vírgula).</div>
                    </div>
                </div>
            </div>
            
            <!-- Variáveis Disponíveis -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-code-square me-2"></i>
                        Variáveis Disponíveis
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Dados do Cidadão:</h6>
                    <div class="mb-2">
                        <code>{{nome}}</code> - Nome completo<br>
                        <code>{{email}}</code> - E-mail<br>
                        <code>{{telefone}}</code> - Telefone<br>
                        <code>{{bairro}}</code> - Bairro<br>
                        <code>{{idade}}</code> - Idade
                    </div>
                    
                    <h6>Sistema:</h6>
                    <div>
                        <code>{{data_hoje}}</code> - Data atual<br>
                        <code>{{hora_atual}}</code> - Hora atual<br>
                        <code>{{ano}}</code> - Ano atual
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botões de Ação -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between">
                    <a href="{{ route('agendamentos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </a>
                    
                    <div>
                        <button type="button" class="btn btn-outline-primary me-2" onclick="salvarRascunho()">
                            <i class="bi bi-save me-1"></i>
                            Salvar Rascunho
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-calendar-plus me-1"></i>
                            Criar Agendamento
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar data mínima
    const dataInput = document.getElementById('data_agendamento');
    const agora = new Date();
    agora.setMinutes(agora.getMinutes() - agora.getTimezoneOffset());
    dataInput.min = agora.toISOString().slice(0, 16);
    
    // Carregar templates baseado no canal
    const canalSelect = document.getElementById('canal');
    const templateSelect = document.getElementById('template_id');
    
    canalSelect.addEventListener('change', function() {
        carregarTemplatesPorCanal(this.value);
    });
    
    // Aplicar template quando selecionado
    templateSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const assunto = selectedOption.getAttribute('data-assunto');
            const conteudo = selectedOption.getAttribute('data-conteudo');
            
            if (assunto) {
                document.getElementById('titulo').value = assunto;
            }
            if (conteudo) {
                document.getElementById('conteudo').value = conteudo;
            }
            
            atualizarPreview();
        }
    });
    
    // Atualizar preview quando cidadão ou conteúdo mudar
    document.getElementById('cidadao_id').addEventListener('change', atualizarPreview);
    document.getElementById('titulo').addEventListener('input', atualizarPreview);
    document.getElementById('conteudo').addEventListener('input', atualizarPreview);
});

function carregarTemplatesPorCanal(canal) {
    const templateSelect = document.getElementById('template_id');
    
    // Limpar opções existentes
    templateSelect.innerHTML = '<option value="">Carregando...</option>';
    
    if (!canal) {
        templateSelect.innerHTML = '<option value="">Selecione um canal primeiro</option>';
        return;
    }
    
    fetch(`/api/templates/canal?canal=${canal}`)
        .then(response => response.json())
        .then(templates => {
            templateSelect.innerHTML = '<option value="">Selecione um template</option>';
            
            templates.forEach(template => {
                const option = document.createElement('option');
                option.value = template.id;
                option.textContent = `${template.nome} (${template.tipo})`;
                option.setAttribute('data-assunto', template.assunto || '');
                option.setAttribute('data-conteudo', template.conteudo || '');
                templateSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar templates:', error);
            templateSelect.innerHTML = '<option value="">Erro ao carregar templates</option>';
        });
}

function atualizarPreview() {
    const cidadaoId = document.getElementById('cidadao_id').value;
    const templateId = document.getElementById('template_id').value;
    const titulo = document.getElementById('titulo').value;
    const conteudo = document.getElementById('conteudo').value;
    
    const previewContainer = document.getElementById('preview-container');
    
    if (!cidadaoId) {
        previewContainer.innerHTML = '<p class="text-muted">Selecione um cidadão para ver o preview.</p>';
        return;
    }
    
    if (!titulo && !conteudo && !templateId) {
        previewContainer.innerHTML = '<p class="text-muted">Preencha o conteúdo ou selecione um template.</p>';
        return;
    }
    
    previewContainer.innerHTML = '<p class="text-muted">Gerando preview...</p>';
    
    // Se tem template, usar API de preview do template
    if (templateId) {
        fetch('/api/templates/preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                template_id: templateId,
                cidadao_id: cidadaoId
            })
        })
        .then(response => response.json())
        .then(data => {
            exibirPreview(data.assunto, data.conteudo);
        })
        .catch(error => {
            console.error('Erro ao gerar preview:', error);
            previewContainer.innerHTML = '<p class="text-danger">Erro ao gerar preview.</p>';
        });
    } else {
        // Preview simples sem template
        exibirPreview(titulo, conteudo);
    }
}

function exibirPreview(assunto, conteudo) {
    const previewContainer = document.getElementById('preview-container');
    
    let html = '';
    
    if (assunto) {
        html += `<h6><strong>Assunto:</strong></h6><p>${assunto}</p>`;
    }
    
    if (conteudo) {
        html += `<h6><strong>Conteúdo:</strong></h6><div class="border-start border-3 border-primary ps-3">${conteudo.replace(/\n/g, '<br>')}</div>`;
    }
    
    if (!html) {
        html = '<p class="text-muted">Preencha o conteúdo para ver o preview.</p>';
    }
    
    previewContainer.innerHTML = html;
}

function salvarRascunho() {
    // Implementar salvamento de rascunho
    alert('Funcionalidade de rascunho será implementada em breve.');
}
</script>
@endsection
