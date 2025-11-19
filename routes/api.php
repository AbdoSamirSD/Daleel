<?php

use Illuminate\Http\Request;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;


// User Routes
Route::prefix('user')->group(function () {
    Route::get('/categories', [CategoryController::class, 'showAll']);
    Route::get('/{categoryId}/shops', [ShopController::class, 'shopsByCategory']);
    Route::get('/shops/{shop}', [ShopController::class, 'showDetails']);
    Route::get('banners', [ShopController::class, 'listBanners']);
    Route::get('about', [CategoryController::class, 'about']);
});

Route::post('/admin/login', [ShopController::class, 'adminLogin']);
// Admin Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/categories/{category}', [CategoryController::class, 'index']);
    Route::post('/admin/categories', [CategoryController::class, 'store']);
    Route::post('/admin/categories/{categoryId}/update', [CategoryController::class, 'update']);
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy']);

    Route::get('/admin/shops', [ShopController::class, 'shopsByCategory']);
    Route::post('/admin/shops', [ShopController::class, 'store']);
    Route::put('/admin/shops/{shop}', [ShopController::class, 'update']);
    Route::delete('/admin/shops/{shop}', [ShopController::class, 'destroy']);

    Route::post('/admin/banner/upload', [ShopController::class, 'uploadBanner']);
    Route::delete('/admin/banners/{banner}', [ShopController::class, 'deleteBanner']);
    Route::get('/admin/banner/{banner}', [ShopController::class, 'showBanner']);
    Route::post('/admin/about', [CategoryController::class, 'updateAbout']);
});