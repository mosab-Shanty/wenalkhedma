<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AiAssistantController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/


// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Categories & Services
Route::get('/categories', [ServiceController::class, 'categories']);

Route::get('/services', [ServiceController::class, 'index']);

Route::get('/services/nearby', [ServiceController::class, 'nearby']);

Route::get('/services/{id}', [ServiceController::class, 'show']);


// Service Reports
Route::get(
    '/services/{id}/reports',
    [ReportController::class, 'serviceReports']
);


// Simple AI Assistant
Route::post(
    '/ai/ask',
    [AiAssistantController::class, 'ask']
);


/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/


Route::middleware(['auth:sanctum', 'active'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Account
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/profile',
        [AuthController::class, 'profile']
    );


    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    );


    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */


    // إضافة خدمة جديدة
    Route::post(
        '/services',
        [ServiceController::class, 'store']
    );


    // خدمات المستخدم الحالي
    Route::get(
        '/my-services',
        [ServiceController::class, 'myServices']
    );


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */


    Route::post(
        '/reports',
        [ReportController::class, 'store']
    );


    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/favorites',
        [FavoriteController::class, 'index']
    );


    Route::post(
        '/favorites/toggle',
        [FavoriteController::class, 'toggle']
    );


    /*
    |--------------------------------------------------------------------------
    | Documents
    |--------------------------------------------------------------------------
    */


    Route::post(
        '/documents/upload',
        [DocumentController::class, 'upload']
    );


    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    );


    Route::patch(
        '/notifications/{id}/read',
        [NotificationController::class, 'markAsRead']
    );


    /*
    |--------------------------------------------------------------------------
    | Moderator / Admin Routes
    |--------------------------------------------------------------------------
    */


    Route::middleware('role:moderator,admin')->group(function () {


        // Pending Services

        Route::get(
            '/admin/services/pending',
            [ServiceController::class, 'pendingServices']
        );


        Route::patch(
            '/admin/services/{id}/approve',
            [ServiceController::class, 'approveService']
        );


        Route::patch(
            '/admin/services/{id}/reject',
            [ServiceController::class, 'rejectService']
        );


        // Pending Reports

        Route::get(
            '/admin/reports/pending',
            [ReportController::class, 'pendingReports']
        );


        Route::patch(
            '/admin/reports/{id}/approve',
            [ReportController::class, 'approveReport']
        );


        Route::patch(
            '/admin/reports/{id}/reject',
            [ReportController::class, 'rejectReport']
        );


    });




});
