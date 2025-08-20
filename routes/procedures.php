<?php

use App\Http\Controllers\Procedures\ProceduresController; 

Route::get('/new-procedure', [ProceduresController::class, 'new'])->middleware(['auth'])->name('new-procedure');
     
Route::post('/new-procedure', [ProceduresController::class, 'create'])->middleware(['auth'])->name('new-procedure');

Route::get('/procedures', [ProceduresController::class, 'index'])->middleware(['auth'])->name('procedures');
    
Route::get('/edit-procedure/{id}', [ProceduresController::class, 'show'])->middleware(['auth'])->name('edit-procedure');  
Route::post('/edit-procedure/{id}', [ProceduresController::class, 'update'])->middleware(['auth'])->name('edit-procedure');

Route::any('/delete-procedure/{id}', [ProceduresController::class, 'destroy'])->middleware(['auth'])->name('delete-procedure');   
Route::any('activate-procedure/{id}', [ProceduresController::class, 'activate'])->middleware(['auth'])->name('activate-procedure');

 





