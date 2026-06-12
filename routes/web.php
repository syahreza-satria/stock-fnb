<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest / Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (Accessible to all roles: admin, staff, owner)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // INGREDIENTS ROUTES
    // View ingredients (all roles)
    Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
    
    // Modify stock/ingredients (admin & staff only)
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
        Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');
        Route::post('/ingredients/{ingredient}/adjust', [IngredientController::class, 'adjustStock'])->name('ingredients.adjust');
    });

    // RECIPE ROUTES
    // View recipes (all roles)
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    
    // Modify recipes (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
        Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
        Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    });

    // ORDERS / SALES SIMULATION
    // View sales simulation panel (all roles)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    
    // Process sale (admin & staff only)
    Route::post('/orders/sell', [OrderController::class, 'processSale'])->middleware('role:admin,staff')->name('orders.sell');

    // REPORTS (all roles)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // USER MANAGEMENT (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
