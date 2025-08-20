<?php

use App\Http\Controllers\Stocks\StocksController;

Route::get('/controle_estoque', [StocksController::class, 'index'])->middleware(['auth'])->name('controle_estoque');

Route::get('/saidas', [StocksController::class, 'index_exit'])->middleware(['auth'])->name('saidas');
Route::get('/entradas', [StocksController::class, 'index_entry'])->middleware(['auth'])->name('entradas');
Route::get('/produtos', [StocksController::class, 'index_product'])->middleware(['auth'])->name('produtos');

Route::get('/product-new', [StocksController::class, 'create'])->middleware(['auth'])->name('product-new'); 
Route::post('/product-create', [StocksController::class, 'create_new'])->middleware(['auth'])->name('product-create'); 

Route::get('/product-exit', [StocksController::class, 'create_exit'])->middleware(['auth'])->name('product-exit'); 
Route::post('/product-create-exit', [StocksController::class, 'create_new_exit'])->middleware(['auth'])->name('product-create-exit'); 

Route::get('/product-entry', [StocksController::class, 'create_entry'])->middleware(['auth'])->name('product-entry'); 
Route::post('/product-create-entry', [StocksController::class, 'create_new_entry'])->middleware(['auth'])->name('product-create-entry'); 


Route::get('/product-edit/{id}', [StocksController::class, 'edit'])->middleware(['auth'])->name('product-edit');


Route::any('/delete-product/{id}', [StocksController::class, 'destroy'])->middleware(['auth'])->name('delete-product');   
Route::any('activate-product/{id}', [StocksController::class, 'activate'])->middleware(['auth'])->name('activate-product');

Route::any('/delete-inventory_controls/{id}', [StocksController::class, 'destroy_inventory_controls'])->middleware(['auth'])->name('delete-inventory_controls');   
Route::any('activate-inventory_controls/{id}', [StocksController::class, 'activate_inventory_controls'])->middleware(['auth'])->name('activate-inventory_controls');
