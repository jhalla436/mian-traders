@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Product</h2>
    <a href="{{ route('mt.products.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('mt.products.store') }}" class="form-stack" id="product-form">
      @csrf
      <div id="form-content">
        @include('mt.products.form', ['product' => $product])
      </div>
      <div style="height:100px"></div>
    </form>
    <div style="position:fixed;bottom:20px;left:0;right:0;display:flex;justify-content:center;z-index:9999;pointer-events:none;">
      <button type="submit" form="product-form" class="btn btn-success btn-lg" style="padding:14px 32px;border-radius:8px;cursor:pointer;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);pointer-events:all;">✓ Save Product</button>
    </div>
  </div>
@endsection
