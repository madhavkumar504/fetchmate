<?php

use App\Http\Controllers\ApiTesterController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return "Welcome test! 😎";
// });

// View to show the tester frontend
Route::view('/tester', 'api-tester');

// Show frontend from controller (optional)
Route::get('/', [ApiTesterController::class, 'index']);

// Proxy route to send API requests (important!)
Route::post('/api/send-request', [ApiTesterController::class, 'sendRequest']);
