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
        $messages = [
            'nome_pessoa.string'      => 'O nome da pessoa deve ser um texto válido.',
            'nome_empresa.string'     => 'O nome da empresa deve ser um texto válido.',
            'cpf_cnpj.required'       => 'O campo CPF/CNPJ é obrigatório.',
            'cpf_cnpj.string'         => 'O campo CPF/CNPJ deve ser um texto.',
            'cpf_cnpj.unique'         => 'Este CPF/CNPJ já está em uso.',
            'telefone.string'         => 'O telefone deve ser um texto válido.',
            'email.required'          => 'O e-mail é obrigatório.',
            'email.email'             => 'Informe um e-mail válido.',
            'email.unique'            => 'Este e-mail já está em uso.',
            'password.required'       => 'A senha é obrigatória.',
            'password.min'            => 'A senha deve ter no mínimo :min caracteres.',
            'password.confirmed'      => 'A confirmação da senha não confere.',
        ];

        $validator = \Validator::make($request->all(), [
            'nome_pessoa'   => 'nullable|string|max:255',
            'nome_empresa'  => 'nullable|string|max:255',
            'cpf_cnpj'      => 'required|string|max:20|unique:users,cpf_cnpj',
            'telefone'      => 'nullable|string|max:20',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|confirmed',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = \App\Models\User::create([
            'nome_pessoa'  => $request->nome_pessoa,
            'nome_empresa' => $request->nome_empresa,
            'cpf_cnpj'     => $request->cpf_cnpj,
            'telefone'     => $request->telefone,
            'email'        => $request->email,
            'password'     => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        // Se estiver utilizando JWT, gere o token
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'user'    => $user,
            'token'   => $token
        ], 201);
    }



    /**
     * Realiza o login e retorna um token JWT.
     */
    public function login(Request $request)
    {
        $messages = [
            'email.required'    => 'O e-mail é obrigatório.',
            'email.email'       => 'Informe um e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ];

        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], $messages);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'error'   => 'As credenciais não correspondem aos nossos registros.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => auth()->user()
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
        return response()->json(auth()->user());
    }

     /**
     * Atualiza os dados do usuário autenticado.
     */
    public function updateUser(Request $request)
    {
        $user = auth()->user();

        $messages = [
            'nome_pessoa.string'   => 'O nome da pessoa deve ser um texto válido.',
            'nome_empresa.string'  => 'O nome da empresa deve ser um texto válido.',
            'telefone.string'      => 'O telefone deve ser um texto válido.',
            'tag_name.unique'      => 'Este @ da loja já está em uso.',
        ];

        $data = $request->validate([
            'nome_pessoa'  => 'nullable|string|max:255',
            'nome_empresa' => 'nullable|string|max:255',
            'telefone'     => 'nullable|string|max:20',
            'tag_name'     => 'nullable|string|unique:users,tag_name,' . $user->id,
        ], $messages);

        $user->update($data);

        return response()->json([
            'success' => true,
            'user'    => $user,
            'message' => 'Usuário atualizado com sucesso.'
        ]);
    }
}
