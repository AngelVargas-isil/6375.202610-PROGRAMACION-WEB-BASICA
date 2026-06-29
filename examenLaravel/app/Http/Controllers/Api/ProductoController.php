<?php

namespace App\Http\Controllers\Api;

use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        return Producto::with(['categoria', 'proveedor'])->get();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
        ]);

        $producto = Producto::create($datos);
        return response()->json($producto, 201);
    }

    public function show(string $id)
    {
        $producto = Producto::with(['categoria', 'proveedor'])->findOrFail($id);
        return response()->json($producto);
    }

    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'precio' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'categoria_id' => 'sometimes|exists:categorias,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
        ]);

        $producto->update($datos);
        return response()->json($producto);
    }

    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return response()->json(null, 204);
    }
}