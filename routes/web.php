<?php

use Illuminate\Support\Facades\Route;
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

// Rotas de autenticação
require __DIR__ . '/auth.php';

// Rotas de clients
require __DIR__ . '/client.php';

// Rotas de service
require __DIR__ . '/service.php';

// Rotas de procedures
require __DIR__ . '/procedures.php';

// Rotas de health_plans
require __DIR__ . '/health_plans.php';

// Rotas de medicines
require __DIR__ . '/send_emails.php';

// Rotas de medicines
require __DIR__ . '/stock.php';

// Rotas de master
require __DIR__ . '/master.php';




Route::get('/', [DashboardController::class, 'dashboard'])->middleware(['auth'])->name('home');
Route::get('/home', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verificarUsuarioAtivoParaLogin'])->name('home');

// require __DIR__ . '/master.php';

// Rota para consultas do medico
Route::middleware(['auth'])->get('/appointments', [AppointmentController::class, 'index'])->name('appointments');

// Rota para consultas do cliente
Route::middleware(['auth'])->prefix('client')->group(function () {
    Route::get('/appointments', [\App\Http\Controllers\Client\AppointmentController::class, 'index'])->name('client.appointments');
    Route::get('/appointments/solicitar', [\App\Http\Controllers\Client\AppointmentController::class, 'solicitarAtendimento'])->name('client.appointments.solicitar');
    Route::post('/appointments', [\App\Http\Controllers\Client\AppointmentController::class, 'store'])->name('client.appointments.store');
    Route::get('/appointments/available-times', [\App\Http\Controllers\Client\AppointmentController::class, 'getAvailableTimes'])->name('client.appointments.available-times');
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


// Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

// Rotas para Receitas
Route::post('/receita', [ReceitaController::class, 'store'])->name('receita.store');
Route::delete('/receita/{id}', [ReceitaController::class, 'destroy'])->name('receita.destroy');
Route::get('/receita/print/{id}', [ReceitaController::class, 'print'])->name('receita.print');
