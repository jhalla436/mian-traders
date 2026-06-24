@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Edit Discount Rule</h2>
    <a href="{{ route('mt.discount_rules.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:900px;">
    <form method="POST" action="{{ route('mt.discount_rules.update', ['discount_rule' => $discount_rule->id]) }}" class="form-stack">
      @csrf @method('PUT')
      <div class="flex gap-12 flex-wrap items-center">
        <label class="form-label">Scope:</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="company" @checked(old('scope_type', $discount_rule->scope_type)==='company')> Company</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="category" @checked(old('scope_type', $discount_rule->scope_type)==='category')> Category</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="product" @checked(old('scope_type', $discount_rule->scope_type)==='product')> Product</label>
      </div>
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="form-group">
          <label class="form-label">Company</label>
          <select name="company_id" class="mt-select">
            <option value="">-- select --</option>
            @foreach($companies as $c)
              <option value="{{ $c->id }}" @selected((int)old('company_id', ($discount_rule->scope_type==='company' ? $discount_rule->scope_id : null))===(int)$c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category_id" class="mt-select">
            <option value="">-- select --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected((int)old('category_id', ($discount_rule->scope_type==='category' ? $discount_rule->scope_id : null))===(int)$cat->id)>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Product</label>
          <select name="product_id" class="mt-select">
            <option value="">-- select --</option>
            @foreach($products as $p)
              <option value="{{ $p->id }}" @selected((int)old('product_id', ($discount_rule->scope_type==='product' ? $discount_rule->scope_id : null))===(int)$p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Discount Type</label>
          <select name="discount_type_id" required class="mt-select">
            @foreach($types as $t)
              <option value="{{ $t->id }}" @selected((int)old('discount_type_id', $discount_rule->discount_type_id)===(int)$t->id)>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Rule Type</label>
          <select name="rule_type" required class="mt-select">
            @foreach($ruleTypes as $k => $label)
              <option value="{{ $k }}" @selected(old('rule_type', $discount_rule->rule_type)===$k)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="form-group">
          <label class="form-label">Percent 1</label>
          <input name="percent_1" type="number" step="0.01" value="{{ old('percent_1', $discount_rule->percent_1) }}" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Percent 2</label>
          <input name="percent_2" type="number" step="0.01" value="{{ old('percent_2', $discount_rule->percent_2) }}" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Fixed Purchase Price</label>
          <input name="fixed_purchase_price" type="number" step="0.01" value="{{ old('fixed_purchase_price', $discount_rule->fixed_purchase_price) }}" class="mt-input">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Note</label>
        <input name="note" value="{{ old('note', $discount_rule->note) }}" class="mt-input">
      </div>
      <div class="flex gap-8">
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
      @if($errors->any())
        <div class="errors-box">@foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach</div>
      @endif
      @if(session('error'))
        <div class="mt-alert mt-alert-error">{{ session('error') }}</div>
      @endif
    </form>

    <form method="POST" action="{{ route('mt.discount_rules.destroy', ['discount_rule' => $discount_rule->id]) }}" style="margin-top:16px;">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger">Delete Rule</button>
    </form>
  </div>
@endsection
