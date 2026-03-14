<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private $UnsupportedHeaders = [
        'X-Powered-By',
        'Server',
    ];

    public function handle(Request $request, Closure $next)
    {
        $this->removeUnsupportedHeaders($this->UnsupportedHeaders);
        $response = $next($request);
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'deny');
        $csp = "default-src 'self' https://framework-gb.cdn.gob.mx; "
            . "script-src 'self' 'unsafe-inline' https://framework-gb.cdn.gob.mx http://www.google-analytics.com http://ajax.googleapis.com https://www.google-analytics.com; "
            . "style-src 'self' 'unsafe-inline' https://framework-gb.cdn.gob.mx http://fonts.googleapis.com; "
            . "img-src 'self' http://ajax.googleapis.com https://www.google-analytics.com https://framework-gb.cdn.gob.mx; " // Agregamos https://framework-gb.cdn.gob.mx a img-src
            . "font-src 'self' https://framework-gb.cdn.gob.mx http://fonts.gstatic.com; "
            . "connect-src 'self' https://framework-gb.cdn.gob.mx https://www.google-analytics.com";
        $response->headers->set('Content-Security-Policy', $csp);


        return $response;
    }

    private function removeUnsupportedHeaders($headerList)
    {
        foreach ($headerList as $header) {
            header_remove($header);
        }
    }
}
