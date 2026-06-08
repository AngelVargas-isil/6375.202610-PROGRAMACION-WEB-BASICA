<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\CategoriaController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('productos', ProductoController::class)->only(['index', 'store']);
Route::apiResource('categorias', CategoriaController::class)->only(['index', 'store']);
