<?php

namespace App\Http\Controllers\Api;

use App\Models\Proveedor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        return Proveedor::all();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $proveedor = Proveedor::create($datos);
        return response()->json($proveedor, 201);
    }

    public function show(string $id)
    {
        $proveedor = Proveedor::with('productos')->findOrFail($id);
        return response()->json($proveedor);
    }

    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $proveedor->update($datos);
        return response()->json($proveedor);
    }

    public function destroy(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();
        return response()->json(null, 204);
    }
}