@extends('mt.layouts.app')

@section('content')
<div class="card row" style="justify-content:space-between;">
  <h2 style="margin:0;">{{ $heading->name }} (Select by picture/code)</h2>
  <a class="btn btn-gray" href="{{ route('mt.pos.index') }}">Back to POS</a>
</div>

<div class="card grid">
  @foreach($variants as $v)
    <div class="tile">
      @if($v->image_path)
        <img src="{{ asset('storage/'.$v->image_path) }}" />
      @else
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect width='400' height='300' fill='%23e5e7eb'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%236b7280' font-size='22'%3ENo Image%3C/text%3E%3C/svg%3E" />
      @endif
      <div style="font-size:22px; font-weight:700; margin-top:8px;">#{{ $v->variant_code }}</div>
      <div>Sell: {{ $v->selling_price_default ?? '-' }}</div>

      <form method="POST" action="{{ route('mt.pos.add', $v) }}">
        @csrf
        <button class="btn btn-primary" style="width:100%; margin-top:10px;">Add</button>
      </form>
    </div>
  @endforeach

  @if($variants->count() === 0)
    <div class="tile">No variants yet. Add from Headings → Variants.</div>
  @endif
</div>
@endsection
