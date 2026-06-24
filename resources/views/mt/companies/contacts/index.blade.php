@extends('mt.layouts.app')

@section('content')
  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['admin', 'manager']);
  @endphp

  <div class="page-header">
    <div>
      <h2 class="page-title">{{ $company->name }}</h2>
      <div class="page-subtitle">
        <span class="font-bold">Company</span>
        @if(!empty($company->phone_main))
          @php $waCompany = \App\Support\WhatsApp::url($company->phone_main); @endphp
          • Phone: <b>{{ $company->phone_main }}</b>
          @if($waCompany) <a href="{{ $waCompany }}" target="_blank" rel="noopener" class="badge-wa" style="margin-left:4px;">WA</a> @endif
        @endif
        @if(!empty($company->email)) • Email: <b>{{ $company->email }}</b> @endif
        @if(!empty($company->address)) • Address: <b>{{ $company->address }}</b> @endif
      </div>
    </div>
    <div class="page-actions">
      <a href="{{ route('mt.companies.edit', $company) }}" class="btn btn-secondary btn-sm">Company Settings</a>
      @if($canManage)
        <a href="{{ route('mt.company_contacts.create', $company) }}" class="btn btn-success btn-sm">+ Add Contact</a>
      @endif
    </div>
  </div>

  <div class="filter-bar" style="justify-content:space-between;">
    <div class="flex gap-8 items-center flex-wrap">
      <div class="mt-tabs">
        <a href="{{ route('mt.companies.edit', $company) }}" class="mt-tab">Details</a>
        <span class="mt-tab active">Contacts</span>
      </div>
      <form method="GET" action="{{ route('mt.company_contacts.index', $company) }}" class="flex gap-8 items-center">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search name / role / phone..." class="mt-input" style="min-width:240px;">
        <button class="btn btn-secondary btn-sm">Search</button>
        @if(!empty($q))
          <a href="{{ route('mt.company_contacts.index', $company) }}" class="btn btn-outline btn-sm">Clear</a>
        @endif
      </form>
    </div>
    <span class="text-xs text-muted">Total: <b>{{ $contacts->count() }}</b>@if(!empty($q)) • Filter: <b>{{ $q }}</b>@endif</span>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Role</th>
          <th>Phone</th>
          <th>Reports To</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($contacts as $ct)
          <tr>
            <td>
              <div class="font-bold">{{ $ct->name }}</div>
              <div class="text-xs text-muted">
                @if(!empty($ct->email)) {{ $ct->email }} @endif
                @if(!empty($ct->address)) • {{ $ct->address }} @endif
              </div>
              @if(!empty($ct->note))
                <div class="text-xs text-muted" style="margin-top:2px;">{{ $ct->note }}</div>
              @endif
            </td>
            <td>{{ $ct->role_title ?? '-' }}</td>
            <td>
              @php $p1 = $ct->phone_primary ?? ''; $p2 = $ct->phone_alt ?? ''; $wa1 = \App\Support\WhatsApp::url($p1); $wa2 = \App\Support\WhatsApp::url($p2); @endphp
              <div class="flex gap-8 items-center flex-wrap">
                <span>{{ $p1 !== '' ? $p1 : '-' }}</span>
                @if($wa1) <a href="{{ $wa1 }}" target="_blank" rel="noopener" class="badge-wa">WA</a> @endif
              </div>
              @if(!empty($p2))
                <div class="text-xs text-muted flex gap-8 items-center flex-wrap" style="margin-top:4px;">
                  <span>Alt: {{ $p2 }}</span>
                  @if($wa2) <a href="{{ $wa2 }}" target="_blank" rel="noopener" class="badge-wa">WA</a> @endif
                </div>
              @endif
            </td>
            <td>{{ $ct->reportsTo?->name ?? '-' }}</td>
            <td><span class="badge {{ $ct->is_active ? 'badge-success' : 'badge-danger' }}">{{ $ct->is_active ? 'Yes' : 'No' }}</span></td>
            <td>
              <div class="actions">
                @if($canManage)
                  <a href="{{ route('mt.company_contacts.edit', [$company, $ct]) }}" class="btn btn-primary btn-xs">Edit</a>
                  <form method="POST" action="{{ route('mt.company_contacts.destroy', [$company, $ct]) }}" onsubmit="return confirm('Delete this contact?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                  </form>
                @else
                  <span class="text-muted text-xs">View only</span>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="6">No contacts found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">
      <a href="{{ route('mt.companies.index') }}" class="btn btn-secondary btn-sm">← Back to Companies</a>
    </div>
  </div>
@endsection
