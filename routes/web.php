<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Master\MasterController;
use App\Http\Controllers\Master\ClinicaController;
use App\Http\Controllers\Master\AdminUserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReceitaController;

/**
 * --- Rotas temporárias de diagnóstico (remover depois) ---
 */
Route::get('/_ping', fn () => 'ok '.app()->version());

Route::get('/__clear', function () {
    \Artisan::call('optimize:clear');
    return nl2br(e(\Artisan::output()));
});

Route::get('/_routes', function () {
    \Artisan::call('route:list');
    return response('<pre>'.e(\Artisan::output()).'</pre>');
});

/**
 * --- Bootstrap do admin (TEMPORÁRIO; remova depois de usar) ---
 */
Route::get('/bootstrap-admin', function () {
    $email = 'admin@multiclinicas.com.br'; // troque se quiser
    $senha = 'Trocar123!';

    $modelClass = class_exists(\App\Models\User::class)
        ? \App\Models\User::class
        : \App\User::class;

    $user = $modelClass::firstOrCreate(
        ['email' => $email],
        ['name' => 'Administrador', 'password' => Hash::make($senha)]
    );

    if (!$user->wasRecentlyCreated) {
        $user->password = Hash::make($senha);
        $user->save();
    }

    return "<pre>ADMIN PRONTO\nLogin: $email\nSenha: $senha\n(Remova a rota /bootstrap-admin depois!)</pre>";
});

/**
 * Includes de rotas
 */
require __DIR__ . '/auth.php';
require __DIR__ . '/client.php';
require __DIR__ . '/service.php';
require __DIR__ . '/procedures.php';
require __DIR__ . '/health_plans.php';
require __DIR__ . '/send_emails.php';
require __DIR__ . '/stock.php';
require __DIR__ . '/master.php';

/**
 * Suas rotas
 */
Route::get('/', [DashboardController::class, 'dashboard'])->middleware(['auth'])->name('home');
Route::get('/home', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verificarUsuarioAtivoParaLogin'])->name('home');

Route::middleware(['auth'])->get('/appointments', [AppointmentController::class, 'index'])->name('appointments');

Route::middleware(['auth'])->prefix('client')->group(function () {
    Route::get('/appointments', [ClientAppointmentController::class, 'index'])->name('client.appointments');
    Route::get('/appointments/solicitar', [ClientAppointmentController::class, 'solicitarAtendimento'])->name('client.appointments.solicitar');
    Route::post('/appointments', [ClientAppointmentController::class, 'store'])->name('client.appointments.store');
    Route::get('/appointments/available-times', [ClientAppointmentController::class, 'getAvailableTimes'])->name('client.appointments.available-times');
});

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('admin.appointments');
    Route::get('/appointments/new', [AdminAppointmentController::class, 'create'])->name('admin.new-appointment');
    Route::post('/appointments', [AdminAppointmentController::class, 'store'])->name('admin.appointments.store');
    Route::get('/appointments/{id}/edit', [AdminAppointmentController::class, 'edit'])->name('admin.edit-appointment');
    Route::put('/appointments/{id}', [AdminAppointmentController::class, 'update'])->name('admin.appointments.update');
    Route::delete('/appointments/{id}', [AdminAppointmentController::class, 'destroy'])->name('admin.delete-appointment');
    Route::get('/appointments/{id}', [AdminAppointmentController::class, 'view'])->name('admin.view-appointment');
    Route::get('/appointments/{id}/check-availability', [AdminAppointmentController::class, 'checkAvailability'])->name('admin.appointments.check-availability');
});

Route::post('/receita', [ReceitaController::class, 'store'])->name('receita.store');
Route::delete('/receita/{id}', [ReceitaController::class, 'destroy'])->name('receita.destroy');
Route::get('/receita/print/{id}', [ReceitaController::class, 'print'])->name('receita.print');