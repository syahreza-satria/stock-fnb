<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiIngredientController;
use App\Http\Controllers\Api\ApiRecipeController;
use App\Http\Controllers\Api\ApiOrderController;
use App\Http\Controllers\Api\ApiReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/login', [ApiAuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Dashboard & Reports (admin, staff, owner)
    Route::get('/reports/dashboard', [ApiReportController::class, 'dashboard']);
    Route::get('/reports/movements', [ApiReportController::class, 'movements']);

    // Ingredients - View (all roles)
    Route::get('/ingredients', [ApiIngredientController::class, 'index']);
    Route::get('/ingredients/{ingredient}', [ApiIngredientController::class, 'show']);

    // Ingredients - Modify (admin, staff)
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/ingredients', [ApiIngredientController::class, 'store']);
        Route::put('/ingredients/{ingredient}', [ApiIngredientController::class, 'update']);
        Route::delete('/ingredients/{ingredient}', [ApiIngredientController::class, 'destroy']);
        Route::post('/ingredients/{ingredient}/adjust', [ApiIngredientController::class, 'adjustStock']);
    });

    // Recipes - View (all roles)
    Route::get('/recipes', [ApiRecipeController::class, 'index']);
    Route::get('/recipes/{recipe}', [ApiRecipeController::class, 'show']);

    // Recipes - Modify (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::post('/recipes', [ApiRecipeController::class, 'store']);
        Route::put('/recipes/{recipe}', [ApiRecipeController::class, 'update']);
        Route::delete('/recipes/{recipe}', [ApiRecipeController::class, 'destroy']);
    });

    // Sales - Process (admin, staff)
    Route::post('/orders/sell', [ApiOrderController::class, 'processSale'])->middleware('role:admin,staff');
});

