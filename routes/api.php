<?php
use  App\Http\Controllers\FeedbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/feedback',[FeedbackController::class,'feedback']);
Route::get('/feedback/all',[FeedbackController::class,'show']);
Route::post('/product',[FeedbackController::class,'saveProduct']);
Route::get('/product/all',[FeedbackController::class,'showProduct']);