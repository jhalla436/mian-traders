@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <div>
      <h2 class="page-title">Edit Contact — {{ $company->name }}</h2>
      <div class="page-subtitle"><b>{{ $contact->name }}</b>@if(!empty($contact->role_title)) • {{ $contact->role_title }}@endif</div>
    </div>
    <a href="{{ route('mt.company_contacts.index', $company) }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card" style="max-width:950px;">
    <form method="POST" action="{{ route('mt.company_contacts.update', [$company, $contact]) }}" class="form-stack">
      @csrf @method('PUT')
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Name</label><input name="name" value="{{ old('name', $contact->name) }}" required class="mt-input">@error('name')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label class="form-label">Role / Title</label><input name="role_title" value="{{ old('role_title', $contact->role_title) }}" placeholder="CEO / Sales Head / Manager..." class="mt-input"></div>
      </div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Phone (Primary)</label><input name="phone_primary" value="{{ old('phone_primary', $contact->phone_primary) }}" placeholder="0300xxxxxxx" class="mt-input"></div>
        <div class="form-group"><label class="form-label">Phone (Alt)</label><input name="phone_alt" value="{{ old('phone_alt', $contact->phone_alt) }}" placeholder="optional" class="mt-input"></div>
      </div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Email</label><input name="email" value="{{ old('email', $contact->email) }}" placeholder="optional" class="mt-input"></div>
        <div class="form-group"><label class="form-label">Reports To</label>
          <select name="reports_to_contact_id" class="mt-select"><option value="">— None —</option>@foreach($reportToOptions as $r)<option value="{{ $r->id }}" @selected((string)old('reports_to_contact_id', $contact->reports_to_contact_id) === (string)$r->id)>{{ $r->name }}@if($r->role_title) ({{ $r->role_title }})@endif</option>@endforeach</select>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Address</label><input name="address" value="{{ old('address', $contact->address) }}" placeholder="optional" class="mt-input"></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Sort Order</label><input name="sort_order" type="number" min="0" value="{{ old('sort_order', $contact->sort_order ?? 0) }}" class="mt-input"></div>
        <div class="form-group" style="justify-content:flex-end;"><label class="form-checkbox"><input type="checkbox" name="is_active" value="1" @checked((int)old('is_active', $contact->is_active ? 1 : 0) === 1)> Active</label></div>
      </div>
      <div class="form-group"><label class="form-label">Note</label><textarea name="note" rows="3" class="mt-input" style="resize:vertical;" placeholder="optional">{{ old('note', $contact->note) }}</textarea></div>
      @if($errors->any())<div class="errors-box"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
      <div class="flex gap-8"><button class="btn btn-primary">Update Contact</button><a href="{{ route('mt.company_contacts.index', $company) }}" class="btn btn-secondary">Cancel</a></div>
    </form>
  </div>
@endsection
