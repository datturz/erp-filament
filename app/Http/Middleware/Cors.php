<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
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
        $response = $next($request);

        $origin = $request->header('Origin');
        
        // Check if origin is allowed
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:3001',
            'https://erp-filament.vercel.app',
        ];
        
        // Allow any Vercel preview URL
        if ($origin && (in_array($origin, $allowedOrigins) || preg_match('#^https://.*\.vercel\.app$#', $origin))) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}