<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AdicionalController;

Route::get('/', function () {
    return view('welcome');
});

// Agrupa as rotas e remove o middleware CSRF
Route::withoutMiddleware([ValidateCsrfToken::class])->group(function () {

    // Rotas para Produtos
    Route::get('/produtos', [ProdutoController::class, 'index']);       // Listar produtos
    Route::get('/produtos/{id}', [ProdutoController::class, 'show']);     // Visualizar produto
    Route::post('/produtos', [ProdutoController::class, 'store']);        // Criar produto
    Route::put('/produtos/{id}', [ProdutoController::class, 'update']);     // Atualizar produto
    Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy']); // Deletar produto

    // Rotas para Adicionais
    Route::get('/adicionais', [AdicionalController::class, 'index']);       // Listar adicionais
    Route::get('/adicionais/{id}', [AdicionalController::class, 'show']);     // Visualizar adicional
    Route::post('/adicionais', [AdicionalController::class, 'store']);        // Criar adicional
    Route::put('/adicionais/{id}', [AdicionalController::class, 'update']);     // Atualizar adicional
    Route::delete('/adicionais/{id}', [AdicionalController::class, 'destroy']); // Deletar adicional

    // Rotas para Vendas
    Route::get('/vendas/summary', [VendaController::class, 'summary']);
    Route::get('/vendas', [VendaController::class, 'index']);               // Listar vendas
    Route::get('/vendas/{id}', [VendaController::class, 'show']);             // Visualizar venda
    Route::post('/vendas', [VendaController::class, 'store']);                // Criar venda
    Route::put('/vendas/{id}', [VendaController::class, 'update']);             // Atualizar venda
    Route::delete('/vendas/{id}', [VendaController::class, 'destroy']);         // Deletar venda
});
