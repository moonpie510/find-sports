<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Миддлвар для проверки наличия токена в заголовках.
 *
 * Токен нужен чтобы можно было аутентифицировать пользователя.
 */
class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header(User::TOKEN);

        if (empty($token)) {
            return ApiResponseHelper::error(message: 'Не найден токен пользователя в заголовке User-Token', code: 401);
        }

        return $next($request);
    }
}
