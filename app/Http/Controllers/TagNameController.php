<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TagNameController extends Controller
{
    /**
     * Verifica se o tag_name está disponível para uso.
     *
     * @param  string  $tag_name
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTagName($tag_name)
    {
        $exists = User::where('tag_name', $tag_name)->exists();

        if ($exists) {
            return response()->json([
                'available' => false,
                'message'   => 'Este @ da loja já está em uso.'
            ]);
        } else {
            return response()->json([
                'available' => true,
                'message'   => 'Este @ da loja está disponível.'
            ]);
        }
    }
}
