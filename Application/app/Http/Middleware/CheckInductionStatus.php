<?php

namespace App\Http\Middleware;

use App\Models\CorporateContent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckInductionStatus
{
    /**
     * Redirige a los usuarios autenticados con inducción pendiente hacia el flujo obligatorio
     * únicamente cuando tienen contenidos obligatorios activos aplicables a su perfil
     * (excluyendo los roles admin y developer).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        if ($user->induction_status !== 'pending') {
            return $next($request);
        }

        $isAllowed = $request->routeIs(
            'induction.*',
            'logout',
            'developer.*',
            'dashboard.corporate-contents.token',
            'dashboard.corporate-contents.stream'
        ) || $request->is('induction', 'induction/*', 'logout', 'backoffice/developer*');

        if ($isAllowed) {
            return $next($request);
        }

        if ($user->hasAnyRole(['admin', 'developer'])) {
            return $next($request);
        }

        $hasMandatoryContents = CorporateContent::visibleTo($user)
            ->where('corporate_contents.is_mandatory', true)
            ->exists();

        if (!$hasMandatoryContents) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Debes completar la inducción corporativa obligatoria.',
                'redirect_url' => route('induction.index'),
            ], 403);
        }

        return redirect()->route('induction.index');
    }
}
