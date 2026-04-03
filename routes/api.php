use App\Http\Controllers\BreathingSessionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/breathing-sessions', [BreathingSessionController::class, 'store']);
});