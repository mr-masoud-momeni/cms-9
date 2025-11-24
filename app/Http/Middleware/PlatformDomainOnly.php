<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PlatformDomainOnly
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        // دامنه اصلی پلتفرم
        $mainDomain = config('app.platform_domain', 'octoshop.ir');

        if ($host !== $mainDomain) {
            // ۳ گزینه داری:
            // 1) abort(404);
            // 2) abort(403, 'Forbidden');
            redirect()->away('https://' . $mainDomain);
        }
        return $next($request);
    }
}
