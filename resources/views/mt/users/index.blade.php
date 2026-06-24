@extends('mt.layouts.app')

@section('content')
@php
  $q = $q ?? '';
  $role = $role ?? '';
  $roles = ['' => 'All', 'admin'=>'Admin', 'manager'=>'Manager', 'cashier'=>'Cashier'];
@endphp

<div class="page-header">
  <h2 class="page-title">Users</h2>
  <div class="page-actions">
    <form method="GET" class="flex gap-8 items-center">
      <input type="hidden" name="role" value="{{ $role }}">
      <input name="q" value="{{ $q }}" placeholder="Search name/email..." class="mt-input" style="width:200px;">
      <button class="btn btn-primary btn-sm">Search</button>
    </form>
  </div>
</div>

<div class="filter-bar" style="justify-content:space-between;">
  <div class="mt-tabs">
    @foreach($roles as $k=>$label)
      <a href="{{ route('mt.users.index', ['role'=>$k]) }}" class="mt-tab{{ $role===$k ? ' active' : '' }}">{{ $label }}</a>
    @endforeach
  </div>
  <a href="{{ route('mt.users.create') }}" class="btn btn-success btn-sm">+ Add User</a>
</div>

<div class="table-card">
  <table class="mt-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $u)
        <tr>
          <td class="font-bold">{{ $u->name }}</td>
          <td>{{ $u->email }}</td>
          <td><span class="badge badge-info">{{ $u->role }}</span></td>
          <td><span class="badge {{ (int)$u->is_active === 1 ? 'badge-success' : 'badge-danger' }}">{{ (int)$u->is_active === 1 ? 'Active' : 'Disabled' }}</span></td>
          <td>
            <div class="actions">
              <a href="{{ route('mt.users.edit', $u) }}" class="btn btn-secondary btn-xs">Edit / Reset</a>
              <form method="POST" action="{{ route('mt.users.destroy', $u) }}" onsubmit="return confirm('Delete user?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-xs">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr class="empty-row"><td colspan="5">No users found.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination-wrap">{{ $users->links() }}</div>
</div>
@endsection
