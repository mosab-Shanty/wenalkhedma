<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AiAssistantController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminUserController;

/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/map', [ServiceController::class, 'map'])->name('services.map');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::post('/ai/ask', [AiAssistantController::class, 'ask'])->name('ai.ask');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/auth/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

    // My Services & Service Management
    Route::get('/my-services', [ServiceController::class, 'myServices'])->name('services.mine');
    Route::get('/services-create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services-store', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services-store', fn() => redirect()->route('services.create'));
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

/*
|--------------------------------------------------------------------------
| Admin & Moderator Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:admin,moderator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Services Approval
    Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::patch('/services/{service}/approve', [AdminServiceController::class, 'approve'])->name('services.approve');
    Route::patch('/services/{service}/reject', [AdminServiceController::class, 'reject'])->name('services.reject');
    Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

    // Reports Management
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{report}/approve', [AdminReportController::class, 'approve'])->name('reports.approve');
    Route::patch('/reports/{report}/reject', [AdminReportController::class, 'reject'])->name('reports.reject');

    // Categories Management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Users Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle_status');
    Route::patch('/users/{user}/update-role', [AdminUserController::class, 'updateRole'])->name('users.update_role');
});
