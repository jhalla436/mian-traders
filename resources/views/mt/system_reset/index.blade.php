@extends('mt.layouts.app')

@section('content')
<div class="page-header">
  <div>
    <h2 class="page-title">Backup & Reset</h2>
    <div class="page-subtitle">Create a backup, then clear selected dummy data.</div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div style="display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:16px;align-items:start;">
  <div class="card">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px;">
      <div>
        <h3 style="margin:0 0 6px;font-size:18px;font-weight:800;color:#0f172a;">Reset Sections</h3>
        <p class="text-muted" style="margin:0;font-size:13px;line-height:1.5;">
          A JSON backup is saved automatically before anything is deleted. Users, shops, sessions, cache, jobs and migration records are kept.
        </p>
      </div>
      <button type="button" class="btn btn-secondary btn-sm" id="selectAllResetGroups">Select All</button>
    </div>

    <form method="POST" action="{{ route('mt.system_reset.store') }}" id="systemResetForm">
      @csrf

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:10px;margin-bottom:18px;">
        @foreach($groups as $group)
          <label style="display:flex;gap:10px;align-items:flex-start;border:1px solid #e2e8f0;border-radius:8px;padding:12px;background:#fff;cursor:pointer;">
            <input type="checkbox" name="groups[]" value="{{ $group['key'] }}" class="reset-group-checkbox" style="margin-top:3px;width:16px;height:16px;">
            <span>
              <span style="display:block;font-weight:800;color:#0f172a;font-size:14px;">{{ $group['label'] }}</span>
              <span style="display:block;color:#64748b;font-size:12px;line-height:1.45;margin-top:3px;">{{ $group['description'] }}</span>
              <span style="display:inline-block;margin-top:8px;font-size:11px;font-weight:800;color:#334155;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:999px;padding:3px 8px;">
                {{ number_format($group['count']) }} affected row(s)
              </span>
            </span>
          </label>
        @endforeach
      </div>

      <div style="border:1px solid #fecaca;background:#fef2f2;border-radius:8px;padding:14px;margin-bottom:14px;">
        <div style="font-weight:900;color:#991b1b;margin-bottom:6px;">Backup is automatic, reset is permanent.</div>
        <div style="font-size:13px;color:#7f1d1d;line-height:1.5;">
          Type <b>RESET</b> below to confirm. The backup file can be downloaded from the Recent Backups panel.
        </div>
      </div>

      <div class="form-grid" style="align-items:end;">
        <div class="form-group">
          <label class="form-label">Confirmation</label>
          <input name="confirm_text" class="mt-input" placeholder="Type RESET" autocomplete="off">
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-danger" style="width:100%;">Backup and Reset Selected</button>
        </div>
      </div>
    </form>
  </div>

  <div class="card">
    <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;color:#0f172a;">Recent Backups</h3>
    @forelse($backups as $backup)
      <div style="border-bottom:1px solid #e2e8f0;padding:10px 0;">
        <div style="font-size:12px;font-weight:800;color:#0f172a;word-break:break-all;">{{ $backup['filename'] }}</div>
        <div style="font-size:11px;color:#64748b;margin-top:3px;">{{ $backup['modified'] }} · {{ $backup['size'] }}</div>
        <a href="{{ route('mt.system_reset.download', $backup['filename']) }}" class="btn btn-secondary btn-sm" style="margin-top:8px;">Download</a>
      </div>
    @empty
      <div class="text-muted" style="font-size:13px;">No reset backups yet.</div>
    @endforelse
  </div>
</div>

<script>
  document.getElementById('selectAllResetGroups')?.addEventListener('click', function() {
    const boxes = Array.from(document.querySelectorAll('.reset-group-checkbox'));
    const shouldCheck = boxes.some(box => !box.checked);
    boxes.forEach(box => box.checked = shouldCheck);
    this.textContent = shouldCheck ? 'Clear All' : 'Select All';
  });

  document.getElementById('systemResetForm')?.addEventListener('submit', function(event) {
    const checked = document.querySelectorAll('.reset-group-checkbox:checked').length;
    if (!checked) {
      event.preventDefault();
      alert('Select at least one reset section.');
      return;
    }

    if (!confirm('Backup will be created first. Continue with reset?')) {
      event.preventDefault();
    }
  });
</script>
@endsection
