<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Usuário registrado com sucesso!',
            'usuario' => $user,
        ], 201);
    }
    
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Usuário logado com sucesso!',
                'token' => $token,
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'mensagem' => 'Falha na autenticação!',
        ]);    
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'sucesso'  => true,
            'mensagem' => 'Logout realizado com sucesso!',
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'sucesso'  => true,
            'usuario' => Auth::user(),
        ]);
    }

    public function validateToken(): JsonResponse
    {
        return response()->json([
            'token_valido'  => true,
        ]);
    }
}
