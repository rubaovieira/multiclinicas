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
        Schema::create('inventory_controls', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ID como UUID
            $table->uuid('product_id'); // UUID do produto
            $table->integer('qtd'); // Quantidade
            $table->boolean('active')->default(true);
            $table->uuid('service_medicine_time_ministered_id')->nullable(); // Pode ser nulo
            $table->uuid('created_by'); 
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_control');
    }
};
