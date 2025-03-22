<?php

namespace App\Http\Controllers;

use App\Models\Adicional;
use Illuminate\Http\Request;

class AdicionalController extends Controller
{
    public function index()
    {
        // Busca todos os adicionais cadastrados
        $adicionais = Adicional::all();

        // Retorna os adicionais em formato JSON
        return response()->json($adicionais);
    }
}
