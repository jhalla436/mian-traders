@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Edit Shop</h2>
    <a href="{{ route('mt.shops.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:720px;">
    <form method="POST" action="{{ route('mt.shops.update', $shop) }}" class="form-stack">
      @csrf @method('PUT')
      <div class="form-group">
        <label class="form-label">Shop Name</label>
        <input name="name" value="{{ $shop->name }}" placeholder="Shop name" required class="mt-input">
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Owner Name</label>
          <input name="owner_name" value="{{ $shop->owner_name }}" placeholder="Owner name" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input name="phone" value="{{ $shop->phone }}" placeholder="Phone" class="mt-input">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Address</label>
        <input name="address" value="{{ $shop->address }}" placeholder="Address" class="mt-input">
      </div>
      <label class="form-checkbox">
        <input type="checkbox" name="is_active" {{ $shop->is_active ? 'checked' : '' }}>
        Active
      </label>
      <button class="btn btn-primary" style="justify-self:start;">Update Shop</button>
    </form>
  </div>
@endsection
