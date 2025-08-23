<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cidadao extends Model
{
    use SoftDeletes;

    protected $table = 'cidadaos';

    protected $fillable = [
        'nome',
        'cpf',
        'email',
        'telefone',
        'bairro',
        'endereco',
        'idade',
        'data_nascimento',
        'profissao',
        'renda_familiar',
        'interesses_politicos',
        'tags',
        'status',
        'nivel_engajamento',
        'origem_cadastro',
        'observacoes',
        'redes_sociais'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'interesses_politicos' => 'array',
        'tags' => 'array',
        'redes_sociais' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Status possíveis
    const STATUS_LEAD = 'lead';
    const STATUS_ENGAJADO = 'engajado';
    const STATUS_ATIVO = 'ativo';
    const STATUS_APOIADOR = 'apoiador';
    const STATUS_INATIVO = 'inativo';

    // Níveis de engajamento
    const ENGAJAMENTO_BAIXO = 'baixo';
    const ENGAJAMENTO_MEDIO = 'medio';
    const ENGAJAMENTO_ALTO = 'alto';

    /**
     * Relacionamento com demandas
     */
    public function demandas()
    {
        return $this->hasMany(Demanda::class);
    }

    /**
     * Relacionamento com interações
     */
    public function interacoes()
    {
        return $this->hasMany(Interacao::class);
    }

    /**
     * Relacionamento com agendamentos
     */
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    /**
     * Scope para filtrar por bairro
     */
    public function scopePorBairro($query, $bairro)
    {
        return $query->where('bairro', $bairro);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por tags
     */
    public function scopeComTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope para busca geral
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->where('nome', 'like', "%{$termo}%")
              ->orWhere('email', 'like', "%{$termo}%")
              ->orWhere('cpf', 'like', "%{$termo}%")
              ->orWhere('telefone', 'like', "%{$termo}%")
              ->orWhere('bairro', 'like', "%{$termo}%");
        });
    }

    /**
     * Retorna array de status possíveis
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_LEAD => 'Lead',
            self::STATUS_ENGAJADO => 'Engajado',
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_APOIADOR => 'Apoiador',
            self::STATUS_INATIVO => 'Inativo'
        ];
    }

    /**
     * Retorna array de níveis de engajamento
     */
    public static function getEngajamentoOptions()
    {
        return [
            self::ENGAJAMENTO_BAIXO => 'Baixo',
            self::ENGAJAMENTO_MEDIO => 'Médio',
            self::ENGAJAMENTO_ALTO => 'Alto'
        ];
    }

    /**
     * Formata CPF para exibição
     */
    public function getCpfFormatadoAttribute()
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    /**
     * Formata telefone para exibição
     */
    public function getTelefoneFormatadoAttribute()
    {
        $telefone = preg_replace('/\D/', '', $this->telefone);
        
        if (strlen($telefone) == 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        } else if (strlen($telefone) == 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }
        
        return $this->telefone;
    }

    /**
     * Calcula score de engajamento
     */
    public function getScoreEngajamento()
    {
        $score = 0;
        
        // Pontos por interações recentes (últimos 30 dias)
        $interacoesRecentes = $this->interacoes()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $score += $interacoesRecentes * 10;
        
        // Pontos por demandas ativas
        $demandasAtivas = $this->demandas()
            ->whereIn('status', ['aberta', 'em_andamento'])
            ->count();
        $score += $demandasAtivas * 20;
        
        // Pontos por tempo de cadastro
        $diasCadastro = $this->created_at->diffInDays(now());
        if ($diasCadastro > 365) {
            $score += 30; // Cidadão antigo
        } else if ($diasCadastro > 90) {
            $score += 20;
        } else if ($diasCadastro > 30) {
            $score += 10;
        }
        
        return $score;
    }
}
