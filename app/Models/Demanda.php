<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Demanda extends Model
{
    use SoftDeletes;

    protected $table = 'demandas';

    protected $fillable = [
        'titulo',
        'descricao',
        'tipo',
        'categoria',
        'subcategoria',
        'urgencia',
        'status',
        'cidadao_id',
        'responsavel_id',
        'bairro',
        'endereco_referencia',
        'protocolo',
        'data_prazo',
        'data_resolucao',
        'solucao_aplicada',
        'custo_estimado',
        'observacoes_internas',
        'anexos',
        'tags'
    ];

    protected $casts = [
        'data_prazo' => 'date',
        'data_resolucao' => 'date',
        'custo_estimado' => 'decimal:2',
        'anexos' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Tipos de demanda
    const TIPO_INDIVIDUAL = 'individual';
    const TIPO_COLETIVA = 'coletiva';

    // Status possíveis
    const STATUS_ABERTA = 'aberta';
    const STATUS_EM_ANDAMENTO = 'em_andamento';
    const STATUS_PENDENTE = 'pendente';
    const STATUS_RESOLVIDA = 'resolvida';
    const STATUS_CANCELADA = 'cancelada';

    // Níveis de urgência
    const URGENCIA_BAIXA = 'baixa';
    const URGENCIA_MEDIA = 'media';
    const URGENCIA_ALTA = 'alta';
    const URGENCIA_CRITICA = 'critica';

    // Categorias principais
    const CATEGORIA_INFRAESTRUTURA = 'infraestrutura';
    const CATEGORIA_SAUDE = 'saude';
    const CATEGORIA_EDUCACAO = 'educacao';
    const CATEGORIA_SEGURANCA = 'seguranca';
    const CATEGORIA_MEIO_AMBIENTE = 'meio_ambiente';
    const CATEGORIA_ASSISTENCIA_SOCIAL = 'assistencia_social';
    const CATEGORIA_TRANSPORTE = 'transporte';
    const CATEGORIA_HABITACAO = 'habitacao';
    const CATEGORIA_CULTURA = 'cultura';
    const CATEGORIA_ESPORTE = 'esporte';
    const CATEGORIA_OUTROS = 'outros';

    /**
     * Relacionamento com cidadão
     */
    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    /**
     * Relacionamento com responsável (usuário do sistema)
     */
    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    /**
     * Relacionamento com interações
     */
    public function interacoes()
    {
        return $this->hasMany(Interacao::class);
    }

    /**
     * Relacionamento com histórico de status
     */
    public function historicoStatus()
    {
        return $this->hasMany(DemandaHistorico::class);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por categoria
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para filtrar por urgência
     */
    public function scopePorUrgencia($query, $urgencia)
    {
        return $query->where('urgencia', $urgencia);
    }

    /**
     * Scope para filtrar por bairro
     */
    public function scopePorBairro($query, $bairro)
    {
        return $query->where('bairro', $bairro);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('created_at', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para busca geral
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->where('titulo', 'like', "%{$termo}%")
              ->orWhere('descricao', 'like', "%{$termo}%")
              ->orWhere('protocolo', 'like', "%{$termo}%")
              ->orWhere('bairro', 'like', "%{$termo}%");
        });
    }

    /**
     * Scope para demandas em atraso
     */
    public function scopeEmAtraso($query)
    {
        return $query->where('data_prazo', '<', now())
                    ->whereNotIn('status', [self::STATUS_RESOLVIDA, self::STATUS_CANCELADA]);
    }

    /**
     * Retorna array de tipos
     */
    public static function getTiposOptions()
    {
        return [
            self::TIPO_INDIVIDUAL => 'Individual',
            self::TIPO_COLETIVA => 'Coletiva'
        ];
    }

    /**
     * Retorna array de status
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_ABERTA => 'Aberta',
            self::STATUS_EM_ANDAMENTO => 'Em Andamento',
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_RESOLVIDA => 'Resolvida',
            self::STATUS_CANCELADA => 'Cancelada'
        ];
    }

    /**
     * Retorna array de urgências
     */
    public static function getUrgenciasOptions()
    {
        return [
            self::URGENCIA_BAIXA => 'Baixa',
            self::URGENCIA_MEDIA => 'Média',
            self::URGENCIA_ALTA => 'Alta',
            self::URGENCIA_CRITICA => 'Crítica'
        ];
    }

    /**
     * Retorna array de categorias
     */
    public static function getCategoriasOptions()
    {
        return [
            self::CATEGORIA_INFRAESTRUTURA => 'Infraestrutura',
            self::CATEGORIA_SAUDE => 'Saúde',
            self::CATEGORIA_EDUCACAO => 'Educação',
            self::CATEGORIA_SEGURANCA => 'Segurança',
            self::CATEGORIA_MEIO_AMBIENTE => 'Meio Ambiente',
            self::CATEGORIA_ASSISTENCIA_SOCIAL => 'Assistência Social',
            self::CATEGORIA_TRANSPORTE => 'Transporte',
            self::CATEGORIA_HABITACAO => 'Habitação',
            self::CATEGORIA_CULTURA => 'Cultura',
            self::CATEGORIA_ESPORTE => 'Esporte',
            self::CATEGORIA_OUTROS => 'Outros'
        ];
    }

    /**
     * Gera protocolo único
     */
    public static function gerarProtocolo()
    {
        $ano = date('Y');
        $ultimoNumero = self::where('protocolo', 'like', $ano . '%')
                           ->orderBy('protocolo', 'desc')
                           ->value('protocolo');
        
        if ($ultimoNumero) {
            $numero = intval(substr($ultimoNumero, -4)) + 1;
        } else {
            $numero = 1;
        }
        
        return $ano . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Retorna classe CSS para o status
     */
    public function getStatusClasseAttribute()
    {
        $classes = [
            self::STATUS_ABERTA => 'badge bg-warning',
            self::STATUS_EM_ANDAMENTO => 'badge bg-info',
            self::STATUS_PENDENTE => 'badge bg-secondary',
            self::STATUS_RESOLVIDA => 'badge bg-success',
            self::STATUS_CANCELADA => 'badge bg-danger'
        ];

        return $classes[$this->status] ?? 'badge bg-secondary';
    }

    /**
     * Retorna classe CSS para a urgência
     */
    public function getUrgenciaClasseAttribute()
    {
        $classes = [
            self::URGENCIA_BAIXA => 'text-success',
            self::URGENCIA_MEDIA => 'text-warning',
            self::URGENCIA_ALTA => 'text-danger',
            self::URGENCIA_CRITICA => 'text-danger fw-bold'
        ];

        return $classes[$this->urgencia] ?? 'text-secondary';
    }

    /**
     * Verifica se a demanda está em atraso
     */
    public function getEmAtrasoAttribute()
    {
        return $this->data_prazo && 
               $this->data_prazo < now() && 
               !in_array($this->status, [self::STATUS_RESOLVIDA, self::STATUS_CANCELADA]);
    }

    /**
     * Calcula dias para o prazo
     */
    public function getDiasParaPrazoAttribute()
    {
        if (!$this->data_prazo) {
            return null;
        }

        return now()->diffInDays($this->data_prazo, false);
    }

    /**
     * Retorna tempo de resolução em dias
     */
    public function getTempoResolucaoAttribute()
    {
        if ($this->status !== self::STATUS_RESOLVIDA || !$this->data_resolucao) {
            return null;
        }

        return $this->created_at->diffInDays($this->data_resolucao);
    }
}
