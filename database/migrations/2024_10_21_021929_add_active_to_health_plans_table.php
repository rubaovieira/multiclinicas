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
        Schema::table('health_plans', function (Blueprint $table) {
            $table->boolean('active')->default(true); // Adiciona a coluna active com default true
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_plans', function (Blueprint $table) {
            $table->dropColumn('active'); // Remove a coluna active
        });
    }
};
