<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteracoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interacoes', function (Blueprint $table) {
            $table->id();
            
            // Relacionamentos
            $table->unsignedBigInteger('cidadao_id');
            $table->unsignedBigInteger('demanda_id')->nullable(); // Opcional
            $table->unsignedBigInteger('usuario_id'); // Usuário responsável pela interação
            
            // Tipo e canal de comunicação
            $table->enum('tipo', [
                'reuniao', 'telefonema', 'email', 'sms', 'whatsapp', 
                'evento', 'visita', 'carta', 'redes_sociais'
            ]);
            $table->enum('canal', [
                'presencial', 'telefonico', 'email', 'whatsapp', 'sms',
                'facebook', 'instagram', 'twitter', 'linkedin', 'site'
            ]);
            
            // Conteúdo da interação
            $table->string('assunto');
            $table->text('descricao');
            $table->enum('sentido', ['entrada', 'saida']); // entrada = cidadão contatou, saida = gabinete contatou
            
            // Status e agendamento
            $table->enum('status', ['agendada', 'realizada', 'cancelada', 'nao_realizada'])->default('realizada');
            $table->datetime('data_agendamento')->nullable();
            $table->datetime('data_realizacao')->nullable();
            $table->integer('duracao_minutos')->nullable();
            
            // Resultado e observações
            $table->enum('resultado', ['positivo', 'neutro', 'negativo', 'nao_atendeu', 'reagendado'])->nullable();
            $table->text('observacoes')->nullable();
            $table->json('anexos')->nullable(); // Array de arquivos anexados
            $table->json('tags')->nullable(); // Tags para classificação
            
            // Timestamps
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('cidadao_id')->references('id')->on('cidadaos')->onDelete('cascade');
            $table->foreign('demanda_id')->references('id')->on('demandas')->onDelete('set null');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índices para performance
            $table->index(['tipo']);
            $table->index(['canal']);
            $table->index(['sentido']);
            $table->index(['status']);
            $table->index(['data_agendamento']);
            $table->index(['data_realizacao']);
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
        Schema::dropIfExists('interacoes');
    }
}
