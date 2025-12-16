<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApartmentImagesController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(["auth:sanctum", "verified"])->group(function () {

    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
    Route::get('/profile', [ProfileController::class, 'show']);

    Route::post('/apartments', [ApartmentController::class, 'store']);
    Route::put('/apartments/{id}', [ApartmentController::class, 'update']);
    Route::delete('/apartments/{id}', [ApartmentController::class, 'destroy']);
    Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
    Route::get('/apartments', [ApartmentController::class, 'index']);

    Route::post('apartmentsImages/{id}', [ApartmentImagesController::class, 'store']);
    Route::put('apartmentsImages/{id}', [ApartmentImagesController::class, 'update']);
    Route::delete('apartmentsImages/{id}', [ApartmentImagesController::class, 'destroy']);
    Route::get('apartmentsImages/{id}', [ApartmentImagesController::class, 'show']);

    Route::get('/tenant/reservations', [TenantController::class, 'getTenantReservations']);
    Route::post('/tenant/reservations/create', [TenantController::class, 'createReservation']);
    Route::put('/tenant/reservations/edit/{id}', [TenantController::class, 'editReservation']);
    Route::put('/tenant/reservations/cancel/{id}', [TenantController::class, 'cancelReservation']);

    Route::get('favorites', [FavoriteController::class, 'getFavorites']);
    Route::post('favorites/{id}', [FavoriteController::class, 'addToFavorites']);
    Route::delete('favorites/{id}', [FavoriteController::class, 'removeFromFavorites']);

    Route::put('/owner/reservations/pending/handle/{id}', [OwnerController::class, 'handlePendingReservation']);
    Route::put('/owner/reservations/cancel/handle/{id}', [OwnerController::class, 'handleCancelReservation']);
    Route::put('/owner/reservations/edit/handle/{id}', [OwnerController::class, 'handleEditeReservation']);
    Route::get('/owner/apartment/reservations/{id}', [OwnerController::class, 'getApartmentReservations']);
    Route::get('/owner/apartment/reservations/status/{id}', [OwnerController::class, 'getReservationsByStatus']);



});


Route::middleware(["auth:sanctum", "admin"])->group(function () {


    Route::get('/admin/users/search', [AdminController::class, 'findUsers']);
    Route::put('/admin/users/role/{id}', [AdminController::class, 'changeRole']);
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'deleteUser']);
    Route::post('/admin/users/verify/{id}', [AdminController::class, 'verifyUser']);
    Route::get('/admin/users/unverified', [AdminController::class, 'getUnverifiedUsers']);
});
