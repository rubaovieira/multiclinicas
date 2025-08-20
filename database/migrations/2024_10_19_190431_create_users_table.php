<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::table('users')->insert([
                'name' => 'ADMIN',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('123'), // Use bcrypt para hash da senha
                'perfil' => 'master',
                'observation' => 'acesso total',
                'active' => true,
                'created_by' => null, // Pode ser definido conforme necessário
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::table('users')->where('email', 'usuario@example.com')->delete(); 
        });
    }
};
