<?php

use App\Http\Controllers\Auth\AuthController;

Route::get('/login/{slug?}', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login/{slug?}', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);



Route::any('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/users', [AuthController::class, 'list'])->name('users');

Route::any('/delete-users/{id}', [AuthController::class, 'destroy'])->middleware(['auth'])->name('delete-users');    
Route::any('activate-users/{id}', [AuthController::class, 'activate'])->middleware(['auth'])->name('activate-users');


Route::get('/edit-users/{id}', [AuthController::class, 'show'])->middleware(['auth'])->name('edit-users');   
Route::post('/edit-users/{id}', [AuthController::class, 'update'])->middleware(['auth'])->name('edit-users');
 
Route::get('/schedule/{id}', [AuthController::class, 'schedule'])->middleware(['auth'])->name('schedule');
Route::post('/schedule_update/{id}', [AuthController::class, 'schedule_update'])->middleware(['auth'])->name('schedule_update');
 

Route::get('/agenda/buscarhorarios', [AuthController::class, 'buscarhorarios'])->middleware(['auth'])->name('buscar.horarios'); 
