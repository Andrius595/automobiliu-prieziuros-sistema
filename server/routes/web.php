<?php

use App\Models\Appointment;
use App\Models\Car;
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

Route::get('test', function () {
    $car1 = Car::where('vin', 'YV1FWA8V4B1234567')->first();
    $appointment = Appointment::where('car_id', $car1->id)->first();
    $appointment->completed_at = null;

    $car = $appointment->car;
    $records = $appointment->records;

    $hash_string = $car->vin . '_' . $appointment->current_mileage . '_' . $appointment->completed_at . '_' . count($records);
    foreach ($records as $record) {
        $hash_string .= '_' . $record->short_description;
    }

    $hashed = hash('sha256', $hash_string);
    dump($hashed);
    $a = 'YV1FWA8V4B1234567_1_2024-05-01 17:34:28_1_Padangų keitimas';
    $hashed = hash('sha256', $a);

    dump($hashed);
    dump($hash_string);
    dd($a);
});

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});



