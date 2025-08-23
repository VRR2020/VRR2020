@extends('layouts.crm')

@section('title', 'Novo Cidadão - CRM Legislativo')
@section('page-title', 'Cadastrar Novo Cidadão')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">
                <i class="bi bi-person-plus me-2"></i>
                Cadastrar Novo Cidadão
            </h2>
            <a href="{{ route('cidadaos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Voltar à Lista
            </a>
        </div>
    </div>
</div>

<form action="{{ route('cidadaos.store') }}" method="POST" id="formCidadao">
    @csrf
    
    <div class="row">
        <!-- Dados Pessoais -->
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>
                        Dados Pessoais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8 mb-3">
                            <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                   id="nome" name="nome" value="{{ old('nome') }}" required maxlength="255">
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-4 mb-3">
                            <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                   id="cpf" name="cpf" value="{{ old('cpf') }}" required maxlength="14">
                            @error('cpf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                                   id="telefone" name="telefone" value="{{ old('telefone') }}" required maxlength="20">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" 
                                   id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}">
                            @error('data_nascimento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-4 mb-3">
                            <label for="idade" class="form-label">Idade</label>
                            <input type="number" class="form-control @error('idade') is-invalid @enderror" 
                                   id="idade" name="idade" value="{{ old('idade') }}" min="16" max="120">
                            @error('idade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-4 mb-3">
                            <label for="profissao" class="form-label">Profissão</label>
                            <input type="text" class="form-control @error('profissao') is-invalid @enderror" 
                                   id="profissao" name="profissao" value="{{ old('profissao') }}" maxlength="100">
                            @error('profissao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="bairro" class="form-label">Bairro <span class="text-danger">*</span></label>
                            <select class="form-select @error('bairro') is-invalid @enderror" id="bairro" name="bairro" required>
                                <option value="">Selecione o bairro</option>
                                <option value="Centro" {{ old('bairro') == 'Centro' ? 'selected' : '' }}>Centro</option>
                                <option value="Vila Nova" {{ old('bairro') == 'Vila Nova' ? 'selected' : '' }}>Vila Nova</option>
                                <option value="Jardim das Flores" {{ old('bairro') == 'Jardim das Flores' ? 'selected' : '' }}>Jardim das Flores</option>
                                <option value="Bela Vista" {{ old('bairro') == 'Bela Vista' ? 'selected' : '' }}>Bela Vista</option>
                                <option value="São João" {{ old('bairro') == 'São João' ? 'selected' : '' }}>São João</option>
                                <option value="Outro" {{ old('bairro') == 'Outro' ? 'selected' : '' }}>Outro</option>
                            </select>
                            @error('bairro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 col-md-6 mb-3">
                            <label for="renda_familiar" class="form-label">Renda Familiar</label>
                            <select class="form-select @error('renda_familiar') is-invalid @enderror" id="renda_familiar" name="renda_familiar">
                                <option value="">Não informado</option>
                                <option value="ate_1_salario" {{ old('renda_familiar') == 'ate_1_salario' ? 'selected' : '' }}>Até 1 salário mínimo</option>
                                <option value="1_a_3_salarios" {{ old('renda_familiar') == '1_a_3_salarios' ? 'selected' : '' }}>1 a 3 salários mínimos</option>
                                <option value="3_a_5_salarios" {{ old('renda_familiar') == '3_a_5_salarios' ? 'selected' : '' }}>3 a 5 salários mínimos</option>
                                <option value="5_a_10_salarios" {{ old('renda_familiar') == '5_a_10_salarios' ? 'selected' : '' }}>5 a 10 salários mínimos</option>
                                <option value="mais_10_salarios" {{ old('renda_familiar') == 'mais_10_salarios' ? 'selected' : '' }}>Mais de 10 salários mínimos</option>
                            </select>
                            @error('renda_familiar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço Completo</label>
                        <textarea class="form-control @error('endereco') is-invalid @enderror" 
                                  id="endereco" name="endereco" rows="2">{{ old('endereco') }}</textarea>
                        @error('endereco')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Interesses Políticos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-heart me-2"></i>
                        Interesses Políticos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Áreas de Interesse</label>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="educacao" id="int_educacao">
                                    <label class="form-check-label" for="int_educacao">Educação</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="saude" id="int_saude">
                                    <label class="form-check-label" for="int_saude">Saúde</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="infraestrutura" id="int_infraestrutura">
                                    <label class="form-check-label" for="int_infraestrutura">Infraestrutura</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="seguranca" id="int_seguranca">
                                    <label class="form-check-label" for="int_seguranca">Segurança</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="meio_ambiente" id="int_meio_ambiente">
                                    <label class="form-check-label" for="int_meio_ambiente">Meio Ambiente</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="assistencia_social" id="int_assistencia">
                                    <label class="form-check-label" for="int_assistencia">Assistência Social</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="transporte" id="int_transporte">
                                    <label class="form-check-label" for="int_transporte">Transporte</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="cultura" id="int_cultura">
                                    <label class="form-check-label" for="int_cultura">Cultura</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="esporte" id="int_esporte">
                                    <label class="form-check-label" for="int_esporte">Esporte</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interesses_politicos[]" value="habitacao" id="int_habitacao">
                                    <label class="form-check-label" for="int_habitacao">Habitação</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags de Segmentação</label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               placeholder="Ex: liderança, jovem, empresário (separadas por vírgula)">
                        <div class="form-text">Digite as tags separadas por vírgula para facilitar a segmentação.</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Configurações -->
        <div class="col-12 col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Configurações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Inicial</label>
                        <select class="form-select" id="status" name="status">
                            <option value="lead" selected>Lead</option>
                            <option value="engajado">Engajado</option>
                            <option value="ativo">Ativo</option>
                            <option value="apoiador">Apoiador</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nivel_engajamento" class="form-label">Nível de Engajamento</label>
                        <select class="form-select" id="nivel_engajamento" name="nivel_engajamento">
                            <option value="baixo" selected>Baixo</option>
                            <option value="medio">Médio</option>
                            <option value="alto">Alto</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="origem_cadastro" class="form-label">Como chegou ao gabinete?</label>
                        <select class="form-select" id="origem_cadastro" name="origem_cadastro">
                            <option value="">Não informado</option>
                            <option value="evento">Evento/Reunião</option>
                            <option value="indicacao">Indicação</option>
                            <option value="redes_sociais">Redes Sociais</option>
                            <option value="site">Site</option>
                            <option value="telefone">Ligação Telefônica</option>
                            <option value="presencial">Visita Presencial</option>
                            <option value="campanha">Campanha</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Redes Sociais -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-share me-2"></i>
                        Redes Sociais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="facebook" class="form-label">
                            <i class="bi bi-facebook text-primary me-1"></i>
                            Facebook
                        </label>
                        <input type="url" class="form-control" id="facebook" name="redes_sociais[facebook]" 
                               placeholder="https://facebook.com/usuario">
                    </div>
                    
                    <div class="mb-3">
                        <label for="instagram" class="form-label">
                            <i class="bi bi-instagram text-danger me-1"></i>
                            Instagram
                        </label>
                        <input type="url" class="form-control" id="instagram" name="redes_sociais[instagram]" 
                               placeholder="https://instagram.com/usuario">
                    </div>
                    
                    <div class="mb-3">
                        <label for="whatsapp" class="form-label">
                            <i class="bi bi-whatsapp text-success me-1"></i>
                            WhatsApp
                        </label>
                        <input type="text" class="form-control" id="whatsapp" name="redes_sociais[whatsapp]" 
                               placeholder="(11) 99999-9999">
                    </div>
                </div>
            </div>
            
            <!-- Observações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-square-text me-2"></i>
                        Observações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="4" 
                                  placeholder="Informações adicionais sobre o cidadão...">{{ old('observacoes') }}</textarea>
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
                    <a href="{{ route('cidadaos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </a>
                    
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            Cadastrar Cidadão
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
    // Máscara para CPF
    const cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });
    
    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    telefoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 10) {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        e.target.value = value;
    });
    
    // Calcular idade quando data de nascimento for preenchida
    const dataNascimento = document.getElementById('data_nascimento');
    const idadeInput = document.getElementById('idade');
    
    dataNascimento.addEventListener('change', function() {
        if (this.value) {
            const hoje = new Date();
            const nascimento = new Date(this.value);
            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const mes = hoje.getMonth() - nascimento.getMonth();
            
            if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                idade--;
            }
            
            idadeInput.value = idade;
        }
    });
    
    // Validação do formulário
    const form = document.getElementById('formCidadao');
    form.addEventListener('submit', function(e) {
        const nome = document.getElementById('nome').value.trim();
        const email = document.getElementById('email').value.trim();
        const cpf = document.getElementById('cpf').value.trim();
        const telefone = document.getElementById('telefone').value.trim();
        const bairro = document.getElementById('bairro').value;
        
        if (!nome || !email || !cpf || !telefone || !bairro) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
    });
});
</script>
@endsection
