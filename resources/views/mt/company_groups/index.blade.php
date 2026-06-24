@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Company Groups</h2>
    <div class="page-actions">
      <form method="GET" class="flex gap-8 items-center">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search name/key..." class="mt-input" style="width:200px;">
        <button class="btn btn-primary btn-sm">Search</button>
      </form>
      <a href="{{ route('mt.company_groups.create') }}" class="btn btn-success btn-sm">+ Add Group</a>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Key</th>
          <th class="text-right">Sort</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td class="font-bold">{{ $r->name }}</td>
            <td>{{ $r->key }}</td>
            <td class="text-right">{{ (int)$r->sort_order }}</td>
            <td><span class="badge {{ (int)$r->is_active === 1 ? 'badge-success' : 'badge-danger' }}">{{ (int)$r->is_active === 1 ? 'Active' : 'Disabled' }}</span></td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.company_groups.edit', $r) }}" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="{{ route('mt.company_groups.destroy', $r) }}" onsubmit="return confirm('Delete group?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="5">No groups found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $rows->links() }}</div>
  </div>
@endsection
