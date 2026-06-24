@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Variants – {{ $heading->name }}</h2>
    <div class="page-actions">
      <a href="{{ route('mt.headings.index') }}" class="btn btn-secondary btn-sm">← Back</a>
      <a href="{{ route('mt.variants.create', $heading) }}" class="btn btn-primary btn-sm">+ Add Variant</a>
    </div>
  </div>
  <div class="card">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;">
      @foreach($variants as $v)
        <div class="cart-item" style="text-align:center;">
          @if($v->image_path)
            <img src="{{ asset('storage/'.$v->image_path) }}" style="width:100%;height:120px;object-fit:cover;border-radius:10px;background:#f1f5f9;" />
          @else
            <div style="height:120px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;" class="text-muted text-sm">No Image</div>
          @endif
          <div style="font-size:20px;font-weight:800;margin-top:10px;color:#0f172a;">#{{ $v->variant_code }}</div>
          <div class="text-sm text-muted">Sell: {{ $v->selling_price_default ?? '-' }}</div>
          <form method="POST" action="{{ route('mt.variants.destroy', [$heading, $v]) }}" style="margin-top:10px;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-xs" style="width:100%;">Delete</button>
          </form>
        </div>
      @endforeach
      @if($variants->count() === 0)
        <div class="text-muted text-sm" style="padding:24px;border:1px dashed #e2e8f0;border-radius:12px;">No variants yet.</div>
      @endif
    </div>
  </div>
@endsection
