<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\Adicional;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function store(Request $request)
    {
        // Validação dos dados enviados
        $validatedData = $request->validate([
            'produto' => 'required|exists:produtos,id',
            'adicionais' => 'nullable|array',
            'adicionais.*' => 'exists:adicionais,id',
            'nome_cliente' => 'required|string',
            'whatsapp' => 'required|string',
            'rua' => 'required|string',
            'numero' => 'required|string',
            'complemento' => 'nullable|string',
            'bairro' => 'required|string',
            'forma_pagamento' => 'required|in:credit,debit,cash',
            'troco' => 'nullable|numeric',
            'entrega' => 'required|boolean'
        ]);

        // Buscar o produto e calcular o valor inicial
        $produto = Produto::find($validatedData['produto']);
        $total = $produto->preco;

        // Se houver adicionais, somar seus preços
        if (!empty($validatedData['adicionais'])) {
            $adicionais = Adicional::whereIn('id', $validatedData['adicionais'])->get();
            foreach ($adicionais as $adicional) {
                $total += $adicional->preco;
            }
        }

        // Cria a venda com os dados do cliente e endereço
        $venda = Venda::create([
            'produto_id'      => $validatedData['produto'],
            'forma_pagamento' => $validatedData['forma_pagamento'],
            'troco'           => $validatedData['troco'] ?? null,
            'nome_cliente'    => $validatedData['nome_cliente'],
            'whatsapp'        => $validatedData['whatsapp'],
            'rua'             => $validatedData['rua'],
            'numero'          => $validatedData['numero'],
            'complemento'     => $validatedData['complemento'] ?? null,
            'bairro'          => $validatedData['bairro'],
            'valor_total'     => $total,
            'entrega'         => $validatedData['entrega']
        ]);

        // Vincula os adicionais à venda, se houver
        if (!empty($validatedData['adicionais'])) {
            $venda->adicionais()->attach($validatedData['adicionais']);
        }

        return response()->json([
            'message' => 'Venda registrada com sucesso!',
            'venda'   => $venda
        ], 201);
    }
}
