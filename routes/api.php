<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\Api\ExtensionController;
Route::get('/ping', fn() => response()->json(['pong' => true]));

 Route::middleware('auth.api_token', 'check.meeting.limit')->post('/upload', [ExtensionController::class, 'uploadFromExtension']);
 Route::options('/upload', function () {
    return response()->json(['status' => 'ok'], 200);
});

/* Route::post('/upload', function (Request $request) {
    return response()->json([
        'ok' => true,
        'headers' => $request->headers->all(),
    ]);
});
 */