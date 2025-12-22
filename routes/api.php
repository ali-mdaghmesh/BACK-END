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

//done
Route::post('/register', [UserController::class, 'register']);
//done
Route::post('/login', [UserController::class, 'login']);
//done
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware("auth:sanctum")->group(function () {
    Route::get('/filter', [ProfileController::class, 'filterApartments']);
    Route::get('/getApartments', [ApartmentController::class, 'showApartments']);
    Route::get('/profile', [ProfileController::class, 'show']);


});

Route::middleware(["auth:sanctum", "verified"])->group(function () {

    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    Route::get('favorites', [FavoriteController::class, 'getFavorites']);
    Route::post('favorites/{id}', [FavoriteController::class, 'addToFavorites']);
    Route::delete('favorites/{id}', [FavoriteController::class, 'removeFromFavorites']);


});

Route::middleware(["auth:sanctum", "verified", "owner"])->group(function () {

    Route::post('/owner/apartments', [ApartmentController::class, 'store']);
    Route::put('/owner/apartments/{id}', [ApartmentController::class, 'update']);
    Route::delete('/owner/apartments/{id}', [ApartmentController::class, 'destroy']);
    Route::get('/owner/apartments/{id}', [ApartmentController::class, 'show']);
    Route::get('/owner/apartments', [ApartmentController::class, 'index']);

    Route::post('/owner/apartmentsImages/{id}', [ApartmentImagesController::class, 'store']);
    Route::put('/owner/apartmentsImages/{id}', [ApartmentImagesController::class, 'update']);
    Route::delete('/owner/apartmentsImages/{id}', [ApartmentImagesController::class, 'destroy']);
    Route::get('/owner/apartmentsImages/{id}', [ApartmentImagesController::class, 'show']);

    Route::put('/owner/reservations/pending/{id}', [OwnerController::class, 'handlePendingReservation']);
    Route::put('/owner/reservations/cancel/{id}', [OwnerController::class, 'handleCancelReservation']);
    Route::put('/owner/reservations/edit/{id}', [OwnerController::class, 'handleEditeReservation']);
    Route::get('/owner/apartment/reservations/{id}', [OwnerController::class, 'getApartmentReservations']);
    Route::get('/owner/apartment/reservations/status/{id}', [OwnerController::class, 'getReservationsByStatus']);


});

Route::middleware(["auth:sanctum", "verified", "tenant"])->group(function () {

    Route::get('/tenant/reservations', [TenantController::class, 'getTenantReservations']);
    Route::post('/tenant/reservations/{id}', [TenantController::class, 'createReservation']);
    Route::put('/tenant/reservations/edit/{id}', [TenantController::class, 'editReservation']);
    Route::put('/tenant/reservations/cancel/{id}', [TenantController::class, 'cancelReservation']);
    Route::post('/tenant/rate/{id}', [TenantController::class, 'rateApartment']);


});


Route::middleware(["auth:sanctum", "admin"])->group(function () {


    Route::get('/admin/users/search', [AdminController::class, 'findUsers']);
    Route::put('/admin/users/role/{id}', [AdminController::class, 'changeRole']);
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'deleteUser']);
    Route::post('/admin/users/verify/{id}', [AdminController::class, 'verifyUser']);
    Route::get('/admin/users/unverified', [AdminController::class, 'getUnverifiedUsers']);
});
