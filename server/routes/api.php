<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PublicCarHistoryController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ServiceCategoryController;
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
    $user = $request->user();
    $roles = $user->roles;

    return [
        ...$user->toArray(),
        'roles' => $roles->pluck('name')->toArray(),
    ];
});

Route::group(['prefix' => '/auth', 'middleware' => 'api'], static function() {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::group(['prefix' => '/auth', 'middleware' => ['api']], static function() {
    Route::get('/user', static function(Request $request) {
        $user = $request->user();

        if ($user) {
            $roles = $user->roles;
            return [
                ...$user->toArray(),
                'roles' => $roles->pluck('name')->toArray(),
            ];
        }

        return response()->json(null);
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::group(['middleware' => 'auth:api'], static function () {
    Route::apiResource('/cars', CarController::class);
    Route::group(['prefix' => '/cars'], static function() {
        Route::get('/vin/{vin}', [ServiceController::class, 'getCarByVin']);
        Route::get('/reminders/{reminder}', [ReminderController::class, 'show']);
        Route::put('/reminders/{reminder}', [ReminderController::class, 'update']);
        Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy']);
        Route::get('/{car}/history', [CarController::class, 'getCarHistory']);
        Route::get('/{car}/reminders', [ReminderController::class, 'getCarReminders']);
        Route::post('/{car}/reminders', [ReminderController::class, 'store']);
        Route::get('/{car}/public-urls', [CarController::class, 'getCarPublicUrls']);
        Route::post('/{car}/share-history', [CarController::class, 'shareCarHistory']);
        Route::delete('/{car}/delete-public-history', [CarController::class, 'deletePublicCarHistory']);
    });

    Route::apiResource('services', ServiceController::class);
    Route::group(['prefix' => 'services/{service}'], static function() {
        Route::post('/register', [ServiceController::class, 'registerForAppointment']);
        Route::group(['prefix' => '/employees'], static function() {
            Route::get('/', [ServiceController::class, 'getEmployees']);
            Route::post('/', [ServiceController::class, 'createEmployee']);
            Route::get('/{user}', [ServiceController::class, 'getEmployee']);
            Route::put('/{user}', [ServiceController::class, 'updateEmployee']);
            Route::delete('/{user}', [ServiceController::class, 'deleteEmployee']);
        });
    });
    Route::apiResource('appointments', AppointmentController::class);
    Route::post('appointments/{appointment}/write-review', [AppointmentController::class, 'writeReview']);
    Route::post('appointments/{appointment}/review', [AppointmentController::class, 'getReview']);
    Route::group(['prefix' => 'users'], static function() {
        Route::get('list-for-select', [UserController::class, 'indexForSelect']);
    });
    Route::apiResource('users', UserController::class);


    Route::group(['prefix' => '/user'], static function() {
        Route::post('/register-new-car', [UserController::class, 'registerNewCar']);
        Route::group(['prefix' => '/my-cars'], static function() {
            Route::get('/', [UserController::class, 'getMyCars']);
            Route::delete('/{car}/remove', [UserController::class, 'removeCar']);
            Route::post('/{car}/share', [UserController::class, 'shareCar']);
            Route::post('/{car}/transfer', [UserController::class, 'transferCar']);
        });
    });

    Route::group(['prefix' => '/service'], static function() {
        Route::get('registrations', [ServiceController::class, 'getRegistrations']);
        Route::post('registrations/{appointment}/confirm', [ServiceController::class, 'confirmRegistration']);
        Route::group(['prefix' => 'appointments'], static function() {
            Route::post('create-appointment', [ServiceController::class, 'createAppointment']);
            Route::get('active', [ServiceController::class, 'getActiveAppointments']);
            Route::get('completed', [ServiceController::class, 'getCompletedAppointments']);
            Route::post('{appointment}/complete', [ServiceController::class, 'completeAppointment']);
            Route::get('{appointment}', [ServiceController::class, 'getAppointment']);
            Route::get('/records/{record}', [ServiceController::class, 'getRecord']);
            Route::put('/records/{record}', [ServiceController::class, 'updateRecord']);
            Route::delete('/records/{record}', [ServiceController::class, 'deleteRecord']);
            Route::group(['prefix' => '{appointment}/records'], static function() {
                Route::get('/', [ServiceController::class, 'getAppointmentRecords']);
                Route::post('/', [ServiceController::class, 'createRecord']);
            });
        });
    });

    Route::get('/cities/list-for-select', [CityController::class, 'indexForSelect']);
    Route::apiResource('cities', CityController::class);
    Route::get('/service-categories/list-for-select', [ServiceCategoryController::class, 'indexForSelect']);
    Route::apiResource('service-categories', ServiceCategoryController::class);
});

Route::group(['prefix' => '/public-history', 'middleware' => 'api'], static function() {
    Route::get('/{slug}', [PublicCarHistoryController::class, 'showBySlug']);
});

