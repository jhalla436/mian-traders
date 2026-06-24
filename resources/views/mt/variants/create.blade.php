@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Variant – {{ $heading->name }}</h2>
    <a href="{{ route('mt.variants.index', $heading) }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="{{ route('mt.variants.store', $heading) }}" enctype="multipart/form-data" class="flex gap-12 flex-wrap items-center">
      @csrf
      <input name="variant_code" placeholder="Code number (e.g. 101)" required class="mt-input" style="min-width:200px;max-width:260px;">
      <input name="selling_price_default" type="number" step="0.01" placeholder="Default sell price" class="mt-input" style="min-width:200px;max-width:260px;">
      <input name="image" type="file" accept="image/*" class="mt-input" style="min-width:240px;max-width:300px;padding:8px;">
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
    @if($errors->any())
      <div class="errors-box" style="margin-top:16px;">
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif
  </div>
@endsection
