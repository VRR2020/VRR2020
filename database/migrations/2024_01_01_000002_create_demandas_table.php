<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();
            
            // Dados básicos da demanda
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('tipo', ['individual', 'coletiva'])->default('individual');
            $table->string('categoria', 100); // infraestrutura, saude, educacao, etc.
            $table->string('subcategoria', 100)->nullable();
            $table->enum('urgencia', ['baixa', 'media', 'alta', 'critica'])->default('media');
            $table->enum('status', ['aberta', 'em_andamento', 'pendente', 'resolvida', 'cancelada'])->default('aberta');
            
            // Relacionamentos
            $table->unsignedBigInteger('cidadao_id');
            $table->unsignedBigInteger('responsavel_id')->nullable(); // Usuário responsável
            
            // Localização
            $table->string('bairro', 100);
            $table->text('endereco_referencia')->nullable();
            
            // Controle e gestão
            $table->string('protocolo', 20)->unique(); // Protocolo único da demanda
            $table->date('data_prazo')->nullable();
            $table->date('data_resolucao')->nullable();
            $table->text('solucao_aplicada')->nullable();
            $table->decimal('custo_estimado', 10, 2)->nullable();
            
            // Observações e anexos
            $table->text('observacoes_internas')->nullable();
            $table->json('anexos')->nullable(); // Array de arquivos anexados
            $table->json('tags')->nullable(); // Tags para classificação
            
            // Timestamps e soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Chaves estrangeiras
            $table->foreign('cidadao_id')->references('id')->on('cidadaos')->onDelete('cascade');
            $table->foreign('responsavel_id')->references('id')->on('users')->onDelete('set null');
            
            // Índices para performance
            $table->index(['status']);
            $table->index(['categoria']);
            $table->index(['urgencia']);
            $table->index(['bairro']);
            $table->index(['data_prazo']);
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
        Schema::dropIfExists('demandas');
    }
}
