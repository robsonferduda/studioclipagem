<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // Se o usuário é cliente, redireciona para dashboard
            if (Auth::user()->hasRole('cliente')) {
                return redirect('/cliente/dashboard');
            }
            
            // Para outros usuários, usa o redirecionamento padrão
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
