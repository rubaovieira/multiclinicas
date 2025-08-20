<?php

use App\Http\Controllers\Services\ServicesController;  

Route::get('/services', [ServicesController::class, 'index'])->middleware(['auth'])->name('services');
Route::get('/service-client/{id}', [ServicesController::class, 'show'])->middleware(['auth'])->name('service-client');  
Route::post('/service-medicine-item', [ServicesController::class, 'service_medicine_add'])->middleware(['auth'])->name('service-medicine-item');  
Route::post('/service-procedure-item', [ServicesController::class, 'service_procedure_add'])->middleware(['auth'])->name('service-procedure-item');
Route::get('/service-history', [ServicesController::class, 'service_history'])->middleware(['auth'])->name('service-history'); 
Route::get('/service-new', [ServicesController::class, 'create'])->middleware(['auth'])->name('service-new'); 
Route::post('/service', [ServicesController::class, 'create_new'])->middleware(['auth'])->name('service');
Route::post('/service-finish', [ServicesController::class, 'finish'])->middleware(['auth'])->name('service-finish');
Route::post('/service-open', [ServicesController::class, 'open'])->middleware(['auth'])->name('service-open');
Route::delete('/service_items/{id}', [ServicesController::class, 'destroy'])->name('service_items.destroy');
Route::post('/service-medicine-minister', [ServicesController::class, 'service_medicine_minister'])->name('service-medicine-minister');
Route::post('/service-evolution', [ServicesController::class, 'service_evolution_add'])->middleware(['auth'])->name('service-evolution');
Route::post('/service-attachment', [ServicesController::class, 'service_attachment'])->middleware(['auth'])->name('service-attachment');
Route::delete('/service-attachment-revert', [ServicesController::class, 'delete_attachment'])->middleware(['auth'])->name('service-attachment-revert');
Route::get('/print-location-occurrences/{id}', [ServicesController::class, 'print'])->middleware(['auth'])->name('print-location-occurrences');  
Route::post('/qtd_evolution', [ServicesController::class, 'qtd_evolution'])->middleware(['auth'])->name('qtd_evolution');


