@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">New Stock Movement</h2>
    <a href="{{ route('mt.stock_movements.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  @if(session('error'))
    <div class="mt-alert mt-alert-error">{{ session('error') }}</div>
  @endif
  <div class="card" style="max-width:720px;">
    <form method="POST" action="{{ route('mt.stock_movements.store') }}" class="form-stack">
      @csrf
      <div class="form-group">
        <label class="form-label">Product</label>
        <select name="product_id" required class="mt-select">
          <option value="">-- select --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}" @selected((int)old('product_id', $product?->id) === (int)$p->id)>
              {{ $p->name }} (Stock: {{ number_format((float)($p->shop_stock_qty ?? $p->stock_qty ?? 0),2) }})
            </option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Type</label>
        <select name="type" required class="mt-select">
          @foreach($types as $k => $label)
            <option value="{{ $k }}" @selected(old('type') === $k)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Quantity</label>
        <input name="qty" type="number" step="0.01" required value="{{ old('qty') }}" class="mt-input">
        <span class="text-xs text-muted">If type is <b>SET</b>, this becomes the exact stock value.</span>
      </div>
      <div class="form-group">
        <label class="form-label">Note</label>
        <input name="note" value="{{ old('note') }}" placeholder="purchase, transport, damaged, etc." class="mt-input">
      </div>
      <button type="submit" class="btn btn-primary" style="justify-self:start;">Save</button>
      @if($errors->any())
        <div class="errors-box">
          @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif
    </form>
  </div>
@endsection
