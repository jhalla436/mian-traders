@extends('mt.layouts.app')

@section('content')
<div style="max-width:900px;margin:0 auto;padding:20px;">
  <h1 style="font-size:24px;font-weight:700;margin-bottom:20px;">Add Prices - {{ $name }}</h1>
  
  <p style="color:#666;margin-bottom:20px;font-size:14px;">
    Set MRP, purchase price, and selling price for each size/quality. Leave blank to skip.
  </p>

  <form method="POST" action="{{ route('mt.products.bulk_price_save') }}" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:20px;">
    @csrf

    <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
      <thead>
        <tr style="background:#f5f5f5;border-bottom:2px solid #ddd;">
          <th style="text-align:left;padding:12px;font-weight:600;border-right:1px solid #ddd;">Product Size</th>
          <th style="text-align:center;padding:12px;font-weight:600;border-right:1px solid #ddd;">MRP</th>
          <th style="text-align:center;padding:12px;font-weight:600;border-right:1px solid #ddd;">Purchase Price</th>
          <th style="text-align:center;padding:12px;font-weight:600;">Selling Price</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $idx => $product)
          <tr style="border-bottom:1px solid #eee;">
            <td style="padding:12px;border-right:1px solid #ddd;">
              {{ $product->name }}
              <div style="font-size:12px;color:#999;margin-top:4px;">
                {{ $product->length_in ?? '-' }} × {{ $product->width_in ?? '-' }} × {{ $product->height_in ?? '-' }}
              </div>
            </td>
            <td style="padding:12px;border-right:1px solid #ddd;">
              <input 
                type="number"
                name="prices[{{ $idx }}][id]"
                value="{{ $product->id }}"
                style="display:none;">
              <input 
                type="number"
                name="prices[{{ $idx }}][mrp]"
                value="{{ $product->mrp ?? '' }}"
                step="0.01"
                min="0"
                placeholder="0.00"
                style="width:100%;padding:8px 6px;border:1px solid #ccc;border-radius:4px;text-align:center;font-size:13px;">
            </td>
            <td style="padding:12px;border-right:1px solid #ddd;">
              <input 
                type="number"
                name="prices[{{ $idx }}][purchase_price_manual]"
                value="{{ $product->purchase_price_manual ?? '' }}"
                step="0.01"
                min="0"
                placeholder="0.00"
                style="width:100%;padding:8px 6px;border:1px solid #ccc;border-radius:4px;text-align:center;font-size:13px;">
            </td>
            <td style="padding:12px;">
              <input 
                type="number"
                name="prices[{{ $idx }}][selling_price_default]"
                value="{{ $product->selling_price_default ?? '' }}"
                step="0.01"
                min="0"
                placeholder="0.00"
                style="width:100%;padding:8px 6px;border:1px solid #ccc;border-radius:4px;text-align:center;font-size:13px;">
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" style="text-align:center;padding:20px;color:#999;">No products found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div style="display:flex;gap:10px;justify-content:center;">
      <button type="submit" style="padding:10px 24px;background:#080;color:#fff;border:1px solid #060;border-radius:4px;font-weight:600;cursor:pointer;font-size:14px;">
        Save Prices
      </button>
      <a href="{{ route('mt.products.index') }}" style="padding:10px 24px;background:#999;color:#fff;border:1px solid #777;border-radius:4px;font-weight:600;cursor:pointer;font-size:14px;text-decoration:none;display:inline-block;">
        Skip
      </a>
    </div>
  </form>
</div>

<style>
  input[type="number"]:focus {
    outline: none;
    border-color: #333;
    box-shadow: 0 0 0 2px rgba(51, 51, 51, 0.1);
  }
</style>
@endsection
