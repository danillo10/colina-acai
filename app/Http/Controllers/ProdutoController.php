<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    // Listar todos os produtos
    public function index()
    {
        $produtos = Produto::all();
        return response()->json($produtos);
    }

    // Criar um novo produto
    public function store(Request $request)
    {
        $request->validate([
            'nome'  => 'required|string|max:50',
            'preco' => 'required|numeric',
        ]);

        $produto = Produto::create($request->only('nome', 'preco'));

        return response()->json($produto, 201);
    }

    // Visualizar um produto específico
    public function show($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        return response()->json($produto);
    }

    // Atualizar um produto existente
    public function update(Request $request, $id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        $request->validate([
            'nome'  => 'sometimes|required|string|max:50',
            'preco' => 'sometimes|required|numeric',
        ]);

        $produto->update($request->only('nome', 'preco'));

        return response()->json($produto);
    }

    // Deletar um produto
    public function destroy($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        $produto->delete();

        return response()->json(['message' => 'Produto deletado com sucesso']);
    }
}
