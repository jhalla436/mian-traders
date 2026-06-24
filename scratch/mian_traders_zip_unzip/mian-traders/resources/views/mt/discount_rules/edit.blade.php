@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Edit Discount Rule</h2>
    <a href="{{ route('mt.discount_rules.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">

    <form method="POST"
          action="{{ route('mt.discount_rules.update', ['discount_rule' => $discount_rule->id]) }}"
          style="display:grid;gap:12px;max-width:900px;">
      @csrf
      @method('PUT')

      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <label><b>Scope:</b></label>
        <label><input type="radio" name="scope_type" value="company" @checked(old('scope_type', $discount_rule->scope_type)==='company')> Company</label>
        <label><input type="radio" name="scope_type" value="category" @checked(old('scope_type', $discount_rule->scope_type)==='category')> Category</label>
        <label><input type="radio" name="scope_type" value="product" @checked(old('scope_type', $discount_rule->scope_type)==='product')> Product</label>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;min-width:260px;">
          <label><b>Company</b></label>
          <select name="company_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
            <option value="">-- select --</option>
            @foreach($companies as $c)
              <option value="{{ $c->id }}"
                @selected((int)old('company_id', ($discount_rule->scope_type==='company' ? $discount_rule->scope_id : null))===(int)$c->id)>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div style="flex:1;min-width:260px;">
          <label><b>Category</b></label>
          <select name="category_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
            <option value="">-- select --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}"
                @selected((int)old('category_id', ($discount_rule->scope_type==='category' ? $discount_rule->scope_id : null))===(int)$cat->id)>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div style="flex:1;min-width:260px;">
          <label><b>Product</b></label>
          <select name="product_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
            <option value="">-- select --</option>
            @foreach($products as $p)
              <option value="{{ $p->id }}"
                @selected((int)old('product_id', ($discount_rule->scope_type==='product' ? $discount_rule->scope_id : null))===(int)$p->id)>
                {{ $p->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;min-width:260px;">
          <label><b>Discount Type</b></label>
          <select name="discount_type_id" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
            @foreach($types as $t)
              <option value="{{ $t->id }}" @selected((int)old('discount_type_id', $discount_rule->discount_type_id)===(int)$t->id)>
                {{ $t->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div style="flex:1;min-width:260px;">
          <label><b>Rule Type</b></label>
          <select name="rule_type" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
            @foreach($ruleTypes as $k => $label)
              <option value="{{ $k }}" @selected(old('rule_type', $discount_rule->rule_type)===$k)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="flex:1;min-width:180px;">
          <label><b>Percent 1</b></label>
          <input name="percent_1" type="number" step="0.01" value="{{ old('percent_1', $discount_rule->percent_1) }}"
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>

        <div style="flex:1;min-width:180px;">
          <label><b>Percent 2</b></label>
          <input name="percent_2" type="number" step="0.01" value="{{ old('percent_2', $discount_rule->percent_2) }}"
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>

        <div style="flex:1;min-width:220px;">
          <label><b>Fixed Purchase Price</b></label>
          <input name="fixed_purchase_price" type="number" step="0.01" value="{{ old('fixed_purchase_price', $discount_rule->fixed_purchase_price) }}"
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
      </div>

      <div>
        <label><b>Note</b></label>
        <input name="note" value="{{ old('note', $discount_rule->note) }}"
               style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>

      <button type="submit" style="padding:12px 14px;border-radius:10px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
        Update
      </button>

      @if($errors->any())
        <div style="background:#fee2e2;padding:10px;border-radius:10px;">
          @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif

      @if(session('error'))
        <div style="background:#fee2e2;padding:10px;border-radius:10px;">
          {{ session('error') }}
        </div>
      @endif
    </form>

    <form method="POST"
          action="{{ route('mt.discount_rules.destroy', ['discount_rule' => $discount_rule->id]) }}"
          style="margin-top:10px;">
      @csrf
      @method('DELETE')
      <button type="submit" style="padding:12px 14px;border-radius:10px;border:0;background:#dc2626;color:#fff;cursor:pointer;">
        Delete
      </button>
    </form>

  </div>
@endsection
