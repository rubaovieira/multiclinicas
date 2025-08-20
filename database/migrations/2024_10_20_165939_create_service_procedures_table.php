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
        Schema::create('service_procedures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('procedure_id'); // UUID para referenciar a tabela procedures
            $table->uuid('service_id'); // UUID para referenciar a tabela services
            $table->text('observation')->nullable(); // Coluna para observações, pode ser nula

            // Definindo a chave estrangeira para a tabela procedures
            $table->foreign('procedure_id')->references('id')->on('procedures')->onDelete('cascade');

            // Definindo a chave estrangeira para a tabela services
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');

            $table->timestamps(); // Adiciona created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_procedures');
    }
};
