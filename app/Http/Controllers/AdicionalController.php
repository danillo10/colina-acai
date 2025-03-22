<?php

namespace App\Http\Controllers;

use App\Models\Adicional;
use Illuminate\Http\Request;

class AdicionalController extends Controller
{
    // Listar todos os adicionais
    public function index()
    {
        $adicionais = Adicional::all();
        return response()->json($adicionais);
    }

    // Visualizar detalhes de um adicional específico
    public function show($id)
    {
        $adicional = Adicional::find($id);
        if (!$adicional) {
            return response()->json(['error' => 'Adicional não encontrado'], 404);
        }
        return response()->json($adicional);
    }

    // Criar um novo adicional
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome'  => 'required|string|max:50',
            'preco' => 'required|numeric',
        ]);

        $adicional = Adicional::create($validatedData);

        return response()->json($adicional, 201);
    }

    // Atualizar um adicional existente
    public function update(Request $request, $id)
    {
        $adicional = Adicional::find($id);
        if (!$adicional) {
            return response()->json(['error' => 'Adicional não encontrado'], 404);
        }

        $validatedData = $request->validate([
            'nome'  => 'sometimes|required|string|max:50',
            'preco' => 'sometimes|required|numeric',
        ]);

        $adicional->update($validatedData);

        return response()->json($adicional);
    }

    // Deletar um adicional
    public function destroy($id)
    {
        $adicional = Adicional::find($id);
        if (!$adicional) {
            return response()->json(['error' => 'Adicional não encontrado'], 404);
        }

        $adicional->delete();

        return response()->json(['message' => 'Adicional deletado com sucesso']);
    }
}
