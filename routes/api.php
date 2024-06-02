<?php
use  App\Http\Controllers\FeedbackController;
use  App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/feedback',[FeedbackController::class,'feedback']);
Route::get('/feedback/all',[FeedbackController::class,'show']);

Route::resource('products', ProductController::class)->except(['update']);
Route::post('products/{product}', [ProductController::class, 'update'])->name('products.update');

