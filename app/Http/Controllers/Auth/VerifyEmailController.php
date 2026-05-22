<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('home', absolute: false));
        }

        $request->validate(['code' => 'required|string|size:6']);

        $code = $request->input('code');

        if ($user->email_verification_code !== $code) {
            throw ValidationException::withMessages([
                'code' => 'El código introducido no es válido.',
            ]);
        }

        if (now()->isAfter($user->email_verification_code_expires_at)) {
            throw ValidationException::withMessages([
                'code' => 'El código ha expirado. Solicita uno nuevo.',
            ]);
        }

        $user->markEmailAsVerified();
        $user->email_verification_code            = null;
        $user->email_verification_code_expires_at = null;
        $user->save();

        event(new Verified($user));

        return redirect()->intended(route('home', absolute: false));
    }
}
