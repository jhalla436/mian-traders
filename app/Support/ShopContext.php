<?php

namespace App\Support;

use App\Models\Shop;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ShopContext
{
    /**
     * Returns:
     * - int shop_id (e.g. 1,2,3)
     * - "all" for admin all-shops view
     * - null if not set
     */
    public static function activeShopId(): int|string|null
    {
        $sid = Session::get('shop_id');

        if ($sid === 'all') {
            return 'all';
        }

        if (is_numeric($sid)) {
            return (int) $sid;
        }

        return null;
    }

    public static function setActiveShopId(int|string $shopId): void
    {
        Session::put('shop_id', $shopId);
    }

    public static function isAll(): bool
    {
        return self::activeShopId() === 'all';
    }

    /**
     * Shops the current user is allowed to access.
     * Admin: all shops
     * Others: assigned shops via user_shops
     */
    public static function allowedShops(): Collection
    {
        $user = Auth::user();
        if (!$user) return collect();

        $role = $user->role ?? 'cashier';

        if ($role === 'admin') {
            return Shop::query()->orderBy('name')->get();
        }

        if (method_exists($user, 'shops')) {
            return $user->shops()->orderBy('name')->get();
        }

        return collect();
    }

    /**
     * Active shop model, or null for "all"/unset.
     */
    public static function activeShop(): ?Shop
    {
        $sid = self::activeShopId();
        if (!is_numeric($sid)) return null;

        return Shop::query()->find((int)$sid);
    }

    /**
     * POS / Purchase / Expense must always be for ONE shop.
     * If shop_id is missing or "all", it will stop with a message.
     */
    public static function requireSingleShopId(): int
    {
        $sid = self::activeShopId();

        if (is_numeric($sid)) {
            return (int)$sid;
        }

        abort(422, 'Please select a specific shop (not "All Shops") before creating a bill.');
    }
}