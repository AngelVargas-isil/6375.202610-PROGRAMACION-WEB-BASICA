<?php

namespace App\Http\Controllers\Api;

use App\Models\DetalleVenta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DetalleVentaController extends Controller
{
    public function index()
    {
        return DetalleVenta::with(['venta', 'producto'])->get();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $detalle = DetalleVenta::create($datos);
        return response()->json($detalle, 201);
    }

    public function show(string $id)
    {
        $detalle = DetalleVenta::with(['venta', 'producto'])->findOrFail($id);
        return response()->json($detalle);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}