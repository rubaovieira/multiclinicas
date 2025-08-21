<?php

use Illuminate\Support\Facades\Route;

Route::get('/_ping', fn () => response('ok', 200));
