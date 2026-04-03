<?php

namespace App\Http\Controllers;

use App\Models\BreathingSession;
use Illuminate\Http\Request;

class BreathingSessionController extends Controller
{
    public function store(Request $request)
{
    BreathingSession::create([
        'user_id'          => auth()->id(),
        'technique'        => $request->technique,
        'duration_minutes' => $request->duration_minutes,
    ]);

    return response()->json(['message' => 'Sesión guardada']);
    }

}
