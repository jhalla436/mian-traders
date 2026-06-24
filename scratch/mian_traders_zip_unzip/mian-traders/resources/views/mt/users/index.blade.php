@extends('mt.layouts.app')

@section('content')
@php
  $q = $q ?? '';
  $role = $role ?? '';
  $roles = ['' => 'All', 'admin'=>'Admin', 'manager'=>'Manager', 'cashier'=>'Cashier'];

  $pill = function($active){
    return $active ? 'background:#111827;color:#fff;' : 'background:#fff;color:#111827;border:1px solid #e5e7eb;';
  };
@endphp

<div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
  <h2 style="margin:0;">Users</h2>

  <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <input type="hidden" name="role" value="{{ $role }}">
    <input name="q" value="{{ $q }}" placeholder="Search name/email..."
           style="padding:10px;border:1px solid #ddd;border-radius:10px;">
    <button style="padding:10px 12px;border:0;border-radius:10px;background:#2563eb;color:#fff;">Search</button>
  </form>
</div>

<div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    @foreach($roles as $k=>$label)
      <a href="{{ route('mt.users.index', ['role'=>$k]) }}"
         style="padding:8px 12px;border-radius:999px;text-decoration:none;{{ $pill($role===$k) }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <a href="{{ route('mt.users.create') }}"
     style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;">
    + Add User
  </a>
</div>

<div style="background:#fff;padding:14px;border-radius:10px;">
  <table style="width:100%;border-collapse:collapse;">
    <thead>
      <tr>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Name</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Email</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Role</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Status</th>
        <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $u)
        <tr>
          <td style="border-bottom:1px solid #f2f2f2;padding:8px;font-weight:800;">{{ $u->name }}</td>
          <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $u->email }}</td>
          <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $u->role }}</td>
          <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ (int)$u->is_active === 1 ? 'Active' : 'Disabled' }}</td>
          <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
            <a href="{{ route('mt.users.edit', $u) }}" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">
              Edit / Reset
            </a>
            <form method="POST" action="{{ route('mt.users.destroy', $u) }}" style="display:inline-block;margin-left:6px;" onsubmit="return confirm('Delete user?')">
              @csrf
              @method('DELETE')
              <button style="padding:6px 10px;border-radius:8px;border:0;background:#b91c1c;color:#fff;cursor:pointer;">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" style="padding:12px;">No users found.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div style="margin-top:12px;">
    {{ $users->links() }}
  </div>
</div>
@endsection
