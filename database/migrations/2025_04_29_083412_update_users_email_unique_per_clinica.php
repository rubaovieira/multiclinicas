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
            // Remove a unique key existente
            $table->dropUnique('users_email_unique');

            // Cria a chave única composta
            $table->unique(['email', 'clinica_id'], 'users_email_clinica_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove a chave composta
            $table->dropUnique('users_email_clinica_unique');

            // Restaura a unique apenas no email
            $table->unique('email', 'users_email_unique');
        });
    }
};
