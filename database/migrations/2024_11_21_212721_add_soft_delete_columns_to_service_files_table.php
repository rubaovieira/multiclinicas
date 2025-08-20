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
        Schema::table('service_files', function (Blueprint $table) {
            $table->softDeletes(); // Cria a coluna `deleted_at` para soft delete
            $table->uuid('deleted_by')->nullable(); // Cria a coluna `deleted_by` como UUID e permite valores nulos
            $table->foreign('deleted_by') // Define a chave estrangeira
                ->references('id')
                ->on('users')
                ->onDelete('set null'); // Define a ação ao excluir o usuário
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_files', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Remove a coluna `deleted_at`
            $table->dropForeign(['deleted_by']); // Remove a chave estrangeira
            $table->dropColumn('deleted_by'); // Remove a coluna `deleted_by`
        });
    }
};
