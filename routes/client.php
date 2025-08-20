<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Clients\ClientsController;

Route::get('/new-client', [ClientsController::class, 'new'])->middleware(['auth'])->name('new-client');


Route::post('/new-client', [ClientsController::class, 'create'])->middleware(['auth'])->name('new-client');

Route::get('/clients', [ClientsController::class, 'index'])->middleware(['auth'])->name('clients');

Route::get('/edit-client/{id}', [ClientsController::class, 'show'])->middleware(['auth'])->name('edit-client');
Route::post('/edit-client/{id}', [ClientsController::class, 'update'])->middleware(['auth'])->name('edit-client');

Route::any('/delete-client/{id}', [ClientsController::class, 'destroy'])->middleware(['auth'])->name('delete-client');
Route::any('activate-client/{id}', [ClientsController::class, 'activate'])->middleware(['auth'])->name('activate-client');


Route::get('/edit-client/{id}', [ClientsController::class, 'show'])->middleware(['auth'])->name('edit-client');


// O cliente pode se cadastrar no site
Route::get('/register-client/{slug?}', [ClientsController::class, 'showRegistrationForm'])->name('register-client');
Route::post('/register-client/{slug?}', [ClientsController::class, 'register']);

// Rota para o cliente solicitar uma nova consulta
Route::get('/client/appointments/solicitar', [\App\Http\Controllers\Client\AppointmentController::class, 'solicitarAtendimento'])->name('client.appointments.solicitar');
Route::post('/client/appointments/solicitar', [\App\Http\Controllers\Client\AppointmentController::class, 'store'])->name('client.appointments.store');






