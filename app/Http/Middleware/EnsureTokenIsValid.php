<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\Sanctum;
use Illuminate\Auth\AuthenticationException;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $cookieHeader = $request->header('cookie');
        $cookies = explode('; ', $cookieHeader);

        foreach ($cookies as $cookie) {
            if (strpos($cookie, 'auth_token=') !== false) {
                $token = str_replace('auth_token=', '', $cookie);
                $user = Sanctum::personalAccessTokenModel()::findToken(urldecode($token));
                if($user) {
                    return $next($request);
                } 
                break;
            }
        }
        
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }

    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : url('/login');
    }
}
