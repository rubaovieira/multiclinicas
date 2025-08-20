<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_medicines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id'); 
            $table->text('medicamento'); // Coluna para o medicamento
            $table->text('observation')->nullable(); // Coluna para observações, pode ser nula
            $table->uuid('created_by'); // UUID para o usuário que criou
            $table->boolean('active')->default(true); // Coluna para indicar se está ativo, padrão é true

            // Definindo a chave estrangeira para a tabela services
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');

            // Definindo a chave estrangeira para a tabela users
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps(); // Adiciona created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_medicines');
    }
};
