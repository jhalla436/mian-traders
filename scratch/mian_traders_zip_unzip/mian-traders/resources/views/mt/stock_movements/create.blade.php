@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">New Stock Movement</h2>
    <a href="{{ route('mt.stock_movements.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  @if(session('error'))
    <div style="background:#fee2e2;padding:10px;border-radius:10px;margin-bottom:12px;">{{ session('error') }}</div>
  @endif

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="{{ route('mt.stock_movements.store') }}" style="display:grid;gap:12px;max-width:720px;">
      @csrf

      <div>
        <label><b>Product</b></label>
        <select name="product_id" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="">-- select --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}"
              @selected((int)old('product_id', $product?->id) === (int)$p->id)>
              {{ $p->name }} (Stock: {{ number_format((float)($p->stock_qty ?? 0),2) }})
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label><b>Type</b></label>
        <select name="type" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          @foreach($types as $k => $label)
            <option value="{{ $k }}" @selected(old('type') === $k)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label><b>Quantity</b></label>
        <input name="qty" type="number" step="0.01" required value="{{ old('qty') }}"
               style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        <div style="color:#6b7280;font-size:12px;margin-top:6px;">
          If type is <b>SET</b>, this becomes the exact stock value.
        </div>
      </div>

      <div>
        <label><b>Note</b></label>
        <input name="note" value="{{ old('note') }}" placeholder="purchase, transport, damaged, etc."
               style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>

      <button type="submit" style="padding:12px 14px;border-radius:10px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
        Save
      </button>

      @if($errors->any())
        <div style="background:#fee2e2;padding:10px;border-radius:10px;">
          @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif
    </form>
  </div>
@endsection
