<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Anti-clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // Impede o navegador de "adivinhar" o tipo do arquivo (MIME sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // Não vaza URLs internas para sites externos
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // Bloqueia APIs sensíveis do navegador que o sistema não usa
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // HSTS apenas quando servido via HTTPS (não quebra o ambiente local em HTTP)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
