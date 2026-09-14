<?php

namespace App;

use Atusan\Route\Route;

Route::get('/', Modules\Home::class);

Route::middleware('auth', function () {
  // Servicios
  Route::ajax('keepAlive', Services\SessionKeepAlive::class, 'keepAlive');
  Route::ajax('close', Services\SessionClose::class, 'close');
}, '/');

// Backend API Health Check
Route::get('health', \App\Services\Health::class);