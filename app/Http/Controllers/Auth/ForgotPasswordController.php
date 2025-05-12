<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', 'Enviamos um link de recuperação para o seu e-mail.');
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Não encontramos um usuário com este endereço de e-mail.']);
        }

        if (!$user->status) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Sua conta está desativada. Entre em contato com o administrador.']);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Não foi possível enviar o e-mail de recuperação. Tente novamente mais tarde.']);
    }
}
