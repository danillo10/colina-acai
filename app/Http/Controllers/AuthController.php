<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Registra um novo usuário e retorna um token JWT.
     */
    public function register(Request $request)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'nome_pessoa'      => 'nullable|string|max:255',
            'nome_empresa'     => 'nullable|string|max:255',
            'cpf_cnpj'         => 'required|string|max:20|unique:users,cpf_cnpj',
            'telefone'         => 'nullable|string|max:20',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Criação do usuário
        $user = User::create([
            'nome_pessoa'  => $request->nome_pessoa,
            'nome_empresa' => $request->nome_empresa,
            'cpf_cnpj'     => $request->cpf_cnpj,
            'telefone'     => $request->telefone,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        // Gera o token JWT para o usuário
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user'  => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Realiza o login e retorna um token JWT.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        return response()->json([
            'token' => $token,
            'user'  => auth('api')->user()
        ]);
    }

    /**
     * Realiza o logout invalidando o token.
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * Retorna os dados do usuário autenticado.
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }
}
