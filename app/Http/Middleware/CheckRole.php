<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verifica se o usuário está logado
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // 2. Verifica se o cargo do usuário está na lista de cargos permitidos para a rota
        if (! in_array($request->user()->role, $roles)) {
            // Se não tiver permissão, impede o acesso (Erro 403 - Proibido)
            abort(403, 'Acesso não autorizado para o seu tipo de usuário.');
        }

        return $next($request);
    }
}
