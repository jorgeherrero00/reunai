<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AuthenticateWithApiToken
{
   public function handle(Request $request, Closure $next)
{



    \Log::info('🔑 Token recibido', ['token' => $request->bearerToken()]);
$token = $request->bearerToken();
    $user = User::where('api_token', $token)->first();

if (!$user) {
    return response()->json(['error' => 'Token inválido'], 401);
}

// En lugar de loguear → simplemente lo guardas en la request
$request->attributes->set('api_user', $user);

return $next($request);
}

}
