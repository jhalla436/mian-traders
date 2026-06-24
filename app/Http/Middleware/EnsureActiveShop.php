<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Shop;

class EnsureActiveShop
{
    public function handle(Request $request, Closure $next): Response
    {
        // If not logged in, just continue (auth middleware will handle)
        if (!$request->user()) {
            return $next($request);
        }

        // Admin can use "all" on dashboard/reports, but NOT on POS create actions if you want
        $user = $request->user();

        $allowedShopIds = method_exists($user, 'shops')
            ? $user->shops()->pluck('shops.id')->toArray()
            : [];

        // If user has no assigned shop, fallback to first shop in system
        if (empty($allowedShopIds)) {
            $firstShop = Shop::query()->orderBy('id')->first();
            if ($firstShop) {
                session(['shop_id' => $firstShop->id]);
            }
            return $next($request);
        }

        $shopId = session('shop_id');

        // If no shop selected in session, pick first allowed
        if (!$shopId) {
            session(['shop_id' => $allowedShopIds[0]]);
            return $next($request);
        }

        // If admin selected "all", allow it
        if ($shopId === 'all') {
            return $next($request);
        }

        // If selected shop not allowed, force to first allowed
        if (!in_array((int)$shopId, $allowedShopIds, true)) {
            session(['shop_id' => $allowedShopIds[0]]);
        }

        return $next($request);
    }
}