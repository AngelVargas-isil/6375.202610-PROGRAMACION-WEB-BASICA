<?php

namespace App\Http\Controllers\Api;

use App\Models\Categoria;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        return Categoria::all();
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
        ]);

        $categoria = Categoria::create($datos);
        return response()->json($categoria, 201);
    }

    public function show(string $id)
    {
        $categoria = Categoria::with('productos')->findOrFail($id);
        return response()->json($categoria);
    }

    public function update(Request $request, string $id)
    {
        $categoria = Categoria::findOrFail($id);

        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string|max:255',
        ]);

        $categoria->update($datos);
        return response()->json($categoria);
    }

    public function destroy(string $id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        return response()->json(null, 204);
    }
}
