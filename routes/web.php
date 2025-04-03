<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagNameController;
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

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/check-tag-name/{tag_name}', [TagNameController::class, 'checkTagName']);

    // Rotas protegidas usando o middleware "auth"
    Route::middleware(['auth'])->group(function () {

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
        Route::delete('/vendas/{id}', [VendaController::class, 'destroy']);
        Route::middleware('auth')->put('/vendas/{id}/status', [VendaController::class, 'updateStatus']);         // Deletar venda

        // Rotas adicionais, por exemplo:
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::middleware('auth')->put('/user/update', [AuthController::class, 'updateUser']);
    });
});
