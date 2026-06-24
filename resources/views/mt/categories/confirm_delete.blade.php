@extends('mt.layouts.app')

@section('content')
<div style="max-width: 600px; margin: 40px auto;">
    <div style="background: #fff; padding: 30px; border-radius: 12px; border: 1px solid #ddd;">
        <div style="text-align: center; margin-bottom: 20px;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>

        <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; text-align: center; margin-bottom: 10px;">
            Delete Category: <strong>{{ $category->name }}</strong>
        </h2>

        <p style="color: #64748b; text-align: center; margin-bottom: 20px;">
            This category has <strong style="color: #0f172a;">{{ $productCount }} product(s)</strong> assigned to it.
            Choose a category to reassign these products to, then delete this category.
        </p>

        @if ($alternateCategories->isEmpty())
            <div style="background: #fef08a; border: 1px solid #fcd34d; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #713f12;">
                <strong>⚠ No alternative categories available</strong><br>
                <small>Please create another category in the same company first, or select a different company.</small>
            </div>
            <div style="text-align: center;">
                <a href="{{ route('mt.categories.index', ['company_id' => $companyId]) }}" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 6px;">
                    Back to Categories
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('mt.categories.reassign_and_delete', $category) }}" style="display: grid; gap: 15px;">
                @csrf

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #0f172a;">Select a category to reassign products to:</label>
                    <select name="new_category_id" required style="width: 100%; padding: 10px; border: 1px solid #999; border-radius: 6px; font-size: 14px;">
                        <option value="">-- Select Category --</option>
                        @foreach($alternateCategories as $cat)
                            <option value="{{ $cat->id }}">
                                {{ $cat->name }}
                                @if($cat->company_id)
                                    ({{ $cat->company?->name ?? 'Company' }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('new_category_id')
                        <small style="color: #dc2626; display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="background: #eff6ff; padding: 12px; border-radius: 6px; border-left: 4px solid #3b82f6; color: #1e3a8a; font-size: 13px;">
                    <strong>ℹ What will happen:</strong><br>
                    • All {{ $productCount }} product(s) will be moved to the selected category<br>
                    • "{{ $category->name }}" category will be permanently deleted
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <form method="POST" action="{{ route('mt.categories.reassign_and_delete', $category) }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            Cancel
                        </button>
                    </form>

                    <button type="submit" style="padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        ✓ Reassign & Delete
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
