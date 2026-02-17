<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TESTING ROUTES 
Route::view('/example-page', 'example-page');
Route::view('/example-auth', 'example-auth');


// ADMIN ROUTES
Route::prefix('admin')->name('admin.')->group(function (){
    Route::middleware([])->group(function (){
        Route::controller(AuthController::class)->group(function (){
            Route::get('/login', 'loginForm')->name('login');
            Route::get('/forgot-password', 'forgotForm')->name('forgot');
        });
    });

    Route::middleware([])->group(function (){
        Route::controller(AdminController::class)->group(function (){
            Route::get('/dashboard','adminDashboard')->name('dashboard');
        });
    });
});