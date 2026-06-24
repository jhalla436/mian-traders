<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Only admin/manager can manage categories
        $this->middleware('role:admin,manager');
    }

    public function index()
    {
        $categories = Category::orderBy('name')->paginate(25);
        return view('mt.categories.index', compact('categories'));
    }

    public function create()
    {
        $category = new Category();
        return view('mt.categories.create', compact('category'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Category::create($data);
        return redirect()->route('mt.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('mt.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validated($request, $category->id);
        $category->update($data);
        return redirect()->route('mt.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('mt.categories.index')->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_key' => ['nullable', 'string', 'in:foam,hardware,fabric,spring,accessories,other'],
            'unit_type' => ['required', 'string', 'in:unit,meter,kg,sqft'],
        ]);
    }
}
