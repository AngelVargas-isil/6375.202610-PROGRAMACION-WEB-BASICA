<?php

namespace App\Http\Controllers\Api;

use App\Models\Cliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        return Cliente::all();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20|unique:clientes,dni',
            'telefono' => 'nullable|string|max:50',
        ]);

        $cliente = Cliente::create($datos);
        return response()->json($cliente, 201);
    }

    public function show(string $id)
    {
        $cliente = Cliente::with('ventas')->findOrFail($id);
        return response()->json($cliente);
    }

    public function update(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'dni' => 'nullable|string|max:20|unique:clientes,dni,' . $cliente->id,
            'telefono' => 'nullable|string|max:50',
        ]);

        $cliente->update($datos);
        return response()->json($cliente);
    }

    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();
        return response()->json(null, 204);
    }
}
