@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Edit Discount Type</h2>
    <a href="{{ route('mt.discount_types.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="{{ route('mt.discount_types.update', $discountType) }}" class="flex gap-12 flex-wrap items-center">
      @csrf @method('PUT')
      <input name="name" value="{{ $discountType->name }}" required class="mt-input" style="min-width:300px;max-width:400px;">
      <label class="form-checkbox"><input type="checkbox" name="is_active" value="1" @checked($discountType->is_active)> Active</label>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
    @if($errors->any())
      <div class="errors-box" style="margin-top:16px;">
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif
  </div>
@endsection
