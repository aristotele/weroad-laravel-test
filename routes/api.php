<?php

use App\Http\Controllers\Api\V1\TravelController;
use App\Http\Controllers\Api\V1\TravelTourController;
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

Route::group([
    'prefix' => 'v1',
    'as' => 'api.v1.',
], function () {
    // Public routes
    Route::get('/travels', [TravelController::class, 'index'])->name('travels.index');
    Route::get('/travels/{travelSlug}/tours', [TravelTourController::class, 'index'])->name('travels.tours.index');

    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // Private routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/travels', [TravelController::class, 'store'])->name('travels.store');
        Route::patch('/travels/{travel}', [TravelController::class, 'update'])->name('travels.update');

        Route::post('/travels/{travelId}/tours', [TravelTourController::class, 'store'])->name('travels.tours.store');

        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
});
