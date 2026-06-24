@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Sheet Designs</h2>
    <div class="page-actions">
      <form method="GET" action="{{ route('mt.sheet_designs.index') }}" class="flex gap-8 items-center">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search design..." class="mt-input" style="width:220px;">
        <button class="btn btn-secondary btn-sm">Search</button>
      </form>
      <a href="{{ route('mt.sheet_designs.bulk_form') }}" class="btn btn-secondary btn-sm" style="margin-right: 4px;">Bulk Creator</a>
      <a href="{{ route('mt.sheet_designs.create') }}" class="btn btn-primary btn-sm">+ Add Design</a>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Finish</th>
          <th>Color Group</th>
          <th>Company Codes</th>
          <th class="text-center">Linked Products</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sheetDesigns as $sd)
          <tr>
            <td>
              <div class="font-bold">{{ $sd->name }}</div>
            </td>
            <td>{{ $sd->finish ?? '-' }}</td>
            <td>{{ $sd->color_group ?? '-' }}</td>
            <td>
              @if(is_array($sd->sheet_codes) && count($sd->sheet_codes) > 0)
                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                  @foreach($sd->sheet_codes as $compId => $code)
                    @php
                      $compName = \App\Models\Company::find($compId)->name ?? 'Company #' . $compId;
                      $compNameClean = str_ireplace([' lamination', ' board'], '', $compName);
                    @endphp
                    <span class="badge badge-gray" style="background-color: #f1f1f1; color: #333; border: 1px solid #ccc; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; display: inline-block;" title="{{ $compName }}">
                      <strong>{{ $compNameClean }}:</strong> {{ $code }}
                    </span>
                  @endforeach
                </div>
              @else
                <span class="text-muted" style="font-size: 0.85rem; color: #999;">-</span>
              @endif
            </td>
            <td class="text-center">
              <span class="badge {{ $sd->products_count > 0 ? 'badge-info' : 'badge-gray' }}">
                {{ $sd->products_count }}
              </span>
            </td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.sheet_designs.edit', $sd) }}" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="{{ route('mt.sheet_designs.destroy', $sd) }}" onsubmit="return confirm('Are you sure you want to delete this sheet design? All linked products will be unlinked (set to no design) but not deleted.')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="6">No sheet designs added yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">
      {{ $sheetDesigns->appends(request()->query())->links() }}
    </div>
  </div>
@endsection
