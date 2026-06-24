@php
  $group = $group ?? new \App\Models\CompanyGroup();
@endphp

<div style="display:grid;gap:10px;">
  <input name="name" value="{{ old('name', $group->name) }}" placeholder="Group Name (e.g. Almari)"
         style="padding:10px;border:1px solid #ddd;border-radius:10px;">

  <input name="key" value="{{ old('key', $group->key) }}" placeholder="Key (e.g. almari) only a-z 0-9 _"
         style="padding:10px;border:1px solid #ddd;border-radius:10px;">

  <input name="sort_order" type="number" value="{{ old('sort_order', $group->sort_order ?? 0) }}" placeholder="Sort order"
         style="padding:10px;border:1px solid #ddd;border-radius:10px;">

  <label style="display:flex;gap:8px;align-items:center;">
    <input type="checkbox" name="is_active" @checked(old('is_active', (int)($group->is_active ?? 1)) == 1)>
    Active
  </label>

  @if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;">
      <div style="font-weight:800;margin-bottom:6px;">Fix errors:</div>
      <ul style="margin:0;padding-left:18px;">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</div>
