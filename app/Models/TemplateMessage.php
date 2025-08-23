<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateMessage extends Model
{
    use SoftDeletes;

    protected $table = 'templates_messages';

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
        'canal',
        'categoria',
        'assunto',
        'conteudo',
        'variaveis_disponiveis',
        'configuracoes',
        'ativo',
        'usuario_id',
        'uso_contador',
        'tags'
    ];

    protected $casts = [
        'variaveis_disponiveis' => 'array',
        'configuracoes' => 'array',
        'ativo' => 'boolean',
        'uso_contador' => 'integer',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Tipos de template
    const TIPO_WELCOME = 'welcome';
    const TIPO_FOLLOW_UP = 'follow_up';
    const TIPO_LEMBRETE = 'lembrete';
    const TIPO_CONVITE = 'convite';
    const TIPO_NEWSLETTER = 'newsletter';
    const TIPO_PESQUISA = 'pesquisa';
    const TIPO_AGRADECIMENTO = 'agradecimento';
    const TIPO_CONFIRMACAO = 'confirmacao';
    const TIPO_CANCELAMENTO = 'cancelamento';
    const TIPO_PERSONALIZADO = 'personalizado';

    // Canais suportados
    const CANAL_EMAIL = 'email';
    const CANAL_SMS = 'sms';
    const CANAL_WHATSAPP = 'whatsapp';
    const CANAL_PUSH = 'push';
    const CANAL_TODOS = 'todos';

    // Categorias
    const CATEGORIA_ADMINISTRATIVO = 'administrativo';
    const CATEGORIA_MARKETING = 'marketing';
    const CATEGORIA_SUPORTE = 'suporte';
    const CATEGORIA_EVENTOS = 'eventos';
    const CATEGORIA_POLITICO = 'politico';
    const CATEGORIA_SOCIAL = 'social';

    /**
     * Relacionamento com usuário criador
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com agendamentos
     */
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'template_id');
    }

    /**
     * Scope para templates ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
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
        return $query->where('canal', $canal)->orWhere('canal', self::CANAL_TODOS);
    }

    /**
     * Scope para filtrar por categoria
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para busca geral
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->where('nome', 'like', "%{$termo}%")
              ->orWhere('descricao', 'like', "%{$termo}%")
              ->orWhere('assunto', 'like', "%{$termo}%")
              ->orWhere('conteudo', 'like', "%{$termo}%");
        });
    }

    /**
     * Scope para templates mais usados
     */
    public function scopeMaisUsados($query, $limite = 10)
    {
        return $query->orderBy('uso_contador', 'desc')->limit($limite);
    }

    /**
     * Retorna array de tipos
     */
    public static function getTiposOptions()
    {
        return [
            self::TIPO_WELCOME => 'Boas-vindas',
            self::TIPO_FOLLOW_UP => 'Follow-up',
            self::TIPO_LEMBRETE => 'Lembrete',
            self::TIPO_CONVITE => 'Convite',
            self::TIPO_NEWSLETTER => 'Newsletter',
            self::TIPO_PESQUISA => 'Pesquisa',
            self::TIPO_AGRADECIMENTO => 'Agradecimento',
            self::TIPO_CONFIRMACAO => 'Confirmação',
            self::TIPO_CANCELAMENTO => 'Cancelamento',
            self::TIPO_PERSONALIZADO => 'Personalizado'
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
            self::CANAL_TODOS => 'Todos os canais'
        ];
    }

    /**
     * Retorna array de categorias
     */
    public static function getCategoriasOptions()
    {
        return [
            self::CATEGORIA_ADMINISTRATIVO => 'Administrativo',
            self::CATEGORIA_MARKETING => 'Marketing',
            self::CATEGORIA_SUPORTE => 'Suporte',
            self::CATEGORIA_EVENTOS => 'Eventos',
            self::CATEGORIA_POLITICO => 'Político',
            self::CATEGORIA_SOCIAL => 'Social'
        ];
    }

    /**
     * Retorna variáveis padrão disponíveis
     */
    public static function getVariaveisPadrao()
    {
        return [
            'cidadao' => [
                '{{nome}}' => 'Nome do cidadão',
                '{{email}}' => 'E-mail do cidadão',
                '{{telefone}}' => 'Telefone do cidadão',
                '{{bairro}}' => 'Bairro do cidadão',
                '{{idade}}' => 'Idade do cidadão',
                '{{cpf}}' => 'CPF do cidadão',
                '{{endereco}}' => 'Endereço do cidadão',
                '{{profissao}}' => 'Profissão do cidadão'
            ],
            'sistema' => [
                '{{data_hoje}}' => 'Data atual',
                '{{hora_atual}}' => 'Hora atual',
                '{{ano}}' => 'Ano atual',
                '{{mes}}' => 'Mês atual',
                '{{dia}}' => 'Dia atual',
                '{{gabinete_nome}}' => 'Nome do gabinete',
                '{{gabinete_endereco}}' => 'Endereço do gabinete',
                '{{gabinete_telefone}}' => 'Telefone do gabinete'
            ],
            'demanda' => [
                '{{demanda_titulo}}' => 'Título da demanda',
                '{{demanda_protocolo}}' => 'Protocolo da demanda',
                '{{demanda_status}}' => 'Status da demanda',
                '{{demanda_categoria}}' => 'Categoria da demanda',
                '{{demanda_prazo}}' => 'Prazo da demanda'
            ]
        ];
    }

    /**
     * Retorna todas as variáveis disponíveis
     */
    public function getTodasVariaveis()
    {
        $variaveis = self::getVariaveisPadrao();
        
        if ($this->variaveis_disponiveis) {
            $variaveis['personalizadas'] = $this->variaveis_disponiveis;
        }

        return $variaveis;
    }

    /**
     * Processa o template com dados específicos
     */
    public function processar($dados = [])
    {
        $conteudo = $this->conteudo;
        $assunto = $this->assunto;

        // Processar variáveis padrão
        $variaveisPadrao = self::getVariaveisPadrao();
        foreach ($variaveisPadrao as $grupo => $variaveis) {
            foreach ($variaveis as $placeholder => $descricao) {
                if (isset($dados[$grupo])) {
                    $valor = $this->extrairValor($dados[$grupo], $placeholder);
                    if ($valor !== null) {
                        $conteudo = str_replace($placeholder, $valor, $conteudo);
                        $assunto = str_replace($placeholder, $valor, $assunto);
                    }
                }
            }
        }

        // Processar variáveis personalizadas
        if (isset($dados['personalizadas'])) {
            foreach ($dados['personalizadas'] as $chave => $valor) {
                $placeholder = "{{" . $chave . "}}";
                $conteudo = str_replace($placeholder, $valor, $conteudo);
                $assunto = str_replace($placeholder, $valor, $assunto);
            }
        }

        return [
            'assunto' => $assunto,
            'conteudo' => $conteudo
        ];
    }

    /**
     * Extrai valor de objeto/array usando placeholder
     */
    private function extrairValor($dados, $placeholder)
    {
        $campo = trim($placeholder, '{}');
        
        if (is_object($dados)) {
            return $dados->$campo ?? null;
        } elseif (is_array($dados)) {
            return $dados[$campo] ?? null;
        }

        return null;
    }

    /**
     * Incrementa contador de uso
     */
    public function incrementarUso()
    {
        $this->increment('uso_contador');
    }

    /**
     * Valida se o template é compatível com o canal
     */
    public function isCompativelComCanal($canal)
    {
        return $this->canal === $canal || $this->canal === self::CANAL_TODOS;
    }

    /**
     * Retorna preview do template
     */
    public function getPreview($dados = [])
    {
        $resultado = $this->processar($dados);
        
        return [
            'assunto' => $resultado['assunto'],
            'conteudo' => $resultado['conteudo'],
            'caracteres' => strlen($resultado['conteudo']),
            'palavras' => str_word_count($resultado['conteudo']),
            'linhas' => substr_count($resultado['conteudo'], "\n") + 1
        ];
    }

    /**
     * Clona o template
     */
    public function clonar($novoNome = null)
    {
        $clone = $this->replicate();
        $clone->nome = $novoNome ?? $this->nome . ' (Cópia)';
        $clone->uso_contador = 0;
        $clone->save();

        return $clone;
    }

    /**
     * Retorna ícone para o tipo
     */
    public function getTipoIconeAttribute()
    {
        $icones = [
            self::TIPO_WELCOME => 'bi-hand-thumbs-up',
            self::TIPO_FOLLOW_UP => 'bi-arrow-repeat',
            self::TIPO_LEMBRETE => 'bi-alarm',
            self::TIPO_CONVITE => 'bi-calendar-event',
            self::TIPO_NEWSLETTER => 'bi-newspaper',
            self::TIPO_PESQUISA => 'bi-question-circle',
            self::TIPO_AGRADECIMENTO => 'bi-heart',
            self::TIPO_CONFIRMACAO => 'bi-check-circle',
            self::TIPO_CANCELAMENTO => 'bi-x-circle',
            self::TIPO_PERSONALIZADO => 'bi-gear'
        ];

        return $icones[$this->tipo] ?? 'bi-file-text';
    }

    /**
     * Retorna cor para o canal
     */
    public function getCanalCorAttribute()
    {
        $cores = [
            self::CANAL_EMAIL => 'text-primary',
            self::CANAL_SMS => 'text-success',
            self::CANAL_WHATSAPP => 'text-success',
            self::CANAL_PUSH => 'text-warning',
            self::CANAL_TODOS => 'text-info'
        ];

        return $cores[$this->canal] ?? 'text-secondary';
    }

    /**
     * Retorna estatísticas de uso
     */
    public function getEstatisticasUso()
    {
        return [
            'total_usos' => $this->uso_contador,
            'agendamentos_pendentes' => $this->agendamentos()->where('status', Agendamento::STATUS_AGENDADO)->count(),
            'agendamentos_enviados' => $this->agendamentos()->where('status', Agendamento::STATUS_ENVIADO)->count(),
            'taxa_sucesso' => $this->calcularTaxaSucesso()
        ];
    }

    /**
     * Calcula taxa de sucesso do template
     */
    private function calcularTaxaSucesso()
    {
        $total = $this->agendamentos()->count();
        if ($total === 0) {
            return 0;
        }

        $sucessos = $this->agendamentos()
            ->whereIn('status', [Agendamento::STATUS_ENTREGUE, Agendamento::STATUS_LIDO, Agendamento::STATUS_RESPONDIDO])
            ->count();

        return round(($sucessos / $total) * 100, 2);
    }
}
