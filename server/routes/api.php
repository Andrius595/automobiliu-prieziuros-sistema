<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/cars', CarController::class);
Route::group(['prefix' => '/cars'], function() {
    Route::post('/{car}/share-car', [CarController::class, 'shareCar']);
    Route::post('/{car}/transfer-car', [CarController::class, 'transferCar']);
    Route::post('/{car}/history', [CarController::class, 'getCarHistory']);
    Route::get('/{car}/reminders', [ReminderController::class, 'getCarReminders']);
    Route::post('/{car}/reminders', [ReminderController::class, 'createCarReminder']);
    Route::put('/{car}/reminders/{reminder}', [ReminderController::class, 'updateCarReminder']);
    Route::delete('/{car}/reminders/{reminder}', [ReminderController::class, 'deleteCarReminder']);
});
Route::apiResource('services', ServiceController::class);
Route::apiResource('appointments', AppointmentController::class);
Route::apiResource('records', RecordController::class);
Route::apiResource('users', UserController::class);

Route::group(['prefix' => '/user', 'middleware' => 'auth:sanctum'], function() {
    Route::post('/register-new-car', [UserController::class, 'registerNewCar']);
    Route::get('/my-cars', [UserController::class, 'getMyCars']);
});

Route::group(['prefix' => '/service', 'middleware' => 'auth:Sanctum'], function() {
    Route::get('registrations', [ServiceController::class, 'getRegistrations']);
    Route::post('registrations/{appointment}/confirm', [ServiceController::class, 'confirmRegistration']);
    Route::post('register-car-without-appointment', [ServiceController::class, 'registerCarWithoutAppointment']);
    Route::put('update-service', [ServiceController::class, 'updateService']);
    Route::group(['prefix' => 'appointments'], function() {
        Route::post('active', [AppointmentController::class, 'getActiveAppointments']);
        Route::post('completed', [AppointmentController::class, 'getCompletedAppointments']);
        Route::post('{appointment}/complete', [AppointmentController::class, 'completeAppointment']);
        Route::get('{appointment}', [AppointmentController::class, 'getAppointment']);
        Route::group(['prefix' => '{appointment}/records'], function() {
            Route::post('/records', [RecordController::class, 'createRecord']);
            Route::put('/records/{record}', [RecordController::class, 'updateRecord']);
            Route::delete('/records/{record}', [RecordController::class, 'deleteRecord']);
        });
    });
    Route::group(['prefix' => 'employees'], function() {
        Route::get('/', [ServiceController::class, 'getEmployees']);
        Route::post('/', [ServiceController::class, 'createEmployee']);
        Route::put('/{employee}', [ServiceController::class, 'updateEmployee']);
        Route::delete('/{employee}', [ServiceController::class, 'deleteEmployee']);
    });
});
