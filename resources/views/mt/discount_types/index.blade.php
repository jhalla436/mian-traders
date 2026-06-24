@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Discount Types</h2>
    <a href="{{ route('mt.discount_types.create') }}" class="btn btn-primary btn-sm">+ Add</a>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($types as $t)
          <tr>
            <td class="font-bold">{{ $t->name }}</td>
            <td><span class="badge {{ $t->is_active ? 'badge-success' : 'badge-danger' }}">{{ $t->is_active ? 'Yes' : 'No' }}</span></td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.discount_types.edit', $t) }}" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="{{ route('mt.discount_types.destroy', $t) }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
        @if($types->count()===0)
          <tr class="empty-row"><td colspan="3">No discount types yet.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
@endsection
