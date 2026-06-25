<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\UnitController;

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

    // WASTE / PEMBUANGAN ROUTES
    Route::get('/wastes', [WasteController::class, 'index'])->name('wastes.index');
    Route::post('/wastes', [WasteController::class, 'store'])->middleware('role:admin,staff')->name('wastes.store');

    // SUPPLIER ROUTES
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // PURCHASE ORDER ROUTES
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->middleware('role:admin,staff')->name('purchase-orders.create');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->middleware('role:admin,staff')->name('purchase-orders.store');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->middleware('role:admin,staff')->name('purchase-orders.receive');

    // OUTLET ROUTES
    Route::get('/outlets', [OutletController::class, 'index'])->name('outlets.index');
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/outlets', [OutletController::class, 'store'])->name('outlets.store');
        Route::put('/outlets/{outlet}', [OutletController::class, 'update'])->name('outlets.update');
        Route::delete('/outlets/{outlet}', [OutletController::class, 'destroy'])->name('outlets.destroy');
    });

    // UNIT & CONVERSION ROUTES
    Route::get('/units', [UnitController::class, 'index'])->name('units.index');
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/units', [UnitController::class, 'storeUnit'])->name('units.store');
        Route::post('/units/conversions', [UnitController::class, 'storeConversion'])->name('units.conversions.store');
        Route::delete('/units/conversions/{conversion}', [UnitController::class, 'destroyConversion'])->name('units.conversions.destroy');
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

    // REPORTS & ANALYTICS (all roles)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    
    // ADVANCED ANALYTICS ROUTES
    Route::get('/reports/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('reports.analytics');
    Route::post('/reports/analytics/pricing', [\App\Http\Controllers\AnalyticsController::class, 'updatePricing'])->middleware('role:admin,staff')->name('analytics.pricing.update');
    Route::post('/reports/analytics/stock-take', [\App\Http\Controllers\AnalyticsController::class, 'storeStockTake'])->middleware('role:admin,staff')->name('analytics.stock-take.store');

    // USER MANAGEMENT (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
