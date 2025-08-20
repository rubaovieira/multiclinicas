<?php

use App\Http\Controllers\HealthPlans\HealthPlansController; 

Route::get('/new-health_plan', [HealthPlansController::class, 'new'])->middleware(['auth'])->name('new-health_plan');
     
Route::post('/new-health_plan', [HealthPlansController::class, 'create'])->middleware(['auth'])->name('new-health_plan');

Route::get('/health_plans', [HealthPlansController::class, 'index'])->middleware(['auth'])->name('health_plans');
    
Route::get('/edit-health_plan/{id}', [HealthPlansController::class, 'show'])->middleware(['auth'])->name('edit-health_plan');  
Route::post('/edit-health_plan/{id}', [HealthPlansController::class, 'update'])->middleware(['auth'])->name('edit-health_plan');

Route::any('/delete-health_plan/{id}', [HealthPlansController::class, 'destroy'])->middleware(['auth'])->name('delete-health_plan');   
Route::any('activate-health_plan/{id}', [HealthPlansController::class, 'activate'])->middleware(['auth'])->name('activate-health_plan');

 





