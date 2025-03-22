<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VendaController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/vendas', [VendaController::class, 'store']);
