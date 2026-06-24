@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Variants – {{ $heading->name }}</h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a href="{{ route('mt.headings.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
      <a href="{{ route('mt.variants.create', $heading) }}" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Add Variant</a>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;">
      @foreach($variants as $v)
        <div style="border:1px solid #eee;border-radius:12px;padding:12px;text-align:center;">
          @if($v->image_path)
            <img src="{{ asset('storage/'.$v->image_path) }}" style="width:100%;height:120px;object-fit:cover;border-radius:10px;background:#f3f4f6;" />
          @else
            <div style="height:120px;border-radius:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#6b7280;">No Image</div>
          @endif

          <div style="font-size:22px;font-weight:700;margin-top:8px;">#{{ $v->variant_code }}</div>
          <div>Sell: {{ $v->selling_price_default ?? '-' }}</div>

          <form method="POST" action="{{ route('mt.variants.destroy', [$heading, $v]) }}" style="margin-top:10px;">
            @csrf
            @method('DELETE')
            <button type="submit" style="width:100%;padding:8px 12px;border-radius:8px;border:0;background:#dc2626;color:#fff;cursor:pointer;">Delete</button>
          </form>
        </div>
      @endforeach

      @if($variants->count() === 0)
        <div style="padding:12px;border:1px dashed #ddd;border-radius:12px;">No variants yet.</div>
      @endif
    </div>
  </div>
@endsection
