<div class="form-stack">
  <div class="form-group">
    <label class="form-label">Category Name</label>
    <input name="name" value="{{ old('name', $category->name ?? '') }}" class="mt-input" placeholder="e.g., Dura Air, Spring Collection">
    @error('name') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Company</label>
      <select name="company_id" class="mt-select">
        <option value="">-- None (Global) --</option>
        @foreach($companies as $comp)
          <option value="{{ $comp->id }}" @selected(old('company_id', $category->company_id ?? $selectedCompanyId) == $comp->id)>
            {{ $comp->name }}
          </option>
        @endforeach
      </select>
      @error('company_id') <div class="form-error">{{ $message }}</div> @enderror
      <div class="text-xs text-muted">Assign to a specific company (e.g., Dura)</div>
    </div>

    <div class="form-group">
      <label class="form-label">Parent Category</label>
      <select name="parent_id" class="mt-select">
        <option value="">-- Root Category --</option>
        @if(isset($availableParents))
          @foreach($availableParents as $parent)
            <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id ?? ($parentId ?? null)) == $parent->id)>
              {{ $parent->name }}
            </option>
          @endforeach
        @endif
      </select>
      @error('parent_id') <div class="form-error">{{ $message }}</div> @enderror
      <div class="text-xs text-muted">Make this a sub-category under another category</div>
    </div>
  </div>

  @if(($parentCategory ?? null) || ($category->parent_id ?? null))
    @php
      $displayParent = $parentCategory ?? ($category->parent_id ? \App\Models\Category::find($category->parent_id) : null);
    @endphp
    @if($displayParent)
      <div style="padding: 12px; background: #e3f2fd; border-radius: 4px; margin-bottom: 16px;">
        <strong>Parent:</strong> {{ $displayParent->name }} 
        @if($displayParent->company)
          from <strong>{{ $displayParent->company->name }}</strong>
        @endif
      </div>
    @endif
  @endif

  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Group</label>
      <select name="group_key" class="mt-select">
        <option value="">-- Select --</option>
        <option value="foam" @selected(old('group_key', $category->group_key ?? '')==='foam')>Foam</option>
        <option value="spring" @selected(old('group_key', $category->group_key ?? '')==='spring')>Spring</option>
        <option value="fabric" @selected(old('group_key', $category->group_key ?? '')==='fabric')>Fabric</option>
        <option value="hardware" @selected(old('group_key', $category->group_key ?? '')==='hardware')>Hardware</option>
        <option value="accessories" @selected(old('group_key', $category->group_key ?? '')==='accessories')>Accessories</option>
        <option value="other" @selected(old('group_key', $category->group_key ?? '')==='other')>Other</option>
      </select>
      @error('group_key') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
      <label class="form-label">Unit Type (how you sell)</label>
      <select name="unit_type" class="mt-select">
        <option value="unit" @selected(old('unit_type', $category->unit_type ?? 'unit')==='unit')>Unit / Piece</option>
        <option value="meter" @selected(old('unit_type', $category->unit_type ?? '')==='meter')>Meter</option>
        <option value="kg" @selected(old('unit_type', $category->unit_type ?? '')==='kg')>Kilogram (KG)</option>
        <option value="sqft" @selected(old('unit_type', $category->unit_type ?? '')==='sqft')>Square Feet</option>
      </select>
      @error('unit_type') <div class="form-error">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="text-xs text-muted">
    💡 <b>Example:</b> Create company "Dura" → then create sub-categories like "Dura Air", "Dura Memory 2in1", "Dura Spine" for easy product organization.
  </div>
</div>
