@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Shops</h2>
    <a href="{{ route('mt.shops.create') }}" class="btn btn-primary btn-sm">+ Add Shop</a>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Owner</th>
          <th>Phone</th>
          <th>Address</th>
          <th>Status</th>
          <th class="text-right">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($shops as $s)
          <tr>
            <td>{{ $s->id }}</td>
            <td class="font-bold">{{ $s->name }}</td>
            <td>{{ $s->owner_name ?? '-' }}</td>
            <td>{{ $s->phone ?? '-' }}</td>
            <td>{{ $s->address ?? '-' }}</td>
            <td><span class="badge {{ $s->is_active ? 'badge-success' : 'badge-danger' }}">{{ $s->is_active ? 'Active' : 'Disabled' }}</span></td>
            <td class="text-right">
              <a href="{{ route('mt.shops.edit', $s) }}" class="btn btn-secondary btn-xs">Edit</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
