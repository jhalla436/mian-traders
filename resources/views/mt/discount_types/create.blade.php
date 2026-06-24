@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Discount Type</h2>
    <a href="{{ route('mt.discount_types.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="{{ route('mt.discount_types.store') }}" class="flex gap-12 flex-wrap items-center">
      @csrf
      <input name="name" placeholder="e.g. covered, uncovered, spring, jumbolon, hardware" required class="mt-input" style="min-width:300px;max-width:400px;">
      <label class="form-checkbox"><input type="checkbox" name="is_active" value="1" checked> Active</label>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
    @if($errors->any())
      <div class="errors-box" style="margin-top:16px;">
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif
  </div>
@endsection
