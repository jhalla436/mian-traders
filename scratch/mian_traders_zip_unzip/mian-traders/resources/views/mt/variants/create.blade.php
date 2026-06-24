@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Add Variant – {{ $heading->name }}</h2>
    <a href="{{ route('mt.variants.index', $heading) }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="{{ route('mt.variants.store', $heading) }}" enctype="multipart/form-data"
          style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
      @csrf

      <input name="variant_code" placeholder="Code number (e.g. 101)" required
             style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:220px;" />

      <input name="selling_price_default" type="number" step="0.01" placeholder="Default sell price"
             style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:220px;" />

      <input name="image" type="file" accept="image/*"
             style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;" />

      <button type="submit"
              style="padding:8px 14px;border-radius:8px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
        Save
      </button>
    </form>

    @if($errors->any())
      <div style="margin-top:12px;background:#fee2e2;padding:10px;border-radius:10px;">
        @foreach($errors->all() as $e)
          <div>{{ $e }}</div>
        @endforeach
      </div>
    @endif
  </div>
@endsection
