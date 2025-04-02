<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    // Listar todos os produtos do usuário logado
    public function index()
    {
        $produtos = Produto::where('user_id', auth()->id())->get();
        return response()->json($produtos);
    }

    // Criar um novo produto vinculado ao usuário logado
    public function store(Request $request)
    {
        $request->validate([
            'nome'  => 'required|string|max:50',
            'preco' => 'required|numeric',
        ]);

        $data = $request->only('nome', 'preco');
        $data['user_id'] = auth()->id();

        $produto = Produto::create($data);

        return response()->json($produto, 201);
    }

    // Visualizar um produto específico do usuário logado
    public function show($id)
    {
        $produto = Produto::where('user_id', auth()->id())->find($id);

        if (!$produto) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        return response()->json($produto);
    }

    // Atualizar um produto existente do usuário logado
    public function update(Request $request, $id)
    {
        $produto = Produto::where('user_id', auth()->id())->find($id);

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

    // Deletar um produto do usuário logado
    public function destroy($id)
    {
        $produto = Produto::where('user_id', auth()->id())->find($id);

        if (!$produto) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        $produto->delete();

        return response()->json(['message' => 'Produto deletado com sucesso']);
    }
}
