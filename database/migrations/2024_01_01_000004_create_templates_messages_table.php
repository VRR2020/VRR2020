<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates_messages', function (Blueprint $table) {
            $table->id();
            
            // Informações básicas do template
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->enum('tipo', [
                'welcome', 'follow_up', 'lembrete', 'convite', 'newsletter', 
                'pesquisa', 'agradecimento', 'confirmacao', 'cancelamento', 'personalizado'
            ])->default('personalizado');
            $table->enum('canal', ['email', 'sms', 'whatsapp', 'push', 'todos'])->default('todos');
            $table->enum('categoria', [
                'administrativo', 'marketing', 'suporte', 'eventos', 'politico', 'social'
            ])->default('administrativo');
            
            // Conteúdo do template
            $table->string('assunto')->nullable(); // Para emails principalmente
            $table->longText('conteudo');
            
            // Configurações e variáveis
            $table->json('variaveis_disponiveis')->nullable(); // Variáveis personalizadas
            $table->json('configuracoes')->nullable(); // Configurações específicas do canal
            $table->json('tags')->nullable(); // Tags para organização
            
            // Controle e estatísticas
            $table->boolean('ativo')->default(true);
            $table->integer('uso_contador')->default(0); // Quantas vezes foi usado
            $table->unsignedBigInteger('usuario_id'); // Criador do template
            
            // Timestamps e soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Chaves estrangeiras
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índices para performance
            $table->index(['tipo']);
            $table->index(['canal']);
            $table->index(['categoria']);
            $table->index(['ativo']);
            $table->index(['uso_contador']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('templates_messages');
    }
}
