<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interacao extends Model
{
    protected $table = 'interacoes';

    protected $fillable = [
        'cidadao_id',
        'demanda_id',
        'usuario_id',
        'tipo',
        'canal',
        'assunto',
        'descricao',
        'sentido',
        'status',
        'data_agendamento',
        'data_realizacao',
        'duracao_minutos',
        'resultado',
        'observacoes',
        'anexos',
        'tags'
    ];

    protected $casts = [
        'data_agendamento' => 'datetime',
        'data_realizacao' => 'datetime',
        'duracao_minutos' => 'integer',
        'anexos' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Tipos de interação
    const TIPO_REUNIAO = 'reuniao';
    const TIPO_TELEFONEMA = 'telefonema';
    const TIPO_EMAIL = 'email';
    const TIPO_SMS = 'sms';
    const TIPO_WHATSAPP = 'whatsapp';
    const TIPO_EVENTO = 'evento';
    const TIPO_VISITA = 'visita';
    const TIPO_CARTA = 'carta';
    const TIPO_REDES_SOCIAIS = 'redes_sociais';

    // Canais de comunicação
    const CANAL_PRESENCIAL = 'presencial';
    const CANAL_TELEFONICO = 'telefonico';
    const CANAL_EMAIL = 'email';
    const CANAL_WHATSAPP = 'whatsapp';
    const CANAL_SMS = 'sms';
    const CANAL_FACEBOOK = 'facebook';
    const CANAL_INSTAGRAM = 'instagram';
    const CANAL_TWITTER = 'twitter';
    const CANAL_LINKEDIN = 'linkedin';
    const CANAL_SITE = 'site';

    // Sentido da comunicação
    const SENTIDO_ENTRADA = 'entrada'; // Cidadão contatou o gabinete
    const SENTIDO_SAIDA = 'saida';     // Gabinete contatou o cidadão

    // Status da interação
    const STATUS_AGENDADA = 'agendada';
    const STATUS_REALIZADA = 'realizada';
    const STATUS_CANCELADA = 'cancelada';
    const STATUS_NAO_REALIZADA = 'nao_realizada';

    // Resultados possíveis
    const RESULTADO_POSITIVO = 'positivo';
    const RESULTADO_NEUTRO = 'neutro';
    const RESULTADO_NEGATIVO = 'negativo';
    const RESULTADO_NAO_ATENDEU = 'nao_atendeu';
    const RESULTADO_REAGENDADO = 'reagendado';

    /**
     * Relacionamento com cidadão
     */
    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    /**
     * Relacionamento com demanda (opcional)
     */
    public function demanda()
    {
        return $this->belongsTo(Demanda::class);
    }

    /**
     * Relacionamento com usuário responsável
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por canal
     */
    public function scopePorCanal($query, $canal)
    {
        return $query->where('canal', $canal);
    }

    /**
     * Scope para filtrar por sentido
     */
    public function scopePorSentido($query, $sentido)
    {
        return $query->where('sentido', $sentido);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('created_at', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para interações realizadas
     */
    public function scopeRealizadas($query)
    {
        return $query->where('status', self::STATUS_REALIZADA);
    }

    /**
     * Scope para interações agendadas
     */
    public function scopeAgendadas($query)
    {
        return $query->where('status', self::STATUS_AGENDADA);
    }

    /**
     * Scope para busca geral
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->where('assunto', 'like', "%{$termo}%")
              ->orWhere('descricao', 'like', "%{$termo}%")
              ->orWhere('observacoes', 'like', "%{$termo}%");
        });
    }

    /**
     * Retorna array de tipos
     */
    public static function getTiposOptions()
    {
        return [
            self::TIPO_REUNIAO => 'Reunião',
            self::TIPO_TELEFONEMA => 'Telefonema',
            self::TIPO_EMAIL => 'E-mail',
            self::TIPO_SMS => 'SMS',
            self::TIPO_WHATSAPP => 'WhatsApp',
            self::TIPO_EVENTO => 'Evento',
            self::TIPO_VISITA => 'Visita',
            self::TIPO_CARTA => 'Carta',
            self::TIPO_REDES_SOCIAIS => 'Redes Sociais'
        ];
    }

    /**
     * Retorna array de canais
     */
    public static function getCanaisOptions()
    {
        return [
            self::CANAL_PRESENCIAL => 'Presencial',
            self::CANAL_TELEFONICO => 'Telefônico',
            self::CANAL_EMAIL => 'E-mail',
            self::CANAL_WHATSAPP => 'WhatsApp',
            self::CANAL_SMS => 'SMS',
            self::CANAL_FACEBOOK => 'Facebook',
            self::CANAL_INSTAGRAM => 'Instagram',
            self::CANAL_TWITTER => 'Twitter',
            self::CANAL_LINKEDIN => 'LinkedIn',
            self::CANAL_SITE => 'Site'
        ];
    }

    /**
     * Retorna array de sentidos
     */
    public static function getSentidosOptions()
    {
        return [
            self::SENTIDO_ENTRADA => 'Entrada (Cidadão contatou)',
            self::SENTIDO_SAIDA => 'Saída (Gabinete contatou)'
        ];
    }

    /**
     * Retorna array de status
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_AGENDADA => 'Agendada',
            self::STATUS_REALIZADA => 'Realizada',
            self::STATUS_CANCELADA => 'Cancelada',
            self::STATUS_NAO_REALIZADA => 'Não Realizada'
        ];
    }

    /**
     * Retorna array de resultados
     */
    public static function getResultadosOptions()
    {
        return [
            self::RESULTADO_POSITIVO => 'Positivo',
            self::RESULTADO_NEUTRO => 'Neutro',
            self::RESULTADO_NEGATIVO => 'Negativo',
            self::RESULTADO_NAO_ATENDEU => 'Não Atendeu',
            self::RESULTADO_REAGENDADO => 'Reagendado'
        ];
    }

    /**
     * Retorna ícone para o tipo de interação
     */
    public function getIconeAttribute()
    {
        $icones = [
            self::TIPO_REUNIAO => 'bi-people',
            self::TIPO_TELEFONEMA => 'bi-telephone',
            self::TIPO_EMAIL => 'bi-envelope',
            self::TIPO_SMS => 'bi-chat-text',
            self::TIPO_WHATSAPP => 'bi-whatsapp',
            self::TIPO_EVENTO => 'bi-calendar-event',
            self::TIPO_VISITA => 'bi-house-door',
            self::TIPO_CARTA => 'bi-mailbox',
            self::TIPO_REDES_SOCIAIS => 'bi-share'
        ];

        return $icones[$this->tipo] ?? 'bi-chat-dots';
    }

    /**
     * Retorna classe CSS para o status
     */
    public function getStatusClasseAttribute()
    {
        $classes = [
            self::STATUS_AGENDADA => 'badge bg-warning',
            self::STATUS_REALIZADA => 'badge bg-success',
            self::STATUS_CANCELADA => 'badge bg-danger',
            self::STATUS_NAO_REALIZADA => 'badge bg-secondary'
        ];

        return $classes[$this->status] ?? 'badge bg-secondary';
    }

    /**
     * Retorna classe CSS para o sentido
     */
    public function getSentidoClasseAttribute()
    {
        return $this->sentido === self::SENTIDO_ENTRADA ? 'text-primary' : 'text-success';
    }

    /**
     * Retorna ícone para o sentido
     */
    public function getSentidoIconeAttribute()
    {
        return $this->sentido === self::SENTIDO_ENTRADA ? 'bi-arrow-down-left' : 'bi-arrow-up-right';
    }

    /**
     * Verifica se a interação está atrasada (para agendadas)
     */
    public function getAtrasadaAttribute()
    {
        return $this->status === self::STATUS_AGENDADA && 
               $this->data_agendamento && 
               $this->data_agendamento < now();
    }

    /**
     * Formata duração em texto
     */
    public function getDuracaoFormatadaAttribute()
    {
        if (!$this->duracao_minutos) {
            return null;
        }

        $horas = intval($this->duracao_minutos / 60);
        $minutos = $this->duracao_minutos % 60;

        if ($horas > 0) {
            return $horas . 'h' . ($minutos > 0 ? ' ' . $minutos . 'min' : '');
        }

        return $minutos . 'min';
    }
}
