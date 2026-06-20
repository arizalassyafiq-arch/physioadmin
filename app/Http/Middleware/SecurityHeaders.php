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
        $headers = $response->headers;
        $formActionSources = implode(' ', $this->formActionSources());

        $defaults = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'same-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Content-Security-Policy' => "frame-ancestors 'none'; base-uri 'self'; form-action {$formActionSources}",
        ];

        foreach ($defaults as $name => $value) {
            if (! $headers->has($name)) {
                $headers->set($name, $value);
            }
        }

        return $response;
    }

    /**
     * Allow same-origin form posts and the canonical app origin configured for production proxies.
     *
     * @return list<string>
     */
    private function formActionSources(): array
    {
        $sources = ["'self'"];
        $appOrigin = $this->originFromUrl((string) config('app.url'));

        if ($appOrigin !== null) {
            $sources[] = $appOrigin;
        }

        return array_values(array_unique($sources));
    }

    private function originFromUrl(string $url): ?string
    {
        $parts = parse_url($url);

        if (! is_array($parts)) {
            return null;
        }

        $scheme = $parts['scheme'] ?? null;
        $host = $parts['host'] ?? null;

        if (! in_array($scheme, ['http', 'https'], true) || ! is_string($host) || $host === '') {
            return null;
        }

        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return "{$scheme}://{$host}{$port}";
    }
}
