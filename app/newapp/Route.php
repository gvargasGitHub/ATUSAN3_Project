<?php

namespace App;

use Atusan\Route\Route;

Route::get('/', Modules\modAcceso::class);
Route::get('login', Modules\modAcceso::class);

Route::ajax('login', Modules\modAcceso::class, 'login');

Route::middleware('auth', function () {
  // Servicios
  Route::ajax('keepAlive', Services\KeepAliveSessionService::class, 'keepAlive');
  Route::ajax('close', Services\CloseSessionService::class, 'close');
}, '/login');
