<?php

namespace App\Http\Controllers;

use App\Models\DiscountType;
use Illuminate\Http\Request;

class DiscountTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index()
    {
        $types = DiscountType::orderBy('name')->get();
        return view('mt/discount_types/index', compact('types'));
    }

    public function create()
    {
        return view('mt/discount_types/create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:discount_types,name'],
            'is_active' => ['nullable'],
        ]);

        DiscountType::create([
            'name' => trim($data['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('mt.discount_types.index')->with('success','Discount type added.');
    }

    public function edit(DiscountType $discountType)
    {
        return view('mt/discount_types/edit', compact('discountType'));
    }

    public function update(Request $request, DiscountType $discountType)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:discount_types,name,'.$discountType->id],
            'is_active' => ['nullable'],
        ]);

        $discountType->update([
            'name' => trim($data['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('mt.discount_types.index')->with('success','Discount type updated.');
    }

    public function destroy(DiscountType $discountType)
    {
        $discountType->delete();
        return back()->with('success','Discount type deleted.');
    }
}
