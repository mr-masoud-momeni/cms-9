<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;

class ResolveCurrentShop
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $context = session('current_shop');

        if (!$context || ($context['domain'] ?? null) !== $host) {
            $shop = Shop::where('domain', $host)->first();

            if (!$shop) {
                abort(404);
            }

            $context = [
                'id' => $shop->id,
                'user_id' => $shop->user_id,
                'name' => $shop->name,
                'domain' => $shop->domain,
                'slug' => $shop->slug,
            ];

            session()->put('current_shop', $context);
        }

        // Build a lightweight model from the session so the storefront
        // can use $shop->name, $shop->domain, etc. without another query.
        $shop = new Shop($context);
        $shop->exists = true;
        $shop->setRawAttributes($context);

        app()->instance(Shop::class, $shop);
        $request->attributes->set('current_shop', $shop);

        return $next($request);
    }
}
