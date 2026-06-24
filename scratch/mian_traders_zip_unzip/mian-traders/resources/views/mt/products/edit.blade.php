@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Edit Product</h2>
    <a href="{{ route('mt.products.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="{{ route('mt.products.update', $product) }}" style="display:grid;gap:12px;">
      @csrf
      @method('PUT')
      @include('mt.products.form', ['product' => $product])
      <button style="padding:12px;border-radius:10px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
        Update Product
      </button>
    </form>
  </div>
@endsection
