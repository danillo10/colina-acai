<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produto;
use App\Models\Adicional;
use Illuminate\Http\Request;

class PublicStoreController extends Controller
{
    /**
     * Retorna, de uma vez, as informações da loja, os produtos e os adicionais
     * identificados pelo tag_name.
     *
     * Exemplo de URL: GET /@minhatag
     */
    public function getStoreData($tag_name)
    {
        // Busca a loja (usuário) pelo tag_name
        $user = User::where('tag_name', $tag_name)->first();
        if (!$user) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        // Dados básicos da loja
        $storeInfo = [
            'nome'     => $user->nome_empresa ?? $user->nome_pessoa,
            'tag_name' => $user->tag_name,
            // Inclua outros dados que considere necessários, como endereço, etc.
        ];

        // Busca os produtos da loja
        $produtos = Produto::where('user_id', $user->id)->get();

        // Busca os adicionais da loja
        $adicionais = Adicional::where('user_id', $user->id)->get();

        return response()->json([
            'store'      => $storeInfo,
            'products'   => $produtos,
            'adicionais' => $adicionais
        ]);
    }
}
