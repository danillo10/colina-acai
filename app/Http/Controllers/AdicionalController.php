<?php

namespace App\Http\Controllers;

use App\Models\Adicional;
use Illuminate\Http\Request;

class AdicionalController extends Controller
{
    // Listar todos os adicionais do usuário logado
    public function index()
    {
        $adicionais = Adicional::where('user_id', auth()->id())->get();
        return response()->json($adicionais);
    }

    // Visualizar detalhes de um adicional específico do usuário logado
    public function show($id)
    {
        $adicional = Adicional::where('user_id', auth()->id())->find($id);
        if (!$adicional) {
            return response()->json(['error' => 'Adicional não encontrado'], 404);
        }
        return response()->json($adicional);
    }

    // Criar um novo adicional vinculado ao usuário logado
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome'  => 'required|string|max:50',
            'preco' => 'required|numeric',
        ]);

        // Associa o adicional ao usuário logado
        $validatedData['user_id'] = auth()->id();

        $adicional = Adicional::create($validatedData);

        return response()->json($adicional, 201);
    }

    // Atualizar um adicional existente do usuário logado
    public function update(Request $request, $id)
    {
        $adicional = Adicional::where('user_id', auth()->id())->find($id);
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

    // Deletar um adicional do usuário logado
    public function destroy($id)
    {
        $adicional = Adicional::where('user_id', auth()->id())->find($id);
        if (!$adicional) {
            return response()->json(['error' => 'Adicional não encontrado'], 404);
        }

        $adicional->delete();

        return response()->json(['message' => 'Adicional deletado com sucesso']);
    }
}
