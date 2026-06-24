<div style="display:grid;gap:10px;">
  <div>
    <label style="display:block;font-size:12px;color:#6b7280;margin-bottom:6px;">Category Name</label>
    <input name="name" value="{{ old('name', $category->name ?? '') }}"
           style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
    @error('name') <div style="color:#b91c1c;font-size:12px;margin-top:6px;">{{ $message }}</div> @enderror
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
    <div>
      <label style="display:block;font-size:12px;color:#6b7280;margin-bottom:6px;">Group</label>
      <select name="group_key" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        <option value="">-- Select --</option>
        <option value="foam" @selected(old('group_key', $category->group_key ?? '')==='foam')>Foam</option>
        <option value="spring" @selected(old('group_key', $category->group_key ?? '')==='spring')>Spring</option>
        <option value="fabric" @selected(old('group_key', $category->group_key ?? '')==='fabric')>Fabric</option>
        <option value="hardware" @selected(old('group_key', $category->group_key ?? '')==='hardware')>Hardware</option>
        <option value="accessories" @selected(old('group_key', $category->group_key ?? '')==='accessories')>Accessories</option>
        <option value="other" @selected(old('group_key', $category->group_key ?? '')==='other')>Other</option>
      </select>
      @error('group_key') <div style="color:#b91c1c;font-size:12px;margin-top:6px;">{{ $message }}</div> @enderror
    </div>

    <div>
      <label style="display:block;font-size:12px;color:#6b7280;margin-bottom:6px;">Unit Type (how you sell)</label>
      <select name="unit_type" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        <option value="unit" @selected(old('unit_type', $category->unit_type ?? 'unit')==='unit')>Unit / Piece</option>
        <option value="meter" @selected(old('unit_type', $category->unit_type ?? '')==='meter')>Meter</option>
        <option value="kg" @selected(old('unit_type', $category->unit_type ?? '')==='kg')>Kilogram (KG)</option>
        <option value="sqft" @selected(old('unit_type', $category->unit_type ?? '')==='sqft')>Square Feet</option>
      </select>
      @error('unit_type') <div style="color:#b91c1c;font-size:12px;margin-top:6px;">{{ $message }}</div> @enderror
    </div>
  </div>

  <div style="font-size:12px;color:#6b7280;">
    Tip: Use <b>Meter</b> for fabric, <b>KG</b> for items sold by weight. Unit type controls POS quantity step.
  </div>
</div>
