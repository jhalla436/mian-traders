@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Headings</h2>
    <a href="{{ route('mt.headings.create') }}" class="btn btn-primary btn-sm">+ Add Heading</a>
  </div>
  <div class="table-card">
    <table class="mt-table">
      <thead><tr><th>Heading</th><th>Category</th><th>Variants</th><th>Actions</th></tr></thead>
      <tbody>
        @foreach($headings as $h)
          <tr>
            <td class="font-bold">{{ $h->name }}</td>
            <td>{{ $h->category?->name }}</td>
            <td>{{ $h->variants()->count() }}</td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.variants.index', $h) }}" class="btn btn-secondary btn-xs">Variants</a>
                <form method="POST" action="{{ route('mt.headings.destroy', $h) }}">@csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
        @if($headings->count() === 0)
          <tr class="empty-row"><td colspan="4">No headings yet.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
@endsection
