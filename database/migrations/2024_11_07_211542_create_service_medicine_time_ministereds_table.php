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
        Schema::create('service_medicine_time_ministereds', function (Blueprint $table) {
            // Coluna de ID do tipo UUID
            $table->uuid('id')->primary();
            
            // Coluna que referencia a tabela service_medicine_times
            $table->uuid('service_medicine_time_id');
             
            
            // Coluna para quem criou o registro
            $table->uuid('created_by');
            
            // Coluna de descrição
            $table->text('description')->nullable();
            
            // Coluna late, com valor padrão 'false'
            $table->boolean('late')->default(false);
            
            // Coluna active
            $table->boolean('active')->default(true);
            
            // Definir nome curto para a chave estrangeira
            $table->foreign('service_medicine_time_id', 'service_medicine_time_id_foreign')
            ->references('id')->on('service_medicine_times')
            ->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_medicine_time_ministereds');
    }
};
