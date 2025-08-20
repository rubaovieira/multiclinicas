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
        Schema::create('service_medicine_times', function (Blueprint $table) {
            // Coluna de ID do tipo UUID
            $table->uuid('id')->primary();
            
            // Coluna de ID de serviço de remédio, que referencia a tabela service_medicines
            $table->uuid('service_medicine_id');
            
            // Coluna de tempo
            $table->text('time');
            
            // Coluna ativa, com valor padrão 'true'
            $table->boolean('active')->default(true);
            
            // Chave estrangeira para a tabela service_medicines
            $table->foreign('service_medicine_id')->references('id')->on('service_medicines')->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_medicine_times');
    }
};
