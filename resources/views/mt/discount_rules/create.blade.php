@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Discount Rule</h2>
    <a href="{{ route('mt.discount_rules.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:900px;">
    <form method="POST" action="{{ route('mt.discount_rules.store') }}" class="form-stack">
      @csrf
      <div class="flex gap-12 flex-wrap items-center">
        <label class="form-label">Scope:</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="company" checked> Company</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="category"> Category</label>
        <label class="form-checkbox"><input type="radio" name="scope_type" value="product"> Product</label>
      </div>
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="form-group">
          <label class="form-label">Company</label>
          <select name="company_id" class="mt-select">
            <option value="">-- select (if scope=company) --</option>
            @foreach($companies as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category_id" class="mt-select">
            <option value="">-- select (if scope=category) --</option>
            @foreach($categories as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Product</label>
          <select name="product_id" class="mt-select">
            <option value="">-- select (if scope=product) --</option>
            @foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
          </select>
        </div>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Discount Type</label>
          <select name="discount_type_id" required class="mt-select">
            <option value="">Select Discount Type</option>
            @foreach($types as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Rule Type</label>
          <select name="rule_type" required class="mt-select">
            @foreach($ruleTypes as $k => $label) <option value="{{ $k }}">{{ $label }}</option> @endforeach
          </select>
        </div>
      </div>
      <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="form-group">
          <label class="form-label">Percent 1</label>
          <input name="percent_1" type="number" step="0.001" placeholder="e.g. 12.5" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Percent 2</label>
          <input name="percent_2" type="number" step="0.001" placeholder="e.g. 5 (two-step)" class="mt-input">
        </div>
        <div class="form-group">
          <label class="form-label">Fixed Purchase Price</label>
          <input name="fixed_purchase_price" type="number" step="0.01" placeholder="If fixed" class="mt-input">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Note</label>
        <input name="note" placeholder="Note (optional)" class="mt-input">
      </div>
      <button type="submit" class="btn btn-primary" style="justify-self:start;">Save Rule</button>
      @if(session('error'))
        <div class="mt-alert mt-alert-error">{{ session('error') }}</div>
      @endif
      @if($errors->any())
        <div class="errors-box">
          @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif
    </form>
  </div>
@endsection
