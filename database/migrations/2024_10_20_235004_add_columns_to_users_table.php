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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf')->nullable(); // CPF
            $table->string('address')->nullable(); // Endereço
            $table->string('telephone')->nullable(); // Telefone
            $table->text('advice')->nullable(); // Conselho 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) { 
            $table->dropColumn(['cpf', 'address', 'telephone', 'advice', 'created_by']);
        });
    }
};
