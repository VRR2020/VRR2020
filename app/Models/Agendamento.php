<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agendamento extends Model
{
    use SoftDeletes;

    protected $table = 'agendamentos';

    protected $fillable = [
        'cidadao_id',
        'usuario_id',
        'template_id',
        'tipo',
        'canal',
        'titulo',
        'conteudo',
        'data_agendamento',
        'data_envio',
        'status',
        'resultado',
        'observacoes',
        'tentativas',
        'max_tentativas',
        'intervalo_tentativas',
        'prioridade',
        'tags',
        'dados_dinamicos',
        'configuracoes'
    ];

    protected $casts = [
        'data_agendamento' => 'datetime',
        'data_envio' => 'datetime',
        'tags' => 'array',
        'dados_dinamicos' => 'array',
        'configuracoes' => 'array',
        'tentativas' => 'integer',
        'max_tentativas' => 'integer',
        'intervalo_tentativas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Tipos de agendamento
    const TIPO_LEMBRETE = 'lembrete';
    const TIPO_FOLLOW_UP = 'follow_up';
    const TIPO_CONVITE = 'convite';
    const TIPO_INFORMATIVO = 'informativo';
    const TIPO_PESQUISA = 'pesquisa';
    const TIPO_CAMPANHA = 'campanha';

    // Canais de envio
    const CANAL_EMAIL = 'email';
    const CANAL_SMS = 'sms';
    const CANAL_WHATSAPP = 'whatsapp';
    const CANAL_PUSH = 'push';
    const CANAL_INTERNO = 'interno';

    // Status do agendamento
    const STATUS_AGENDADO = 'agendado';
    const STATUS_ENVIANDO = 'enviando';
    const STATUS_ENVIADO = 'enviado';
    const STATUS_ENTREGUE = 'entregue';
    const STATUS_LIDO = 'lido';
    const STATUS_RESPONDIDO = 'respondido';
    const STATUS_FALHADO = 'falhado';
    const STATUS_CANCELADO = 'cancelado';

    // Prioridades
    const PRIORIDADE_BAIXA = 'baixa';
    const PRIORIDADE_NORMAL = 'normal';
    const PRIORIDADE_ALTA = 'alta';
    const PRIORIDADE_URGENTE = 'urgente';

    // Resultados
    const RESULTADO_POSITIVO = 'positivo';
    const RESULTADO_NEUTRO = 'neutro';
    const RESULTADO_NEGATIVO = 'negativo';
    const RESULTADO_SEM_RESPOSTA = 'sem_resposta';

    /**
     * Relacionamento com cidadão
     */
    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    /**
     * Relacionamento com usuário responsável
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com template
     */
    public function template()
    {
        return $this->belongsTo(TemplateMessage::class, 'template_id');
    }

    /**
     * Scope para agendamentos pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where('status', self::STATUS_AGENDADO)
                    ->where('data_agendamento', '<=', now());
    }

    /**
     * Scope para filtrar por canal
     */
    public function scopePorCanal($query, $canal)
    {
        return $query->where('canal', $canal);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_agendamento', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para agendamentos atrasados
     */
    public function scopeAtrasados($query)
    {
        return $query->where('status', self::STATUS_AGENDADO)
                    ->where('data_agendamento', '<', now());
    }

    /**
     * Scope para filtrar por prioridade
     */
    public function scopePorPrioridade($query, $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    /**
     * Retorna array de tipos
     */
    public static function getTiposOptions()
    {
        return [
            self::TIPO_LEMBRETE => 'Lembrete',
            self::TIPO_FOLLOW_UP => 'Follow-up',
            self::TIPO_CONVITE => 'Convite',
            self::TIPO_INFORMATIVO => 'Informativo',
            self::TIPO_PESQUISA => 'Pesquisa',
            self::TIPO_CAMPANHA => 'Campanha'
        ];
    }

    /**
     * Retorna array de canais
     */
    public static function getCanaisOptions()
    {
        return [
            self::CANAL_EMAIL => 'E-mail',
            self::CANAL_SMS => 'SMS',
            self::CANAL_WHATSAPP => 'WhatsApp',
            self::CANAL_PUSH => 'Notificação Push',
            self::CANAL_INTERNO => 'Sistema Interno'
        ];
    }

    /**
     * Retorna array de status
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_AGENDADO => 'Agendado',
            self::STATUS_ENVIANDO => 'Enviando',
            self::STATUS_ENVIADO => 'Enviado',
            self::STATUS_ENTREGUE => 'Entregue',
            self::STATUS_LIDO => 'Lido',
            self::STATUS_RESPONDIDO => 'Respondido',
            self::STATUS_FALHADO => 'Falhado',
            self::STATUS_CANCELADO => 'Cancelado'
        ];
    }

    /**
     * Retorna array de prioridades
     */
    public static function getPrioridadesOptions()
    {
        return [
            self::PRIORIDADE_BAIXA => 'Baixa',
            self::PRIORIDADE_NORMAL => 'Normal',
            self::PRIORIDADE_ALTA => 'Alta',
            self::PRIORIDADE_URGENTE => 'Urgente'
        ];
    }

    /**
     * Processa variáveis dinâmicas no conteúdo
     */
    public function processarConteudo()
    {
        $conteudo = $this->conteudo;
        
        if ($this->template) {
            $conteudo = $this->template->conteudo;
        }

        // Variáveis do cidadão
        if ($this->cidadao) {
            $variaveis = [
                '{{nome}}' => $this->cidadao->nome,
                '{{email}}' => $this->cidadao->email,
                '{{telefone}}' => $this->cidadao->telefone,
                '{{bairro}}' => $this->cidadao->bairro,
                '{{idade}}' => $this->cidadao->idade,
            ];

            // Variáveis dinâmicas específicas
            if ($this->dados_dinamicos) {
                foreach ($this->dados_dinamicos as $chave => $valor) {
                    $variaveis["{{" . $chave . "}}"] = $valor;
                }
            }

            $conteudo = str_replace(array_keys($variaveis), array_values($variaveis), $conteudo);
        }

        // Variáveis do sistema
        $variaveisSistema = [
            '{{data_hoje}}' => now()->format('d/m/Y'),
            '{{hora_atual}}' => now()->format('H:i'),
            '{{ano}}' => now()->year,
            '{{mes}}' => now()->format('m'),
            '{{dia}}' => now()->format('d'),
        ];

        $conteudo = str_replace(array_keys($variaveisSistema), array_values($variaveisSistema), $conteudo);

        return $conteudo;
    }

    /**
     * Verifica se o agendamento está atrasado
     */
    public function getAtrasadoAttribute()
    {
        return $this->status === self::STATUS_AGENDADO && 
               $this->data_agendamento < now();
    }

    /**
     * Retorna classe CSS para o status
     */
    public function getStatusClasseAttribute()
    {
        $classes = [
            self::STATUS_AGENDADO => 'badge bg-warning',
            self::STATUS_ENVIANDO => 'badge bg-info',
            self::STATUS_ENVIADO => 'badge bg-primary',
            self::STATUS_ENTREGUE => 'badge bg-success',
            self::STATUS_LIDO => 'badge bg-success',
            self::STATUS_RESPONDIDO => 'badge bg-success',
            self::STATUS_FALHADO => 'badge bg-danger',
            self::STATUS_CANCELADO => 'badge bg-secondary'
        ];

        return $classes[$this->status] ?? 'badge bg-secondary';
    }

    /**
     * Retorna ícone para o canal
     */
    public function getCanalIconeAttribute()
    {
        $icones = [
            self::CANAL_EMAIL => 'bi-envelope',
            self::CANAL_SMS => 'bi-chat-text',
            self::CANAL_WHATSAPP => 'bi-whatsapp',
            self::CANAL_PUSH => 'bi-bell',
            self::CANAL_INTERNO => 'bi-house-door'
        ];

        return $icones[$this->canal] ?? 'bi-chat-dots';
    }

    /**
     * Retorna cor para a prioridade
     */
    public function getPrioridadeCorAttribute()
    {
        $cores = [
            self::PRIORIDADE_BAIXA => 'text-secondary',
            self::PRIORIDADE_NORMAL => 'text-primary',
            self::PRIORIDADE_ALTA => 'text-warning',
            self::PRIORIDADE_URGENTE => 'text-danger'
        ];

        return $cores[$this->prioridade] ?? 'text-secondary';
    }

    /**
     * Verifica se pode ser reenviado
     */
    public function getPodeReenviarAttribute()
    {
        return in_array($this->status, [self::STATUS_FALHADO, self::STATUS_CANCELADO]) &&
               $this->tentativas < $this->max_tentativas;
    }

    /**
     * Marca como enviado
     */
    public function marcarComoEnviado()
    {
        $this->update([
            'status' => self::STATUS_ENVIADO,
            'data_envio' => now(),
            'tentativas' => $this->tentativas + 1
        ]);
    }

    /**
     * Marca como falhado
     */
    public function marcarComoFalhado($motivo = null)
    {
        $observacoes = $this->observacoes;
        if ($motivo) {
            $observacoes .= "\nFalha: " . $motivo . " (" . now()->format('d/m/Y H:i') . ")";
        }

        $this->update([
            'status' => self::STATUS_FALHADO,
            'tentativas' => $this->tentativas + 1,
            'observacoes' => $observacoes
        ]);
    }

    /**
     * Reagenda para nova data
     */
    public function reagendar($novaData, $motivo = null)
    {
        $observacoes = $this->observacoes;
        if ($motivo) {
            $observacoes .= "\nReagendado: " . $motivo . " (" . now()->format('d/m/Y H:i') . ")";
        }

        $this->update([
            'data_agendamento' => $novaData,
            'status' => self::STATUS_AGENDADO,
            'observacoes' => $observacoes
        ]);
    }
}
