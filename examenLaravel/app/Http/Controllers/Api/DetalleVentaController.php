<?php

namespace App\Http\Controllers\Api;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Permite corregir el producto y/o la cantidad de una línea de venta.
     * Devuelve el stock anterior, descuenta el nuevo, y recalcula el total
     * de la venta a la que pertenece.
     */
    public function update(Request $request, string $id)
    {
        $datos = $request->validate([
            'producto_id' => 'sometimes|exists:productos,id',
            'cantidad' => 'sometimes|integer|min:1',
        ]);

        $detalle = DB::transaction(function () use ($datos, $id) {
            $detalle = DetalleVenta::findOrFail($id);

            // Revertir el stock de la línea anterior
            $detalle->producto->increment('stock', $detalle->cantidad);

            $nuevoProductoId = $datos['producto_id'] ?? $detalle->producto_id;
            $nuevaCantidad = $datos['cantidad'] ?? $detalle->cantidad;
            $nuevoProducto = Producto::findOrFail($nuevoProductoId);

            // Descontar el nuevo stock
            $nuevoProducto->decrement('stock', $nuevaCantidad);

            $detalle->update([
                'producto_id' => $nuevoProductoId,
                'cantidad' => $nuevaCantidad,
                'precio_unitario' => $nuevoProducto->precio,
            ]);

            // Recalcular el total de la venta
            $totalActualizado = DetalleVenta::where('venta_id', $detalle->venta_id)
                ->get()
                ->sum(fn ($d) => $d->cantidad * $d->precio_unitario);

            Venta::where('id', $detalle->venta_id)->update(['total' => $totalActualizado]);

            return $detalle;
        });

        return response()->json($detalle->load('venta', 'producto'));
    }

    /**
     * Elimina una línea de venta, devuelve el stock correspondiente
     * y recalcula el total de la venta.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $detalle = DetalleVenta::findOrFail($id);
            $ventaId = $detalle->venta_id;

            $detalle->producto->increment('stock', $detalle->cantidad);
            $detalle->delete();

            $totalActualizado = DetalleVenta::where('venta_id', $ventaId)
                ->get()
                ->sum(fn ($d) => $d->cantidad * $d->precio_unitario);

            Venta::where('id', $ventaId)->update(['total' => $totalActualizado]);
        });

        return response()->json(null, 204);
    }
}