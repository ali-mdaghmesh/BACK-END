<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::Post('/register', [UserController::class, 'register']);
Route::Post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware("auth:sanctum","verified")->group(function () {

    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
    Route::get('/profile', [ProfileController::class, 'show']);


});


Route::middleware("auth:sanctum","admin")->group(function () {

 
    Route::get('/admin/users/search', [AdminController::class, 'findUsers']);
    Route::put('/admin/users/role/{id}', [AdminController::class, 'changeRole']);
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'deleteUser']);
    Route::post('/admin/users/verify/{id}', [AdminController::class, 'verifyUser']);
    Route::get('/admin/users/unverified', [AdminController::class, 'getUnverifiedUsers']);
});
