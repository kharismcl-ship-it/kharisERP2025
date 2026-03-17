<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Domain-based homepage redirect.
 *
 * When a vanity domain (e.g. khariscourt.com, alphafarms.org) hits the
 * root URL ("/"), this middleware redirects to the module-specific entry
 * path configured in config/domains.php.
 *
 * All other paths and all Livewire/Filament AJAX requests pass through
 * untouched — this only fires on the bare root URL.
 */
class DomainRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only intercept bare root requests — let everything else through.
        if ($request->path() !== '/') {
            return $next($request);
        }

        $host = strtolower($request->getHost());
        $map  = config('domains.redirect_map', []);

        if (isset($map[$host])) {
            return redirect($map[$host]);
        }

        return $next($request);
    }
}
