<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VendaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AdicionalController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/vendas', [VendaController::class, 'store']);
Route::get('/produtos', [ProdutoController::class, 'index']);
Route::get('/adicionais', [AdicionalController::class, 'index']);
