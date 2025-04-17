<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\LoginController as UserLoginController;
use App\Http\Controllers\TestController as TestController;

Route::get('/',[UserLoginController::class,"showLoginForm"])->name('index');
Route::get('/test',[TestController::class,"test"])->name('test');
Route::get('/test2',[TestController::class,"test2"])->name('test2');

Broadcast::routes(['middleware' => ['auth:sanctum']]);
