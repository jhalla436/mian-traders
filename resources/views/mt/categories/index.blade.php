@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Categories</h2>
    <a href="{{ route('mt.categories.create') }}" class="btn btn-success btn-sm">+ Add Category</a>
  </div>

  @if(count($companies) > 0)
    <div class="filter-section" style="margin-bottom: 20px; padding: 12px; background: #f9f9f9; border-radius: 4px;">
      <form method="GET" action="{{ route('mt.categories.index') }}" style="display: flex; gap: 10px; align-items: center;">
        <label for="company_filter" style="margin: 0;">Filter by Company:</label>
        <select name="company_id" id="company_filter" class="mt-select" style="flex: 1; max-width: 300px;">
          <option value="">-- All Companies --</option>
          @foreach($companies as $comp)
            <option value="{{ $comp->id }}" @selected($companyId == $comp->id)>{{ $comp->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
      </form>
    </div>
  @endif

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Company</th>
          <th>Type</th>
          <th>Group</th>
          <th>Sub-Categories</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $c)
          <tr>
            <td class="font-bold">{{ $c->name }}</td>
            <td>{{ $c->company?->name ?? '-' }}</td>
            <td>{{ $c->parent_id ? '📁 Sub-category' : '📦 Root' }}</td>
            <td>{{ $c->group_key ?? '-' }}</td>
            <td>
              @if($c->children->count() > 0)
                <span class="badge badge-info">{{ $c->children->count() }}</span>
              @else
                -
              @endif
            </td>
            <td>
              <div class="actions">
                @if(!$c->parent_id)
                  <a href="{{ route('mt.categories.create', ['parent_id' => $c->id]) }}" class="btn btn-sm btn-primary" title="Add sub-category">+ Sub</a>
                  <a href="{{ route('mt.categories.sizes', $c) }}" class="btn btn-sm btn-info" title="Manage sizes">📐 Sizes ({{ $c->sizes->count() }})</a>
                @endif
                <a href="{{ route('mt.categories.edit', $c) }}" class="btn btn-secondary btn-xs">Edit</a>
                <a href="{{ route('mt.categories.confirm_delete', $c) }}" class="btn btn-danger btn-xs" title="Delete category">Delete</a>
              </div>
            </td>
          </tr>
          @if($c->children->count() > 0)
            @foreach($c->children as $child)
              <tr style="background: #fafafa;">
                <td style="padding-left: 40px;">└─ {{ $child->name }}</td>
                <td>{{ $child->company?->name ?? '-' }}</td>
                <td>📁 Sub-category</td>
                <td>{{ $child->group_key ?? '-' }}</td>
                <td>-</td>
                <td>
                  <div class="actions">
                    <a href="{{ route('mt.categories.edit', $child) }}" class="btn btn-secondary btn-xs">Edit</a>
                    <a href="{{ route('mt.categories.confirm_delete', $child) }}" class="btn btn-danger btn-xs" title="Delete category">Delete</a>
                  </div>
                </td>
              </tr>
            @endforeach
          @endif
        @empty
          <tr class="empty-row"><td colspan="6">No categories yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $categories->links() }}</div>
  </div>
@endsection
