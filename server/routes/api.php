<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/auth', 'middleware' => 'api'], static function() {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::group(['prefix' => '/auth', 'middleware' => ['api', 'auth:api']], static function() {
    Route::get('/user', function(Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('/cars', CarController::class);
    Route::group(['prefix' => '/cars'], function() {
        Route::get('/vin/{vin}', [ServiceController::class, 'getCarByVin']);
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

    Route::group(['prefix' => '/user', 'middleware' => 'auth:api'], function() {
        Route::post('/register-new-car', [UserController::class, 'registerNewCar']);
        Route::get('/my-cars', [UserController::class, 'getMyCars']);
    });

    Route::group(['prefix' => '/service', 'middleware' => 'auth:api'], function() {
        Route::get('registrations', [ServiceController::class, 'getRegistrations']);
        Route::post('registrations/{appointment}/confirm', [ServiceController::class, 'confirmRegistration']);
        Route::put('update-service', [ServiceController::class, 'updateService']);
        Route::group(['prefix' => 'appointments'], function() {
            Route::post('create-appointment', [ServiceController::class, 'createAppointment']);
            Route::get('active', [ServiceController::class, 'getActiveAppointments']);
            Route::get('completed', [AppointmentController::class, 'getCompletedAppointments']);
            Route::post('{appointment}/complete', [ServiceController::class, 'completeAppointment']);
            Route::get('{appointment}', [ServiceController::class, 'getAppointment']);
            Route::get('/records/{record}', [ServiceController::class, 'getRecord']);
            Route::put('/records/{record}', [ServiceController::class, 'updateRecord']);
            Route::delete('/records/{record}', [ServiceController::class, 'deleteRecord']);
            Route::group(['prefix' => '{appointment}/records'], function() {
                Route::get('/', [ServiceController::class, 'getAppointmentRecords']);
                Route::post('/', [ServiceController::class, 'createRecord']);
            });

        });
        Route::group(['prefix' => 'employees'], function() {
            Route::get('/', [ServiceController::class, 'getEmployees']);
            Route::post('/', [ServiceController::class, 'createEmployee']);
            Route::put('/{employee}', [ServiceController::class, 'updateEmployee']);
            Route::delete('/{employee}', [ServiceController::class, 'deleteEmployee']);
        });
    });
});

