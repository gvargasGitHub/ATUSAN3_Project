<?php

namespace App;

use Atusan\Route\Route;

Route::get('/', Modules\Home::class);

// Backend API Health Check
Route::get('health', \App\Services\Health::class);