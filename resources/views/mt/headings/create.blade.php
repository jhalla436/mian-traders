@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Heading</h2>
    <a href="{{ route('mt.headings.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="{{ route('mt.headings.store') }}" class="flex gap-12 flex-wrap items-center">
      @csrf
      <input name="name" placeholder="e.g. Lamination, UV Sheets, Lasani Simple" required class="mt-input" style="min-width:280px;max-width:400px;">
      <select name="category_id" required class="mt-select" style="min-width:220px;max-width:300px;">
        <option value="">Select Category</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->unit_type ?? 'unit' }})</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
    @if($errors->any())
      <div class="errors-box" style="margin-top:16px;">
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif
  </div>
@endsection
