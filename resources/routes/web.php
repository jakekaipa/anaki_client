<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\LoginController as UserLoginController;
use App\Http\Controllers\TestController;


// Route::get('/',[UserLoginController::class,"showLoginForm"])->name('index');

Route::get('/',[UserLoginController::class,"moveLanding"])->name('index');

Route::get('/test',[TestController::class,"test"])->name('test');
Route::get('/test2',[TestController::class,"test2"])->name('test2');
Route::post('/test3',[TestController::class,"test3"])->name('test3');
Route::get('/test4',[TestController::class,"test4"])->name('test4');
// Route::get('/main-landing',[TestController::class,"moveLanding"])->name('main-landing');

Broadcast::routes(['middleware' => ['auth:sanctum']]);
