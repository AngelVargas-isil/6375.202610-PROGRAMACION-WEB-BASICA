<?php

namespace App\Http\Controllers\Api;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        return Venta::with(['cliente', 'detalles.producto'])->get();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $venta = DB::transaction(function () use ($datos) {
            $venta = Venta::create([
                'cliente_id' => $datos['cliente_id'],
                'fecha' => $datos['fecha'],
                'total' => 0,
            ]);

            $total = 0;

            foreach ($datos['productos'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                ]);

                $total += $producto->precio * $item['cantidad'];
                $producto->decrement('stock', $item['cantidad']);
            }

            $venta->update(['total' => $total]);

            return $venta;
        });

        return response()->json($venta->load('detalles.producto'), 201);
    }

    public function show(string $id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto'])->findOrFail($id);
        return response()->json($venta);
    }

    /**
     * Actualiza solo los datos de cabecera de la venta (cliente, fecha).
     * Para modificar los productos de una venta, lo correcto es anularla
     * (destroy) y registrar una nueva, ya que eso mantiene el stock consistente.
     */
    public function update(Request $request, string $id)
    {
        $venta = Venta::findOrFail($id);

        $datos = $request->validate([
            'cliente_id' => 'sometimes|exists:clientes,id',
            'fecha' => 'sometimes|date',
        ]);

        $venta->update($datos);
        return response()->json($venta->load('cliente', 'detalles.producto'));
    }

    /**
     * Anula la venta: devuelve el stock de cada producto vendido
     * y luego elimina la venta (sus detalles se borran en cascada).
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $venta = Venta::with('detalles')->findOrFail($id);

            foreach ($venta->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }

            $venta->delete();
        });

        return response()->json(null, 204);
    }
}