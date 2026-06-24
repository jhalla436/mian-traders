@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <div>
      <h2 class="page-title">Add Contact — {{ $company->name }}</h2>
      <div class="page-subtitle">Add CEO / Sales Head / Manager / Coordinator etc.</div>
    </div>
    <a href="{{ route('mt.company_contacts.index', $company) }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:950px;">
    <form method="POST" action="{{ route('mt.company_contacts.store', $company) }}" class="form-stack">
      @csrf
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Name</label><input name="name" value="{{ old('name') }}" required class="mt-input">@error('name')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label class="form-label">Role / Title</label><input name="role_title" value="{{ old('role_title') }}" placeholder="CEO / Sales Head / Manager..." class="mt-input"></div>
      </div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Phone (Primary)</label><input name="phone_primary" value="{{ old('phone_primary') }}" placeholder="0300xxxxxxx" class="mt-input"></div>
        <div class="form-group"><label class="form-label">Phone (Alt)</label><input name="phone_alt" value="{{ old('phone_alt') }}" placeholder="optional" class="mt-input"></div>
      </div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Email</label><input name="email" value="{{ old('email') }}" placeholder="optional" class="mt-input"></div>
        <div class="form-group"><label class="form-label">Reports To</label>
          <select name="reports_to_contact_id" class="mt-select"><option value="">— None —</option>@foreach($reportToOptions as $r)<option value="{{ $r->id }}" @selected(old('reports_to_contact_id') == $r->id)>{{ $r->name }}@if($r->role_title) ({{ $r->role_title }})@endif</option>@endforeach</select>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Address</label><input name="address" value="{{ old('address') }}" placeholder="optional" class="mt-input"></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Sort Order</label><input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="mt-input"></div>
        <div class="form-group" style="justify-content:flex-end;"><label class="form-checkbox"><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
      </div>
      <div class="form-group"><label class="form-label">Note</label><textarea name="note" rows="3" class="mt-input" style="resize:vertical;" placeholder="optional">{{ old('note') }}</textarea></div>
      @if($errors->any())<div class="errors-box"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
      <div class="flex gap-8"><button class="btn btn-success">Save Contact</button><a href="{{ route('mt.company_contacts.index', $company) }}" class="btn btn-secondary">Cancel</a></div>
    </form>
  </div>
@endsection
