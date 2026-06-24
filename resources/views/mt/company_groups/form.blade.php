@php
  $group = $group ?? new \App\Models\CompanyGroup();
@endphp

<div class="form-stack" style="max-width:520px;">
  <div class="form-group">
    <label class="form-label">Group Name</label>
    <input name="name" value="{{ old('name', $group->name) }}" placeholder="e.g. Almari" class="mt-input">
  </div>
  <div class="form-group">
    <label class="form-label">Key</label>
    <input name="key" value="{{ old('key', $group->key) }}" placeholder="e.g. almari (only a-z 0-9 _)" class="mt-input">
  </div>
  <div class="form-group">
    <label class="form-label">Sort Order</label>
    <input name="sort_order" type="number" value="{{ old('sort_order', $group->sort_order ?? 0) }}" class="mt-input">
  </div>
  <label class="form-checkbox">
    <input type="checkbox" name="is_active" @checked(old('is_active', (int)($group->is_active ?? 1)) == 1)>
    Active
  </label>
  @if($errors->any())
    <div class="errors-box">
      <b>Fix errors:</b>
      <ul>
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif
</div>
