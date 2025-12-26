<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use App\Payments\Support\ApiKeyHasher;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = trim(substr($header, 7));

        if ($token === '') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $hashed = ApiKeyHasher::hash($token);
        $merchant = Merchant::where('api_key_hash', $hashed)->first();

        if (! $merchant || ! ApiKeyHasher::check($token, $merchant->api_key_hash)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($merchant->status !== 'active') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->attributes->set('merchant', $merchant);

        return $next($request);
    }
}
