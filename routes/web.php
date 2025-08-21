<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\ReceitaController;

/**
 * Includes das rotas modulares
 */
require __DIR__.'/auth.php';
require __DIR__.'/client.php';
require __DIR__.'/service.php';
require __DIR__.'/procedures.php';
require __DIR__.'/health_plans.php';
require __DIR__.'/send_emails.php';
require __DIR__.'/stock.php';
require __DIR__.'/master.php';

/**
 * Rotas principais
 */
Route::get('/', [DashboardController::class, 'dashboard'])
    ->middleware(['auth'])
    ->name('home');

Route::get('/home', [DashboardController::class, 'dashboard'])
    ->middleware(['auth', 'verificarUsuarioAtivoParaLogin'])
    ->name('home');

/** Consultas do médico */
Route::middleware(['auth'])->get('/appointments', [AppointmentController::class, 'index'])
    ->name('appointments');

/** Consultas do cliente */
Route::middleware(['auth'])->prefix('client')->group(function () {
    Route::get('/appointments', [ClientAppointmentController::class, 'index'])->name('client.appointments');
    Route::get('/appointments/solicitar', [ClientAppointmentController::class, 'solicitarAtendimento'])->name('client.appointments.solicitar');
    Route::post('/appointments', [ClientAppointmentController::class, 'store'])->name('client.appointments.store');
    Route::get('/appointments/available-times', [ClientAppointmentController::class, 'getAvailableTimes'])->name('client.appointments.available-times');
});

/** Admin */
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

/** Receitas */
Route::post('/receita', [ReceitaController::class, 'store'])->name('receita.store');
Route::delete('/receita/{id}', [ReceitaController::class, 'destroy'])->name('receita.destroy');
Route::get('/receita/print/{id}', [ReceitaController::class, 'print'])->name('receita.print');
