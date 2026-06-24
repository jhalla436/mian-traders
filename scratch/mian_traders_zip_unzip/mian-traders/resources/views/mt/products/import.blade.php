@extends('mt.layouts.app')

@section('content')
@php
  $companies = $companies ?? collect();
  $categories = $categories ?? collect();
  $discountTypes = $discountTypes ?? collect();
@endphp

<div style="background:#fff;padding:14px;border-radius:10px;max-width:860px;">
  <h2 style="margin:0 0 10px;">Import Price List (CSV)</h2>

  <div style="color:#6b7280;font-size:13px;line-height:1.5;margin-bottom:12px;">
    Upload a <b>CSV</b> file (you can export your Excel sheet as CSV). The importer can create new products and (optionally) update existing ones.
    <div style="margin-top:6px;">
      <a href="{{ route('mt.products.import_template') }}" style="text-decoration:none;color:#2563eb;font-weight:700;">
        Download CSV Template
      </a>
    </div>
  </div>

  <form method="POST" action="{{ route('mt.products.import_process') }}" enctype="multipart/form-data" style="display:grid;gap:12px;">
    @csrf

    <div>
      <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">CSV File</div>
      <input type="file" name="file" accept=".csv,text/csv" required>
      @error('file')
        <div style="color:#b91c1c;font-size:12px;">{{ $message }}</div>
      @enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Company (optional)</div>
        <select name="default_company_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="">-- None --</option>
          @foreach($companies as $co)
            <option value="{{ $co->id }}">{{ $co->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Category (optional)</div>
        <select name="default_category_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="">-- None --</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Discount Type (optional)</div>
        <select name="default_discount_type_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="">-- None --</option>
          @foreach($discountTypes as $dt)
            <option value="{{ $dt->id }}">{{ $dt->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Mode</div>
        <select name="mode" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="upsert">Upsert (Create + Update)</option>
          <option value="create_only">Create Only (Skip existing)</option>
        </select>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Default Active</div>
        <select name="default_is_active" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <div></div>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <button type="submit" style="padding:12px 14px;border-radius:10px;border:0;background:#16a34a;color:#fff;cursor:pointer;">
        Import
      </button>
      <a href="{{ route('mt.products.index') }}" style="padding:12px 14px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">
        Back
      </a>
    </div>

    <div style="margin-top:6px;color:#6b7280;font-size:12px;">
      CSV columns supported: <b>name</b>, sku, mrp, selling_price_default, purchase_price_manual, stock_qty, low_stock_alert_qty, pricing_mode (manual/shell), shell_rate, width_in, length_in, height_in, sheet_full_w, sheet_full_l, category, company.
    </div>
  </form>
</div>
@endsection
