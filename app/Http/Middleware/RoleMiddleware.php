<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        //admin can access any route
        if (auth()->user()->role === UserRoleEnum::ADMIN->value) {
            return $next($request);
        } elseif (!in_array(auth()->user()->role, $roles)) { //certain routes are only for certain roles
            abort(Response::HTTP_FORBIDDEN, __('messages.no_permission'));
        }

        //rest are open to anyone
        return $next($request);
    }
}
