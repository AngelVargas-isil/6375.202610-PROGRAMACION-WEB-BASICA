<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\DetalleVentaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('productos', ProductoController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('proveedores', ProveedorController::class);
Route::apiResource('clientes', ClienteController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('detalle-ventas', DetalleVentaController::class);