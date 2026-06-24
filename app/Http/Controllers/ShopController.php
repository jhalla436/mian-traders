<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::orderBy('id')->get();
        return view('mt.shops.index', compact('shops'));
    }

    public function create()
    {
        return view('mt.shops.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:160'],
            'address' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:40'],
            'owner_name' => ['nullable','string','max:160'],
            'is_active' => ['nullable'],
        ]);

        Shop::create([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'owner_name' => $data['owner_name'] ?? null,
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ]);

        return redirect()->route('mt.shops.index')->with('success', 'Shop created.');
    }

    public function edit(Shop $shop)
    {
        return view('mt.shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $data = $request->validate([
            'name' => ['required','string','max:160'],
            'address' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:40'],
            'owner_name' => ['nullable','string','max:160'],
            'is_active' => ['nullable'],
        ]);

        $shop->update([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'owner_name' => $data['owner_name'] ?? null,
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ]);

        return redirect()->route('mt.shops.index')->with('success', 'Shop updated.');
    }
}
