<?php

// Rutas de autenticación generadas por Laravel Breeze.
// Están separadas en dos grupos:
// - 'guest': solo accesibles si el usuario NO está logueado (login, register, reset password)
// - 'auth': solo accesibles si el usuario SÍ está logueado (logout, verificación de email)

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Rutas accesibles solo para usuarios NO autenticados (invitados)
Route::middleware('guest')->group(function () {
    // Formulario y procesamiento del registro de nuevos usuarios
    Route::get('register',  [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Formulario y procesamiento del inicio de sesión
    Route::get('login',  [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Formulario de solicitud de enlace para resetear contraseña (envía el email)
    Route::get('forgot-password',  [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // Formulario de nueva contraseña usando el token del email de reset
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password',        [NewPasswordController::class, 'store'])->name('password.store');
});

// Rutas accesibles solo para usuarios autenticados
Route::middleware('auth')->group(function () {
    // Aviso de que el usuario necesita verificar su email
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');

    // Enlace de verificación que llega por email (firmado digitalmente con 'signed')
    // throttle:6,1 = máximo 6 intentos por minuto para evitar abuso
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Reenvío del email de verificación (limitado a 6 intentos por minuto)
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Pantalla de confirmación de contraseña antes de acciones sensibles
    Route::get('confirm-password',  [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Actualización de contraseña desde el perfil
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Cierre de sesión
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
