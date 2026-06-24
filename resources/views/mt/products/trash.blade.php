@extends('mt.layouts.app')

@section('content')
@php
  $q = $q ?? '';
@endphp

<div class="page-header">
  <h2 class="page-title">Trashed Products</h2>
  <div class="page-actions">
    <form method="GET" class="flex gap-8 items-center">
      <input name="q" value="{{ $q }}" placeholder="Search name..." class="mt-input" style="width:220px;">
      <button class="btn btn-primary btn-sm">Search</button>
    </form>
  </div>
</div>

<div class="page-actions mb-16" style="display:flex;gap:8px;align-items:center;">
  <a href="{{ route('mt.products.index') }}" class="btn btn-secondary">← Back to products</a>
  <form id="bulk-restore-form" method="POST" action="{{ route('mt.products.bulk_restore') }}" style="display:inline-block;margin-left:8px;">
    @csrf
    <button id="bulk-restore-btn" type="button" class="btn btn-success" disabled>Restore selected</button>
  </form>
  <form id="bulk-force-form" method="POST" action="{{ route('mt.products.bulk_force_destroy') }}" style="display:inline-block;margin-left:8px;">
    @csrf
    <button id="bulk-force-btn" type="button" class="btn btn-danger" disabled>Delete permanently</button>
  </form>
</div>

<div class="table-card">
  <table class="mt-table">
    <thead>
      <tr>
        <th style="width:36px;"><input id="select-all" type="checkbox" /></th>
        <th>Name</th>
        <th>Company</th>
        <th>Category</th>
        <th>Deleted At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $p)
        <tr>
          <td><input class="row-check" type="checkbox" value="{{ $p->id }}" /></td>
          <td class="font-bold">{{ $p->name }}</td>
          <td>{{ $p->company?->name ?? '-' }}</td>
          <td>{{ $p->category?->name ?? '-' }}</td>
          <td>{{ $p->deleted_at?->toDateTimeString() ?? '-' }}</td>
          <td>
            <form method="POST" action="{{ route('mt.products.restore', $p) }}" style="display:inline-block;">
              @csrf
              <button class="btn btn-success btn-xs">Restore</button>
            </form>
            <form method="POST" action="{{ route('mt.products.force_destroy', $p) }}" style="display:inline-block;" onsubmit="return confirm('Permanently delete this product?');">
              @csrf
              <button class="btn btn-danger btn-xs">Delete permanently</button>
            </form>
          </td>
        </tr>
      @empty
        <tr class="empty-row"><td colspan="6">No trashed products found.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination-wrap">{{ $products->links() }}</div>
</div>

<script>
  (function(){
    const selectAll = document.getElementById('select-all');
    const rowChecks = () => Array.from(document.querySelectorAll('.row-check'));
    const restoreBtn = document.getElementById('bulk-restore-btn');
    const forceBtn = document.getElementById('bulk-force-btn');
    const restoreForm = document.getElementById('bulk-restore-form');
    const forceForm = document.getElementById('bulk-force-form');

    function updateButton() {
      const checked = rowChecks().filter(c => c.checked).map(c => c.value);
      restoreBtn.disabled = checked.length === 0;
      forceBtn.disabled = checked.length === 0;
    }

    if (selectAll) {
      selectAll.addEventListener('change', function(){
        rowChecks().forEach(ch => ch.checked = selectAll.checked);
        updateButton();
      });
    }

    document.addEventListener('change', function(e){
      if (e.target && e.target.classList && e.target.classList.contains('row-check')) updateButton();
    });

    restoreBtn?.addEventListener('click', function(){
      const checked = rowChecks().filter(c => c.checked).map(c => c.value);
      if (checked.length === 0) return;
      Array.from(restoreForm.querySelectorAll('input[name="ids[]"]')).forEach(n => n.remove());
      checked.forEach(id => {
        const inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value=id; restoreForm.appendChild(inp);
      });
      restoreForm.submit();
    });

    forceBtn?.addEventListener('click', function(){
      const checked = rowChecks().filter(c => c.checked).map(c => c.value);
      if (checked.length === 0) return;
      if (!confirm('Permanently delete ' + checked.length + ' products? This cannot be undone.')) return;
      Array.from(forceForm.querySelectorAll('input[name="ids[]"]')).forEach(n => n.remove());
      checked.forEach(id => {
        const inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value=id; forceForm.appendChild(inp);
      });
      forceForm.submit();
    });
  })();
</script>
