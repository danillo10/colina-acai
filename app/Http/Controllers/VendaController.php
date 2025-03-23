<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\Adicional;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    // Listar todas as vendas, com seus relacionamentos (produto e adicionais)
    public function index()
    {
        $vendas = Venda::with(['produto', 'adicionais'])->get();
        return response()->json($vendas);
    }

    // Visualizar detalhes de uma venda específica
    public function show($id)
    {
        $venda = Venda::with(['produto', 'adicionais'])->find($id);
        if (!$venda) {
            return response()->json(['message' => 'Venda não encontrada'], 404);
        }
        return response()->json($venda);
    }

    // Criar uma nova venda
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
            'forma_pagamento' => 'required|in:credit,debit,cash,pix',
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
            'venda'   => $venda->load(['produto', 'adicionais'])
        ], 201);
    }

    // Atualizar uma venda existente
    public function update(Request $request, $id)
    {
        $venda = Venda::with('adicionais')->find($id);
        if (!$venda) {
            return response()->json(['message' => 'Venda não encontrada'], 404);
        }

        // Validação dos dados (os campos são opcionais para atualização)
        $validatedData = $request->validate([
            'produto' => 'sometimes|required|exists:produtos,id',
            'adicionais' => 'nullable|array',
            'adicionais.*' => 'exists:adicionais,id',
            'nome_cliente' => 'sometimes|required|string',
            'whatsapp' => 'sometimes|required|string',
            'rua' => 'sometimes|required|string',
            'numero' => 'sometimes|required|string',
            'complemento' => 'nullable|string',
            'bairro' => 'sometimes|required|string',
            'forma_pagamento' => 'sometimes|required|in:credit,debit,cash',
            'troco' => 'nullable|numeric',
            'entrega' => 'sometimes|required|boolean'
        ]);

        // Se o produto for atualizado, ou mantenha o atual
        if (isset($validatedData['produto'])) {
            $produto = Produto::find($validatedData['produto']);
        } else {
            $produto = Produto::find($venda->produto_id);
        }

        // Recalcula o total
        $total = $produto->preco;
        $additionalIds = $validatedData['adicionais'] ?? null;
        if ($additionalIds) {
            $adicionais = Adicional::whereIn('id', $additionalIds)->get();
            foreach ($adicionais as $adicional) {
                $total += $adicional->preco;
            }
        } else {
            // Se não for passado, mantém os adicionais já vinculados
            foreach ($venda->adicionais as $adicional) {
                $total += $adicional->preco;
            }
        }
        $validatedData['valor_total'] = $total;

        // Se o produto foi atualizado, atualize o campo produto_id
        if (isset($validatedData['produto'])) {
            $validatedData['produto_id'] = $validatedData['produto'];
            unset($validatedData['produto']);
        }

        $venda->update($validatedData);

        // Atualiza os adicionais se forem fornecidos
        if ($additionalIds !== null) {
            $venda->adicionais()->sync($additionalIds);
        }

        // Recarrega os relacionamentos para a resposta
        $venda->load(['produto', 'adicionais']);

        return response()->json([
            'message' => 'Venda atualizada com sucesso!',
            'venda' => $venda
        ]);
    }

    // Deletar uma venda
    public function destroy($id)
    {
        $venda = Venda::find($id);
        if (!$venda) {
            return response()->json(['message' => 'Venda não encontrada'], 404);
        }
        $venda->delete();
        return response()->json(['message' => 'Venda deletada com sucesso!']);
    }

    /**
     * Retorna um resumo das vendas: total de vendas, soma dos valores e últimas vendas.
     */
    public function summary()
    {
        // Total de vendas e soma dos valores
        $totalSales = Venda::count();
        $totalValue = Venda::sum('valor_total');

        // Buscar as últimas 5 vendas com os relacionamentos (produto e adicionais)
        $latestSales = Venda::with(['produto', 'adicionais'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Adiciona o status padrão "Em preparo" para cada venda (caso não exista uma coluna 'status')
        $latestSales->transform(function ($sale) {
            // Se você tiver uma coluna 'status' no banco, remova ou ajuste essa linha
            $sale->status = "Em preparo";
            return $sale;
        });

        return response()->json([
            'total_sales_count' => $totalSales,
            'total_sales_value' => $totalValue,
            'latest_sales'      => $latestSales
        ]);
    }
}
