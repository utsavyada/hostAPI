<?php

use App\Http\Controllers\AuthController;
use  App\Http\Controllers\FeedbackController;
use  App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/feedback',[FeedbackController::class,'feedback']);
Route::get('/feedback/all',[FeedbackController::class,'show']);

Route::get('products', [ProductController::class, 'index'])->name('products.list'); 
Route::post('products/create', [ProductController::class, 'store'])->name('products.save'); 
Route::get('products/{product}', [ProductController::class, 'show'])->name('products.view'); 
Route::delete('products/{product}/delete', [ProductController::class, 'destroy'])->name('products.delete'); 
Route::post('products/{product}/edit', [ProductController::class, 'update'])->name('products.modify'); 

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
});
