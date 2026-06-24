<?php

namespace App\Http\Controllers;

use App\Support\ShopContext;
use Illuminate\Http\Request;

class ShopSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required'],
        ]);

        $shopId = $data['shop_id'];
        $user = $request->user();

        if (($user->role ?? '') === 'admin' && $shopId === 'all') {
            session(['shop_id' => 'all']);
            return back()->with('success', 'Switched to All Shops.');
        }

        if (!is_numeric($shopId)) {
            return back()->with('error', 'Invalid shop.');
        }

        $shopIdInt = (int)$shopId;
        $allowed = ShopContext::allowedShops()->pluck('id')->contains($shopIdInt);
        if (!$allowed) {
            return back()->with('error', 'You do not have access to that shop.');
        }

        session(['shop_id' => $shopIdInt]);
        return back()->with('success', 'Shop changed.');
    }
}
