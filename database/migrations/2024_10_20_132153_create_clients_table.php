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
        Schema::create('clients', function (Blueprint $table) {
            // Coluna UUID para identificação do cliente
            $table->uuid('id')->primary();
            
            // Informações do cliente
            $table->string('name');
            $table->string('telephone');
            $table->string('caregiver_responsible')->nullable(); // Responsável, pode ser opcional
            $table->string('address')->nullable();
            $table->date('date_birth')->nullable(); // Data de nascimento
            $table->string('cpf')->unique(); // CPF é único

            // Diagnóstico e plano de saúde
            $table->string('diagnosis')->nullable();
            $table->uuid('health_plan_id'); // Relacionamento com health_plans

            // Relacionamento com a tabela users para created_by
            $table->uuid('created_by'); // Relacionamento com users
            
            // Colunas de timestamps
            $table->timestamps();

            // Definindo as relações (foreign keys)
            $table->foreign('health_plan_id')->references('id')->on('health_plans')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
