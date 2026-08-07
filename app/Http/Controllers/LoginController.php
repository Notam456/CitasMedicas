<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController
{
    public function login(Request $request)
    {
        
        $credentials = $request->validate([
        'name'     => 'required|string|max:255',
        'password' => 'required|string',
    ], [
        'name.required'     => 'El nombre de usuario es obligatorio.',
        'password.required' => 'La contraseña es obligatoria.',
    ]);
        

        if (Auth::attempt($credentials)) {

            request()->session()->regenerate();

            return redirect()->route('dashboard');
        }

       return back()
        ->withInput($request->only('name'))
        ->withErrors([
            'name' => 'Las credenciales son incorrectas.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}
