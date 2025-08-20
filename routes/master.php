<?php

use App\Http\Controllers\Master\AdminUserController;
use App\Http\Controllers\Master\ClinicaController;
use App\Http\Controllers\Master\MasterController;

Route::middleware(['auth', 'master'])->prefix('master')->group(function () {


    // Rotas para clínicas
    Route::get('/clinics', [ClinicaController::class, 'index'])->name('clinics');
    Route::get('/clinics/new', [ClinicaController::class, 'create'])->name('clinics.create');
    Route::post('/clinics', [ClinicaController::class, 'store'])->name('clinics.store');
    Route::get('/clinics/{id}/edit', [ClinicaController::class, 'edit'])->name('clinics.edit');
    Route::put('/clinics/{id}', [ClinicaController::class, 'update'])->name('clinics.update');
    Route::delete('/clinics/{id}', [ClinicaController::class, 'destroy'])->name('clinics.delete');
    Route::post('/clinics/{id}/restore', [ClinicaController::class, 'restore'])->name('clinics.restore');
    Route::post('/clinics/{id}/deactivate', [ClinicaController::class, 'deactivate'])->name('clinics.deactivate');
    Route::post('/clinics/{id}/activate', [ClinicaController::class, 'activate'])->name('clinics.activate');

    // Rotas para administradores
    Route::get('/admin-users', [AdminUserController::class, 'index'])->name('admin-users');
    Route::get('/admin-users/new', [AdminUserController::class, 'create'])->name('admin-users.create');
    Route::post('/admin-users', [AdminUserController::class, 'store'])->name('admin-users.store');
    Route::get('/admin-users/{id}/edit', [AdminUserController::class, 'edit'])->name('admin-users.edit');
    Route::put('/admin-users/{id}', [AdminUserController::class, 'update'])->name('admin-users.update');
    Route::delete('/admin-users/{id}', [AdminUserController::class, 'destroy'])->name('admin-users.destroy');
    Route::post('/admin-users/{id}/deactivate', [AdminUserController::class, 'deactivate'])->name('admin-users.deactivate');
    Route::post('/admin-users/{id}/activate', [AdminUserController::class, 'activate'])->name('admin-users.activate');
});





Route::get('/master/home', [MasterController::class, 'home'])->name('master.home')->middleware(['auth']);
Route::redirect('/master/home', '/master/clinics')->middleware(['auth']);
