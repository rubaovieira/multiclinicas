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
            // Remove a chave estrangeira
            $table->dropForeign(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     
    }
};
