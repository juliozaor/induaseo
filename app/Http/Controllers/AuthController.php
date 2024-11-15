<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function showLoginForm()
{
    return view('livewire.pages.auth.login'); // Cambia la ruta de la vista si es necesario
}

    // Método de inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha', // Validación del captcha
        ]);
    
        if (Auth::attempt(['numero_documento' => $request->usuario, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
    
        return back()->withErrors([
            'usuario' => __('auth.failed'),
        ]);
    }

    // Mostrar el formulario de recuperación de contraseña
    public function showForgotPasswordForm()
    {
        return view('livewire.pages.auth.forgot-password');
    }

    // Enviar el correo de recuperación de contraseña
    public function sendPasswordResetLink(Request $request)
    {
        
            $request->validate([
                'usuario' => 'required|exists:usuarios,numero_documento',
                'email' => 'required|email|exists:usuarios,email',
            ]);
        
            // Verificar si el usuario y el correo coinciden
            $user = Usuario::where('numero_documento', $request->usuario)
                            ->where('email', $request->email)
                            ->first();

        
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El usuario y el correo no coinciden en nuestros registros.',
                ]);
            }
        
            // Enviar el enlace de recuperación
            $status = Password::sendResetLink(
                $request->only('email')
            );
        
            return response()->json([
                'status' => $status === Password::RESET_LINK_SENT ? 'success' : 'error',
                'message' => __($status),
            ]);
    }

    // Mostrar el formulario de restablecimiento de contraseña
    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('livewire.pages.auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Restablecer la contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/', // Al menos una minúscula
                'regex:/[A-Z]/', // Al menos una mayúscula
                'regex:/[0-9]/', // Al menos un número
                'regex:/[@$!%*#?&+]/', // Al menos un carácter especial
                'confirmed',
            ],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}

