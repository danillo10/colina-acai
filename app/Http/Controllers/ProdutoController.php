<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        // Busca todos os produtos cadastrados
        $produtos = Produto::all();

        // Retorna os produtos em formato JSON
        return response()->json($produtos);
    }
}
