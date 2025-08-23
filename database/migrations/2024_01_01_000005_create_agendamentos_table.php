<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            
            // Relacionamentos
            $table->unsignedBigInteger('cidadao_id');
            $table->unsignedBigInteger('usuario_id'); // Usuário responsável
            $table->unsignedBigInteger('template_id')->nullable(); // Template usado (opcional)
            
            // Tipo e configuração do agendamento
            $table->enum('tipo', [
                'lembrete', 'follow_up', 'convite', 'informativo', 'pesquisa', 'campanha'
            ])->default('follow_up');
            $table->enum('canal', ['email', 'sms', 'whatsapp', 'push', 'interno'])->default('email');
            $table->enum('prioridade', ['baixa', 'normal', 'alta', 'urgente'])->default('normal');
            
            // Conteúdo da mensagem
            $table->string('titulo');
            $table->longText('conteudo');
            
            // Controle de agendamento
            $table->datetime('data_agendamento');
            $table->datetime('data_envio')->nullable();
            $table->enum('status', [
                'agendado', 'enviando', 'enviado', 'entregue', 'lido', 'respondido', 'falhado', 'cancelado'
            ])->default('agendado');
            
            // Controle de tentativas
            $table->integer('tentativas')->default(0);
            $table->integer('max_tentativas')->default(3);
            $table->integer('intervalo_tentativas')->default(60); // minutos
            
            // Resultado e feedback
            $table->enum('resultado', ['positivo', 'neutro', 'negativo', 'sem_resposta'])->nullable();
            $table->text('observacoes')->nullable();
            
            // Dados dinâmicos e configurações
            $table->json('dados_dinamicos')->nullable(); // Variáveis específicas para este agendamento
            $table->json('configuracoes')->nullable(); // Configurações específicas do canal
            $table->json('tags')->nullable(); // Tags para organização e filtragem
            
            // Timestamps e soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Chaves estrangeiras
            $table->foreign('cidadao_id')->references('id')->on('cidadaos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('templates_messages')->onDelete('set null');
            
            // Índices para performance
            $table->index(['status']);
            $table->index(['canal']);
            $table->index(['tipo']);
            $table->index(['prioridade']);
            $table->index(['data_agendamento']);
            $table->index(['data_envio']);
            $table->index(['tentativas']);
            $table->index(['created_at']);
            
            // Índice composto para agendamentos pendentes
            $table->index(['status', 'data_agendamento']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agendamentos');
    }
}
