<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Only admin/manager can manage categories
        $this->middleware('role:admin,manager');
    }

    public function index(Request $request)
    {
        $companyId = $request->get('company_id');
        
        // Get all companies for filter
        $companies = Company::orderBy('name')->get();
        
        // Build query: show root categories (parent_id is null)
        $categories = Category::whereNull('parent_id')
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->orderBy('name')
            ->with('children')
            ->paginate(25);
        
        return view('mt.categories.index', compact('categories', 'companies', 'companyId'));
    }

    public function create(Request $request)
    {
        $category = new Category();
        $companies = Company::orderBy('name')->get();
        
        // Pre-select company if provided
        $selectedCompanyId = $request->get('company_id');
        
        // If a parent category is specified, get it
        $parentId = $request->get('parent_id');
        $parentCategory = $parentId ? Category::find($parentId) : null;
        
        // If creating a child category, inherit the company from parent
        if ($parentCategory && $selectedCompanyId === null) {
            $selectedCompanyId = $parentCategory->company_id;
        }

        // Get potential parent categories (only from the same company, excluding self and descendants)
        $availableParents = $parentCategory 
            ? Category::query()
                ->where('id', '!=', $parentCategory->id)
                ->when($parentCategory->company_id, function ($q) use ($parentCategory) {
                    $q->where('company_id', $parentCategory->company_id);
                })
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get()
            : Category::whereNull('parent_id')
                ->when($selectedCompanyId, function ($q) use ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                })
                ->orderBy('name')
                ->get();
        
        return view('mt.categories.create', compact(
            'category',
            'companies',
            'selectedCompanyId',
            'parentCategory',
            'parentId',
            'availableParents'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $category = Category::create($data);

        $copiedSizes = 0;
        if (!empty($data['parent_id'])) {
            $parent = Category::find($data['parent_id']);
            if ($parent) {
                $copiedSizes = $category->copySizesFrom($parent);
            }
        } elseif (!empty($data['company_id'])) {
            $sourceCategory = Category::where('company_id', $data['company_id'])
                ->where('id', '!=', $category->id)
                ->whereHas('sizes')
                ->orderBy('id')
                ->first();
            if ($sourceCategory) {
                $copiedSizes = $category->copySizesFrom($sourceCategory);
            }
        }

        $message = 'Category created.';
        if ($data['company_id'] ?? null) {
            $company = Company::find($data['company_id']);
            if ($company) {
                $message .= ' (' . $company->name . ')';
            }
        }
        if ($copiedSizes > 0) {
            $message .= ' ' . $copiedSizes . ' size(s) copied automatically.';
        }

        return redirect()->route('mt.categories.index', ['company_id' => $data['company_id'] ?? null])
            ->with('success', $message);
    }

    public function edit(Category $category)
    {
        $companies = Company::orderBy('name')->get();
        $selectedCompanyId = null;
        
        // Get potential parent categories (only from the same company, excluding self and descendants)
        $availableParents = Category::query()
            ->where('id', '!=', $category->id)
            ->when($category->company_id, function ($q) use ($category) {
                $q->where('company_id', $category->company_id);
            })
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
        
        return view('mt.categories.edit', compact('category', 'companies', 'selectedCompanyId', 'availableParents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validated($request, $category->id);
        $category->update($data);
        
        $message = 'Category updated.';
        if ($data['company_id'] ?? null) {
            $company = Company::find($data['company_id']);
            if ($company) {
                $message .= ' (' . $company->name . ')';
            }
        }
        
        return redirect()->route('mt.categories.index', ['company_id' => $category->company_id ?? null])
            ->with('success', $message);
    }

    /**
     * Show confirmation page for category deletion with product reassignment options.
     */
    public function confirmDelete(Category $category)
    {
        $companyId = $category->company_id;
        
        // Check if category has child categories
        $childCount = Category::where('parent_id', $category->id)->count();
        if ($childCount > 0) {
            return redirect()->route('mt.categories.index', ['company_id' => $companyId ?? null])
                ->with('error', "Cannot delete category '{$category->name}' - it has {$childCount} sub-category(s). Please delete them first.");
        }
        
        $productCount = Product::where('category_id', $category->id)->count();
        
        // Get alternative categories (same company, excluding current)
        $alternateCategories = Category::query()
            ->where('id', '!=', $category->id)
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->orderBy('name')
            ->get();
        
        return view('mt.categories.confirm_delete', compact(
            'category',
            'productCount',
            'alternateCategories',
            'companyId'
        ));
    }

    /**
     * Reassign products and delete the category.
     */
    public function reassignAndDelete(Request $request, Category $category)
    {
        $companyId = $category->company_id;
        
        if ($request->input('action') === 'cancel') {
            return redirect()->route('mt.categories.index', ['company_id' => $companyId ?? null]);
        }
        
        // Validate new category
        $data = $request->validate([
            'new_category_id' => ['required', 'exists:categories,id'],
        ]);
        
        $newCategoryId = (int)$data['new_category_id'];
        
        // Ensure new category is not the same as current
        if ($newCategoryId === $category->id) {
            return back()->with('error', 'Please select a different category.');
        }
        
        // Reassign all products to new category
        Product::where('category_id', $category->id)
            ->update(['category_id' => $newCategoryId]);
        
        $productCount = Product::where('category_id', $newCategoryId)
            ->where('id', '!=', $category->id) // Don't count in updated query
            ->count();
        
        // Delete the category
        $category->delete();
        
        return redirect()->route('mt.categories.index', ['company_id' => $companyId ?? null])
            ->with('success', "Category deleted. {$productCount} product(s) reassigned to the new category.");
    }

    public function destroy(Category $category)
    {
        $companyId = $category->company_id;
        
        // Check if category has products
        $productCount = Product::where('category_id', $category->id)->count();
        if ($productCount > 0) {
            return redirect()->route('mt.categories.confirm_delete', $category)
                ->with('info', "This category has {$productCount} product(s). Please reassign them before deletion.");
        }
        
        // Check if category has child categories
        $childCount = Category::where('parent_id', $category->id)->count();
        if ($childCount > 0) {
            return redirect()->route('mt.categories.index', ['company_id' => $companyId ?? null])
                ->with('error', "Cannot delete category '{$category->name}' - it has {$childCount} sub-category(s). Please delete them first.");
        }
        
        $category->delete();
        
        return redirect()->route('mt.categories.index', ['company_id' => $companyId ?? null])
            ->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'group_key' => ['nullable', 'string', 'in:foam,uncovered_foam,hardware,fabric,spring,accessories,other'],
            'unit_type' => ['required', 'string', 'in:unit,meter,kg,sqft'],
        ]);
    }
}
