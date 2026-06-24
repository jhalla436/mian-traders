<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VariantController extends Controller
{
    public function index(Product $heading)
    {
        abort_unless($heading->is_variant_parent, 404);

        $variants = $heading->variants()->orderBy('variant_code')->get();
        return view('mt/variants/index', compact('heading', 'variants'));
    }

    public function create(Product $heading)
    {
        abort_unless($heading->is_variant_parent, 404);

        return view('mt/variants/create', compact('heading'));
    }

    public function store(Request $request, Product $heading)
    {
        abort_unless($heading->is_variant_parent, 404);

        $data = $request->validate([
            'variant_code' => ['required','string','max:50'],
            'selling_price_default' => ['nullable','numeric','min:0'],
            'image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
        ]);

        // unique code per heading
        $exists = $heading->variants()->where('variant_code', $data['variant_code'])->exists();
        if ($exists) {
            return back()->with('error', 'This code already exists in this heading.')->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('variants', 'public');
        }

        $heading->variants()->create([
            'variant_code' => $data['variant_code'],
            'selling_price_default' => $data['selling_price_default'] ?? null,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('mt.variants.index', $heading)->with('success', 'Variant added.');
    }

    public function destroy(Product $heading, ProductVariant $variant)
    {
        abort_unless($heading->is_variant_parent, 404);
        abort_unless($variant->product_id === $heading->id, 404);

        if ($variant->image_path) {
            Storage::disk('public')->delete($variant->image_path);
        }

        $variant->delete();
        return back()->with('success', 'Variant deleted.');
    }
}
