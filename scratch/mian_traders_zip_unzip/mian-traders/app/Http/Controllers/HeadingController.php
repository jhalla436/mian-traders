<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HeadingController extends Controller
{
    public function index()
    {
        $headings = Product::where('is_variant_parent', true)->orderBy('name')->get();
        return view('mt/headings/index', compact('headings'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('mt/headings/create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'category_id' => ['required','exists:categories,id'],
        ]);

        Product::create([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'is_variant_parent' => true,
        ]);

        return redirect()->route('mt.headings.index')->with('success', 'Heading created.');
    }

    public function destroy(Product $heading)
    {
        abort_unless($heading->is_variant_parent, 404);

        // delete variants first
        $heading->variants()->delete();
        $heading->delete();

        return back()->with('success', 'Heading deleted.');
    }
}
