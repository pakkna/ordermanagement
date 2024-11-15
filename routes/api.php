<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\ApiAuthController;
use App\Http\Controllers\Api\V1\Orders\OrdersController;
use App\Http\Controllers\Api\V1\Products\ProductsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth api
Route::group(['middleware' => 'api'], function () {

    Route::post('user_registration', [ApiAuthController::class, 'registration']);
    Route::post('user_login', [ApiAuthController::class, 'user_login']);
    Route::get('logout', [ApiAuthController::class, 'logout']);
    Route::get('refresh', [ApiAuthController::class, 'refresh']);
    Route::get('userinfo', [ApiAuthController::class, 'userInfo']);

    Route::prefix('product')->group(function () {
        // List all products publicly
        Route::get('/list', [ProductsController::class, 'index'])->withoutMiddleware('api');

        //only admin can store and update
        Route::post('/store', [ProductsController::class, 'store']);  // Product store
        Route::post('/update/{id}', [ProductsController::class, 'update']);  // Product update
    });

    Route::prefix('order')->group(function () {
        // place order with products
        Route::post('/place', [OrdersController::class, 'placeOrder']);
        //user order history
        Route::get('/history', [OrdersController::class, 'orderHistory']);
    });
});
