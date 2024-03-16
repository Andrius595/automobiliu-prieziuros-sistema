<?php

use App\Http\Controllers\AuthController;
use App\Models\Appointment;
use App\Services\LambdaService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::post('test', function() {
   $lambdaService = new LambdaService();

   $appointment = Appointment::find(1);





    dd();
});


