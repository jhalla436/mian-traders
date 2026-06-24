@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Companies</h2>
    <div class="page-actions">
      <form method="GET" action="{{ route('mt.companies.index') }}" class="flex gap-8 items-center">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search company..." class="mt-input" style="width:220px;">
        <button class="btn btn-secondary btn-sm">Search</button>
      </form>
      <a href="{{ route('mt.companies.create') }}" class="btn btn-primary btn-sm">+ Add Company</a>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Group</th>
          <th>Phone</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($companies as $c)
          <tr>
            <td>
              <div class="font-bold">{{ $c->name }}</div>
              @if(!empty($c->address))
                <div class="text-xs text-muted">{{ $c->address }}</div>
              @endif
            </td>
            <td>{{ $c->group_key ?? '-' }}</td>
            <td>{{ $c->phone_main ?? '-' }}</td>
            <td><span class="badge {{ $c->is_active ? 'badge-success' : 'badge-danger' }}">{{ $c->is_active ? 'Yes' : 'No' }}</span></td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.company_contacts.index', $c) }}" class="btn btn-success btn-xs">Contacts</a>
                <a href="{{ route('mt.company_ledger.index', $c) }}" class="btn btn-teal btn-xs">Ledger</a>
                <a href="{{ route('mt.companies.edit', $c) }}" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="{{ route('mt.companies.destroy', $c) }}" onsubmit="return confirm('Delete this company?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="5">No companies added yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $companies->links() }}</div>
  </div>
@endsection
