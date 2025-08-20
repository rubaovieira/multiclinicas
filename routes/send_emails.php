<?php

use App\Http\Controllers\SendEmails\SendEmailsController;  
 
Route::post('/send-email', [SendEmailsController::class, 'sendEmail'])->name('send-email');  
Route::post('/verificar-email', [SendEmailsController::class, 'verifyEmail'])->name('verificar-email');  
