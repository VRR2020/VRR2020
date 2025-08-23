<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCidadaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cidadaos', function (Blueprint $table) {
            $table->id();
            
            // Dados pessoais básicos
            $table->string('nome');
            $table->string('cpf', 14)->unique();
            $table->string('email')->unique();
            $table->string('telefone', 20);
            $table->string('bairro', 100);
            $table->text('endereco')->nullable();
            $table->integer('idade')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('profissao', 100)->nullable();
            $table->string('renda_familiar', 50)->nullable();
            
            // Dados políticos e de engajamento
            $table->json('interesses_politicos')->nullable(); // Array de temas de interesse
            $table->json('tags')->nullable(); // Tags de segmentação
            $table->enum('status', ['lead', 'engajado', 'ativo', 'apoiador', 'inativo'])->default('lead');
            $table->enum('nivel_engajamento', ['baixo', 'medio', 'alto'])->default('baixo');
            $table->string('origem_cadastro', 100)->nullable(); // Como chegou ao gabinete
            
            // Observações e redes sociais
            $table->text('observacoes')->nullable();
            $table->json('redes_sociais')->nullable(); // Links para perfis sociais
            
            // Timestamps e soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para performance
            $table->index(['bairro']);
            $table->index(['status']);
            $table->index(['nivel_engajamento']);
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
        Schema::dropIfExists('cidadaos');
    }
}
