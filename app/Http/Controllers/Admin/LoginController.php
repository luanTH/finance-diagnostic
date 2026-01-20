<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Mostra a tela de login
     */
    public function showLoginForm()
    {
        // Se o cara já estiver logado, manda direto pro dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('Admin/Login');
    }

    /**
     * Processa a tentativa de login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        // Tenta autenticar
        if (Auth::attempt($credentials, $request->remember)) {
            // Gera uma nova sessão para evitar ataques de fixação de sessão
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        // Se falhar, devolve o erro pro front
        throw ValidationException::withMessages([
            'email' => 'As credenciais fornecidas não coincidem com nossos registros.',
        ]);
    }

    /**
     * Desloga o usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
