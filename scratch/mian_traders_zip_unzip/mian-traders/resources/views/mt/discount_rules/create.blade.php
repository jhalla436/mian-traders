@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Add Discount Rule</h2>
    <a href="{{ route('mt.discount_rules.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="{{ route('mt.discount_rules.store') }}" style="display:grid;gap:12px;max-width:900px;">
      @csrf

      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <label><b>Scope:</b></label>
        <label><input type="radio" name="scope_type" value="company" checked> Company</label>
        <label><input type="radio" name="scope_type" value="category"> Category</label>
        <label><input type="radio" name="scope_type" value="product"> Product</label>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <select name="company_id" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <option value="">Select Company (if scope=company)</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>

        <select name="category_id" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <option value="">Select Category (if scope=category)</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>

        <select name="product_id" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <option value="">Select Product (if scope=product)</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <select name="discount_type_id" required style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          <option value="">Select Discount Type</option>
          @foreach($types as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
          @endforeach
        </select>

        <select name="rule_type" required style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
          @foreach($ruleTypes as $k => $label)
            <option value="{{ $k }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <input name="percent_1" type="number" step="0.001" placeholder="%1 (e.g. 12.5 / 29.7)" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
        <input name="percent_2" type="number" step="0.001" placeholder="%2 (only for two-step e.g. 5)" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
        <input name="fixed_purchase_price" type="number" step="0.01" placeholder="Fixed purchase price (if fixed)" style="padding:8px;border:1px solid #ddd;border-radius:8px;min-width:260px;">
      </div>

      <input name="note" placeholder="Note (optional)" style="padding:8px;border:1px solid #ddd;border-radius:8px;">

      <button type="submit" style="padding:10px 14px;border-radius:8px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
        Save Rule
      </button>

      @if(session('error'))
        <div style="background:#fee2e2;padding:10px;border-radius:10px;">{{ session('error') }}</div>
      @endif

      @if($errors->any())
        <div style="background:#fee2e2;padding:10px;border-radius:10px;">
          @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif
    </form>
  </div>
@endsection
