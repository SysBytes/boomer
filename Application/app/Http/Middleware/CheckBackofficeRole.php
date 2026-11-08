<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBackofficeRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['developer', 'admin', 'purchasing', 'supervisor'])) {
            abort(403, 'Acceso denegado. Su rol de usuario no tiene permisos para ingresar al panel de administración avanzado.');
        }

        return $next($request);
    }
}
