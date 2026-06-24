@extends('mt.layouts.app')

@section('content')
<div class="card row" style="justify-content:space-between;">
  <h2 style="margin:0;">Mian Traders – POS</h2>
  <form method="POST" action="{{ route('mt.pos.clear') }}">
    @csrf
    <button class="btn btn-gray" type="submit">New Invoice</button>
  </form>
</div>

<div class="card">
  <form class="row" method="POST" action="{{ route('mt.pos.search') }}">
    @csrf
    <input name="code" placeholder="Search by sheet code (e.g. 101)" required />
    <button class="btn btn-primary" type="submit">Add</button>
  </form>
</div>

<div class="card">
  <h3>Quick Headings</h3>
  <div class="row">
    @foreach($headings as $h)
      <a class="btn btn-primary" href="{{ route('mt.pos.heading', $h) }}">{{ $h->name }}</a>
    @endforeach
    @if($headings->count() === 0)
      <div>No headings yet. Add from Headings menu.</div>
    @endif
  </div>
</div>

<div class="card">
  <h3>Cart</h3>
  <table>
    <thead>
      <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Rate</th>
        <th>Total</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($cart as $key => $item)
      <tr>
        <td>{{ $item['title'] }}</td>
        <td style="width:160px;">
          <form class="row" method="POST" action="{{ route('mt.pos.update', $key) }}">
            @csrf
            <input name="qty" type="number" step="0.001" value="{{ $item['qty'] }}" style="width:90px;" />
        </td>
        <td style="width:240px;">
            <input name="price" type="number" step="0.01" value="{{ $item['price'] }}" style="width:110px;" />
            <button class="btn btn-primary" type="submit">Update</button>
          </form>
        </td>
        <td>{{ number_format($item['qty'] * $item['price'], 2) }}</td>
        <td style="width:120px;">
          <form method="POST" action="{{ route('mt.pos.remove', $key) }}">
            @csrf
            <button class="btn btn-danger" type="submit">Remove</button>
          </form>
        </td>
      </tr>
      @endforeach

      @if(count($cart) === 0)
        <tr><td colspan="5">Cart empty.</td></tr>
      @endif
    </tbody>
  </table>

  <h3 style="text-align:right; margin-top:12px;">Total: {{ number_format($total, 2) }}</h3>
</div>
@endsection
