<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategorySize;
use Illuminate\Http\Request;

class CategorySizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    /**
     * Show sizes for a category.
     */
    public function index(Category $category)
    {
        $sizes = $category->sizes;
        return view('mt.categories.sizes', compact('category', 'sizes'));
    }

    /**
     * Store a new size for a category (single or bulk).
     */
    public function store(Request $request, Category $category)
    {
        // Check if bulk upload is being used
        if ($request->filled('bulk_sizes')) {
            return $this->storeBulk($request, $category);
        }

        $data = $request->validate([
            'length_in' => ['required', 'numeric', 'min:0.01'],
            'width_in' => ['required', 'numeric', 'min:0.01'],
            'height_in' => ['required', 'numeric', 'min:0.01'],
            'size_type' => ['required', 'in:standard,uncovered_sofa,uncovered_slab'],
        ]);

        CategorySize::create([
            'category_id' => $category->id,
            ...$data
        ]);

        return back()->with('success', "Size {$data['length_in']}×{$data['width_in']}×{$data['height_in']} added.");
    }

    /**
     * Store multiple sizes from bulk input.
     * Format: "72x36x4" or "72x36x4\"" (one per line)
     */
    private function storeBulk(Request $request, Category $category)
    {
        $data = $request->validate([
            'bulk_sizes' => ['required', 'string'],
            'size_type' => ['required', 'in:standard,uncovered_sofa,uncovered_slab'],
        ]);

        $bulkSizes = $data['bulk_sizes'];
        $sizeType = $data['size_type'];
        $rawSizes = [];
        preg_match_all('/\d+(?:\.\d+)?(?:\s*[×xX]\s*|\s+)\d+(?:\.\d+)?(?:\s*[×xX]\s*|\s+)\d+(?:\.\d+)?/u', $bulkSizes, $matches);
        $rawSizes = $matches[0] ?? [];
        preg_match_all('/\d+(?:\.\d+)?(?:\s*-\s*)\d+(?:\.\d+)?(?:\s*-\s*)\d+(?:\.\d+)?/u', $bulkSizes, $dashMatches);
        $rawSizes = array_merge($rawSizes, $dashMatches[0] ?? []);

        $added = 0;
        $skipped = 0;
        $errors = [];

        if (empty($rawSizes)) {
            return back()->withErrors(['bulk_sizes' => 'No valid sizes were found. Use formats like 72x36x4, 72×39×4, or 72 36 4.']);
        }

        foreach ($rawSizes as $line) {
            $raw = trim(mb_strtolower($line));
            $normalized = preg_replace('/\s*[×xX]\s*/u', 'x', $raw);
            $normalized = preg_replace('/\s*-\s*/u', 'x', $normalized);
            $normalized = preg_replace('/\s+/u', 'x', $normalized);
            $normalized = str_replace(['"', '”', ',', ';'], '', $normalized);
            $parts = array_values(array_filter(explode('x', $normalized), fn($value) => $value !== ''));

            if (count($parts) !== 3) {
                $errors[] = "Invalid format: '$line' (use format: 72x36x4 or 72×39×4)";
                continue;
            }

            $length = (float) trim($parts[0]);
            $width = (float) trim($parts[1]);
            $height = (float) trim($parts[2]);

            if ($length < 0.01 || $width < 0.01 || $height < 0.01) {
                $errors[] = "Invalid dimensions in '$line' (must be > 0.01)";
                continue;
            }

            // Check if size already exists
            $exists = CategorySize::where('category_id', $category->id)
                ->where('length_in', $length)
                ->where('width_in', $width)
                ->where('height_in', $height)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            CategorySize::create([
                'category_id' => $category->id,
                'length_in' => $length,
                'width_in' => $width,
                'height_in' => $height,
                'size_type' => $sizeType,
            ]);

            $added++;
        }

        $message = "✓ Added {$added} size(s)";
        if ($skipped > 0) {
            $message .= ", skipped {$skipped} (already exist)";
        }
        if (count($errors) > 0) {
            $message .= ". " . count($errors) . " error(s): " . implode('; ', array_slice($errors, 0, 3));
        }

        return back()->with('success', $message);
    }

    public function destroySelected(Request $request, Category $category)
    {
        $data = $request->validate([
            'size_ids' => ['required', 'array'],
            'size_ids.*' => ['integer', 'exists:category_sizes,id']
        ]);

        $ids = $data['size_ids'] ?? [];
        if (count($ids) === 0) {
            return back()->with('error', 'No sizes selected.');
        }

        $deleted = CategorySize::where('category_id', $category->id)
            ->whereIn('id', $ids)
            ->delete();

        return back()->with('success', "Deleted {$deleted} selected size(s).");
    }

    /**
     * Delete a size from a category.
     */
    public function destroy(Category $category, CategorySize $size)
    {
        if ($size->category_id !== $category->id) {
            abort(403);
        }

        $displayName = $size->getDisplayName();
        $size->delete();

        return back()->with('success', "Size {$displayName} deleted.");
    }

    public function destroyAll(Category $category)
    {
        $count = $category->sizes()->count();
        if ($count === 0) {
            return back()->with('info', 'No sizes to delete.');
        }

        $category->sizes()->delete();

        return back()->with('success', "Deleted {$count} size(s) from {$category->name}.");
    }
}
