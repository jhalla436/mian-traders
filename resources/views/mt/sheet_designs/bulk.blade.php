@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Bulk Create Products from Sheet Designs</h2>
    <a href="{{ route('mt.sheet_designs.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('mt.sheet_designs.bulk_process') }}" enctype="multipart/form-data" class="form-stack">
      @csrf

      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category_id" required class="mt-select">
            <option value="">-- Select Category --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
          <div class="form-help text-muted">The category to assign all newly generated sheet products to.</div>
          @error('category_id')<div class="form-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="form-group" style="margin-top: 16px;">
        <label class="form-label">Sizes to Generate</label>
        <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 8px;">
          <label class="form-checkbox">
            <input type="checkbox" name="sizes[]" value="8x4" checked>
            <span>8x4 (Full Sheet)</span>
          </label>
          <label class="form-checkbox">
            <input type="checkbox" name="sizes[]" value="6x4">
            <span>6x4</span>
          </label>
          <label class="form-checkbox">
            <input type="checkbox" name="sizes[]" value="4x4">
            <span>4x4</span>
          </label>
          <label class="form-checkbox">
            <input type="checkbox" name="sizes[]" value="2x4">
            <span>2x4</span>
          </label>
        </div>
        @error('sizes')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-grid" style="margin-top: 16px;">
        <div class="form-group">
          <label class="form-label">Custom Sizes (Optional)</label>
          <input name="custom_sizes" value="{{ old('custom_sizes') }}" class="mt-input" placeholder="e.g. 7x4, 5x4, 3x4">
          <div class="form-help text-muted">Comma-separated dimensions in length x width format. We automatically convert small numbers (&le; 12) to inches (e.g., 7x4 feet becomes 84x48 inches).</div>
          @error('custom_sizes')<div class="form-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div style="margin-top: 24px; border-top: 1px solid #eee; padding-top: 20px;">
        <h3 style="margin-bottom: 12px; font-size: 1.1rem; font-weight: 600; color: #333;">Design Data Input</h3>
        <p style="font-size: 0.85rem; color: #666; margin-bottom: 16px;">
          Choose either to upload a CSV file or paste tabular data (comma or tab separated) from Excel/Google Sheets directly into the textarea.
        </p>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Option A: Upload CSV File</label>
            <input type="file" name="csv_file" accept=".csv,.txt" style="font-size: 0.85rem; width: 100%; border: 1px solid #cbd5e1; padding: 6px; border-radius: 4px; background: #fafafa;">
            @error('csv_file')<div class="form-error">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-group" style="margin-top: 16px;">
          <label class="form-label">Option B: Paste SpreadSheet Data</label>
          <textarea name="pasted_data" rows="10" class="mt-input" placeholder="design_name,finish,color_group,AL-Noor lamination,KMI lamination,Shaheen Board
Pure White,Glossy,White,4321,135,3950
Oak Wood,Texture,Brown,4322,136," style="font-family: monospace; font-size: 0.85rem;"></textarea>
          <div class="form-help text-muted">Pasted columns can be separated by commas or tabs. If uploading a file, you can leave this textarea blank.</div>
          @error('pasted_data')<div class="form-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div style="margin-top: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px;">
        <h4 style="margin: 0 0 8px 0; font-size: 0.9rem; font-weight: 700; color: #1e293b;">CSV / Paste Layout Guide</h4>
        <p style="font-size: 0.8rem; color: #475569; margin-bottom: 8px; line-height: 1.4;">
          The first line must be a header row. Headers must contain:
        </p>
        <ul style="font-size: 0.8rem; color: #475569; padding-left: 20px; margin-bottom: 12px; line-height: 1.4;">
          <li><code>design_name</code> or <code>name</code> (The design's identifier)</li>
          <li><code>finish</code> (optional, e.g. Glossy, Matt)</li>
          <li><code>color_group</code> (optional, e.g. White, Brown, Gray)</li>
          <li>Columns matching lamination companies (e.g. <code>KMI lamination</code>, <code>AL-Noor lamination</code>, <code>ZRK Lamination</code>, <code>Shaheen Board</code>). Note: the importer matches columns automatically even if you skip the word "lamination" or "board" in the column header (e.g. <code>KMI</code> matches <code>KMI lamination</code>).</li>
        </ul>
        <div style="background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 8px 12px; font-family: monospace; font-size: 0.75rem; color: #334155;">
          name,finish,color_group,KMI,AL-Noor lamination,Shaheen Board<br>
          Super White,Glossy,White,135,4321,3950<br>
          Cherry Wood,Matt,Brown,136,,3951
        </div>
      </div>

      <div style="margin-top: 24px; border-top: 1px solid #eee; padding-top: 20px;">
        <button type="submit" class="btn btn-success">
          Process & Create Products
        </button>
      </div>
    </form>
  </div>
@endsection
