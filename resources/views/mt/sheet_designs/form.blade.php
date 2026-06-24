<div class="form-grid">
  <div class="form-group">
    <label class="form-label">Design Name</label>
    <input name="name" value="{{ old('name', $sheetDesign->name ?? '') }}" required class="mt-input" placeholder="e.g., Oak Wood Grain, Cherry Wood">
    @error('name')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label">Finish</label>
    <input name="finish" value="{{ old('finish', $sheetDesign->finish ?? '') }}" class="mt-input" placeholder="e.g., Glossy, Matt, Textured">
    @error('finish')<div class="form-error">{{ $message }}</div>@enderror
  </div>
</div>

<div class="form-grid" style="margin-top: 16px;">
  <div class="form-group">
    <label class="form-label">Color Group</label>
    <input name="color_group" value="{{ old('color_group', $sheetDesign->color_group ?? '') }}" class="mt-input" placeholder="e.g., Brown, White, Gray, Yellow">
    @error('color_group')<div class="form-error">{{ $message }}</div>@enderror
  </div>
</div>

<div style="margin-top: 24px; border-top: 1px solid #eee; padding-top: 20px;">
  <h3 style="margin-bottom: 12px; font-size: 1.1rem; font-weight: 600; color: #333;">Company-Specific Sheet Numbers / Design Codes</h3>
  <p style="font-size: 0.85rem; color: #666; margin-bottom: 16px;">
    Enter the code/number used by each company for this design (e.g. 135 for KMI, 4321 for Al-Noor). When importing products or sheets, this will automatically resolve to this design.
  </p>

  <div class="form-grid">
    @foreach($companies as $company)
      @php
        $mappedCode = $sheetDesign->getCodeForCompany($company->id);
      @endphp
      <div class="form-group">
        <label class="form-label">{{ $company->name }}</label>
        <input name="sheet_codes[{{ $company->id }}]" value="{{ old('sheet_codes.' . $company->id, $mappedCode) }}" class="mt-input" placeholder="e.g., 135 or 4321">
        @error('sheet_codes.' . $company->id)<div class="form-error">{{ $message }}</div>@enderror
      </div>
    @endforeach
  </div>
</div>

@if($errors->any())
  <div class="errors-box" style="margin-top: 20px;">
    <b>Fix errors:</b>
    <ul>
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

