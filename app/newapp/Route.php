<?php

namespace App;

use App\Modules\modAcceso;
use App\Services\AppServices;
use Atusan\Route\Route;

Route::get('/', modAcceso::class);
Route::get('/login', modAcceso::class);

Route::ajax('/login', modAcceso::class, 'login');

Route::middleware('auth', function () {
  // Servicios
  Route::ajax('/keepAlive', AppServices::class, 'keepAlive');
  Route::ajax('/close', AppServices::class, 'close');
}, '/login');
