<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditTrail
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip audit for certain routes
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $response = $next($request);

        // Log the request after processing
        $this->logRequest($request, $response);

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        // Skip audit for health checks, assets, and high-frequency endpoints
        $skipPatterns = [
            'health',
            'assets/*',
            '*.js',
            '*.css',
            '*.png',
            '*.jpg',
            '*.gif',
            '*.ico',
            '*.svg',
            'telescope/*',
        ];

        foreach ($skipPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        // Skip GET requests to certain endpoints
        if ($request->isMethod('GET') && $request->is('api/*/dashboard')) {
            return true;
        }

        return false;
    }

    private function logRequest(Request $request, Response $response): void
    {
        // Only log certain types of requests
        if (!$this->shouldLog($request, $response)) {
            return;
        }

        $data = [
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'request_size' => strlen($request->getContent()),
            'response_size' => strlen($response->getContent()),
            'duration' => $this->getRequestDuration($request),
        ];

        // Add request data for certain methods
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $data['request_data'] = $this->sanitizeRequestData($request->all());
        }

        // Log critical operations
        if ($this->isCriticalOperation($request)) {
            $data['critical'] = true;
        }

        AuditLog::create([
            'event' => 'api_request',
            'auditable_type' => 'App\Http\Request',
            'auditable_id' => null,
            'user_id' => auth()->id(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'additional_data' => $data,
        ]);
    }

    private function shouldLog(Request $request, Response $response): bool
    {
        // Log all API requests
        if ($request->is('api/*')) {
            return true;
        }

        // Log failed requests
        if ($response->getStatusCode() >= 400) {
            return true;
        }

        // Log critical operations
        if ($this->isCriticalOperation($request)) {
            return true;
        }

        return false;
    }

    private function isCriticalOperation(Request $request): bool
    {
        $criticalPatterns = [
            'api/*/sales/process',
            'api/*/transfers/*/approve',
            'api/*/transfers/*/ship',
            'api/*/transfers/*/receive',
            'api/*/inventory/adjust',
            'api/*/batches/*/progress',
            'api/*/cycle-counts/*/apply-adjustments',
            'admin/*',
        ];

        foreach ($criticalPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        // Log all POST, PUT, DELETE operations on important resources
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            $importantResources = [
                'products',
                'materials',
                'batches',
                'transfers',
                'sales',
                'users',
                'stores',
            ];

            foreach ($importantResources as $resource) {
                if ($request->is("api/*/{$resource}/*") || $request->is("api/*/{$resource}")) {
                    return true;
                }
            }
        }

        return false;
    }

    private function sanitizeRequestData(array $data): array
    {
        // Remove sensitive data
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'ssn',
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }

        return $data;
    }

    private function getRequestDuration(Request $request): float
    {
        if (defined('LARAVEL_START')) {
            return round((microtime(true) - LARAVEL_START) * 1000, 2);
        }

        return 0;
    }
}