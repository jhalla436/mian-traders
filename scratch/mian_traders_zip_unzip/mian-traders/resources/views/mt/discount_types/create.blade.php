@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Add Discount Type</h2>
    <a href="{{ route('mt.discount_types.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="{{ route('mt.discount_types.store') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
      @csrf
      <input name="name" placeholder="e.g. covered, uncovered, spring, jumbolon, hardware" required style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:320px;" />
      <label style="display:flex;gap:8px;align-items:center;">
        <input type="checkbox" name="is_active" value="1" checked />
        Active
      </label>
      <button type="submit" style="padding:8px 14px;border-radius:8px;border:0;background:#2563eb;color:#fff;cursor:pointer;">Save</button>
    </form>

    @if($errors->any())
      <div style="margin-top:12px;background:#fee2e2;padding:10px;border-radius:10px;">
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif
  </div>
@endsection
