<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\Adicional;
use App\Models\VendaProduto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendaController extends Controller
{
    /**
     * Listar todas as vendas com seus produtos e adicionais do usuário logado.
     */
    public function index()
    {
        $vendas = Venda::with(['vendaProdutos.produto', 'vendaProdutos.adicionais'])
            ->where('user_id', auth()->id())
            ->get();
        return response()->json($vendas);
    }

    /**
     * Visualizar detalhes de uma venda específica do usuário logado.
     */
    public function show($id)
    {
        $venda = Venda::with(['vendaProdutos.produto', 'vendaProdutos.adicionais'])
            ->where('user_id', auth()->id())
            ->find($id);
        if (!$venda) {
            return response()->json(['message' => 'Venda não encontrada'], 404);
        }
        return response()->json($venda);
    }

    public function store(Request $request)
    {
        // Validação dos dados enviados, incluindo o tag_name
        $validatedData = $request->validate([
            'tag_name'                 => 'required|string|exists:users,tag_name',
            'nome_cliente'             => 'required|string',
            'whatsapp'                 => 'required|string',
            'rua'                      => 'required|string',
            'numero'                   => 'required|string',
            'complemento'              => 'nullable|string',
            'bairro'                   => 'required|string',
            'forma_pagamento'          => 'required|in:credit,debit,cash,pix',
            'troco'                    => 'nullable|numeric',
            'entrega'                  => 'required|boolean',
            'produtos'                 => 'required|array|min:1',
            'produtos.*.produto_id'    => 'required|exists:produtos,id',
            'produtos.*.adicionais'    => 'nullable|array',
            'produtos.*.adicionais.*'  => 'exists:adicionais,id'
        ]);

        // Busca a loja com base no tag_name recebido
        $store = \App\Models\User::where('tag_name', $validatedData['tag_name'])->first();
        if (!$store) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        DB::beginTransaction();
        try {
            $total = 0;

            // Cria a venda vinculando ao user_id da loja encontrada
            $venda = \App\Models\Venda::create([
                'nome_cliente'    => $validatedData['nome_cliente'],
                'whatsapp'        => $validatedData['whatsapp'],
                'rua'             => $validatedData['rua'],
                'numero'          => $validatedData['numero'],
                'complemento'     => $validatedData['complemento'] ?? null,
                'bairro'          => $validatedData['bairro'],
                'forma_pagamento' => $validatedData['forma_pagamento'],
                'troco'           => $validatedData['troco'] ?? null,
                'entrega'         => $validatedData['entrega'],
                'valor_total'     => 0,  // Valor provisório, será atualizado
                'status'          => 'Aguardando', // Status inicial
                'user_id'         => $store->id,
            ]);

            // Processa cada produto enviado
            foreach ($validatedData['produtos'] as $item) {
                // Busca o produto que pertença à loja (user_id do store)
                $produto = \App\Models\Produto::where('user_id', $store->id)
                    ->find($item['produto_id']);
                if (!$produto) {
                    throw new \Exception("Produto com ID {$item['produto_id']} não encontrado para esta loja.");
                }
                // Soma o preço do produto
                $total += $produto->preco;

                // Cria o registro na tabela venda_produtos, vinculando a loja
                $vendaProduto = \App\Models\VendaProduto::create([
                    'venda_id'   => $venda->id,
                    'produto_id' => $produto->id,
                    'user_id'    => $store->id,
                ]);

                // Se houver adicionais para este produto, processa-os
                if (isset($item['adicionais']) && is_array($item['adicionais'])) {
                    $adicionais = \App\Models\Adicional::where('user_id', $store->id)
                        ->whereIn('id', $item['adicionais'])
                        ->get();
                    foreach ($adicionais as $adicional) {
                        $total += $adicional->preco;
                        // Insere o registro na tabela pivot venda_produto_adicionais
                        \DB::table('venda_produto_adicionais')->insert([
                            'venda_produto_id' => $vendaProduto->id,
                            'adicional_id'     => $adicional->id,
                        ]);
                    }
                }
            }

            // Atualiza o valor total da venda
            $venda->valor_total = $total;
            $venda->save();

            // Registra o histórico inicial do status (old_status é nulo)
            \DB::table('venda_status_histories')->insert([
                'venda_id'   => $venda->id,
                'old_status' => null,
                'new_status' => $venda->status,
                'changed_by' => $store->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Carrega os relacionamentos para retorno
            $venda->load(['vendaProdutos.produto', 'vendaProdutos.adicionais']);

            return response()->json([
                'message' => 'Venda registrada com sucesso!',
                'venda'   => $venda
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao registrar venda.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar uma venda existente (apenas dados gerais) do usuário logado.
     * Nota: Atualizar produtos e adicionais pode ser mais complexo e, neste exemplo,
     * estamos atualizando apenas os dados gerais da venda.
     */
    public function update(Request $request, $id)
    {
        $venda = Venda::with(['vendaProdutos.produto', 'vendaProdutos.adicionais'])
            ->where('user_id', auth()->id())
            ->find($id);
        if (!$venda) {
            return response()->json(['message' => 'Venda não encontrada'], 404);
        }

        $validatedData = $request->validate([
            'nome_cliente'    => 'sometimes|required|string',
            'whatsapp'        => 'sometimes|required|string',
            'rua'             => 'sometimes|required|string',
            'numero'          => 'sometimes|required|string',
            'complemento'     => 'nullable|string',
            'bairro'          => 'sometimes|required|string',
            'forma_pagamento' => 'sometimes|required|in:credit,debit,cash,pix',
            'troco'           => 'nullable|numeric',
            'entrega'         => 'sometimes|required|boolean'
            // Atualização de produtos e adicionais requer lógica adicional
        ]);

        $venda->update($validatedData);
        return response()->json([
            'message' => 'Venda atualizada com sucesso!',
            'venda'   => $venda->load(['vendaProdutos.produto', 'vendaProdutos.adicionais'])
        ]);
    }

    /**
     * Deletar uma venda do usuário logado.
     */
    public function destroy($id)
    {
        $venda = Venda::where('user_id', auth()->id())->find($id);
        if (!$venda) {
            return response()->json(['message' => 'Venda não encontrada'], 404);
        }
        $venda->delete();
        return response()->json(['message' => 'Venda deletada com sucesso!']);
    }

    /**
     * Retornar um resumo das vendas do usuário logado:
     * - Total de vendas
     * - Soma dos valores de vendas
     * - Últimas 5 vendas com detalhes
     */
    public function summary()
    {
        $totalSales = Venda::where('user_id', auth()->id())->count();
        $totalValue = Venda::where('user_id', auth()->id())->sum('valor_total');

        $latestSales = Venda::with(['vendaProdutos.produto', 'vendaProdutos.adicionais'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Define status padrão, se necessário (caso não esteja salvo na tabela)
        $latestSales->each(function ($sale) {
            $sale->status = $sale->status ?? 'Em preparo';
        });

        return response()->json([
            'total_sales_count' => $totalSales,
            'total_sales_value' => $totalValue,
            'latest_sales'      => $latestSales
        ]);
    }

    /**
        * Atualiza somente o status de uma venda do usuário logado.
    */
    public function updateStatus(Request $request, $id)
    {
        $venda = Venda::where('user_id', auth()->id())->find($id);
        if (!$venda) {
            return response()->json(['error' => 'Venda não encontrada'], 404);
        }

        // Validação para garantir que o status enviado seja um dos permitidos
        $validatedData = $request->validate([
            'status' => 'required'
        ]);

        $venda->status = $validatedData['status'];
        $venda->save();

        return response()->json([
            'message' => 'Status atualizado com sucesso!',
            'venda'   => $venda
        ]);
    }

    /**
     * Retorna o histórico de status de uma venda do usuário logado.
     */
    public function statusHistory($id)
    {
        // Verifica se a venda pertence ao usuário logado
        $venda = \App\Models\Venda::where('user_id', auth()->id())->find($id);
        if (!$venda) {
            return response()->json(['error' => 'Venda não encontrada'], 404);
        }

        // Busca o histórico dos status para essa venda, ordenado pela data de criação
        $history = \DB::table('venda_status_histories')
            ->where('venda_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'venda'          => $venda,
            'status_history' => $history
        ]);
    }


}
