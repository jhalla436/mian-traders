@extends('mt.layouts.app')

@section('content')
  @php
    $role = auth()->user()->role ?? '';
    $canManage = in_array($role, ['admin', 'manager']);
  @endphp

  <div style="background:#fff;border-radius:10px;padding:14px;margin-bottom:12px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
      <div>
        <h2 style="margin:0;">{{ $company->name }}</h2>
        <div style="color:#6b7280;font-size:12px;margin-top:6px;">
          <span style="font-weight:800;">Company</span>
          @if(!empty($company->phone_main))
            @php $waCompany = \App\Support\WhatsApp::url($company->phone_main); @endphp
            • Phone: <b>{{ $company->phone_main }}</b>
            @if($waCompany)
              <a href="{{ $waCompany }}" target="_blank" rel="noopener"
                 style="margin-left:6px;padding:2px 8px;border-radius:999px;background:#16a34a;color:#fff;text-decoration:none;font-size:11px;font-weight:800;">WA</a>
            @endif
          @endif
          @if(!empty($company->email)) • Email: <b>{{ $company->email }}</b> @endif
          @if(!empty($company->address)) • Address: <b>{{ $company->address }}</b> @endif
        </div>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('mt.companies.edit', $company) }}"
           style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Company Settings</a>

        @if($canManage)
          <a href="{{ route('mt.company_contacts.create', $company) }}"
             style="padding:8px 12px;border-radius:8px;background:#16a34a;color:#fff;text-decoration:none;font-weight:800;">+ Add Contact</a>
        @endif
      </div>
    </div>

    <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <div style="display:flex;gap:8px;align-items:center;background:#f9fafb;border:1px solid #eee;border-radius:10px;padding:8px;">
        <a href="{{ route('mt.companies.edit', $company) }}"
           style="padding:8px 10px;border-radius:8px;background:#fff;border:1px solid #e5e7eb;color:#111827;text-decoration:none;">Details</a>

        <span style="padding:8px 10px;border-radius:8px;background:#111827;color:#fff;font-weight:800;">Contacts</span>
      </div>

      <form method="GET" action="{{ route('mt.company_contacts.index', $company) }}" style="display:flex;gap:8px;align-items:center;">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search name / role / phone..."
               style="padding:10px;border:1px solid #ddd;border-radius:10px;min-width:260px;">
        <button style="padding:10px 12px;border-radius:10px;border:0;background:#111827;color:#fff;cursor:pointer;">Search</button>
        @if(!empty($q))
          <a href="{{ route('mt.company_contacts.index', $company) }}"
             style="padding:10px 12px;border-radius:10px;background:#e5e7eb;color:#111827;text-decoration:none;">Clear</a>
        @endif
      </form>
    </div>
  </div>

  <div style="background:#fff;border-radius:10px;padding:14px;">
    <div style="font-size:12px;color:#6b7280;margin-bottom:10px;">
      Total contacts: <b>{{ $contacts->count() }}</b>
      @if(!empty($q)) • Filter: <b>{{ $q }}</b> @endif
    </div>

    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Name</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Role</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Phone</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Reports To</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Active</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($contacts as $ct)
          <tr>
            <td style="border-bottom:1px solid #eee;padding:8px;">
              <div style="font-weight:900;">{{ $ct->name }}</div>
              <div style="font-size:11px;color:#6b7280;">
                @if(!empty($ct->email)) {{ $ct->email }} @endif
                @if(!empty($ct->address)) • {{ $ct->address }} @endif
              </div>
              @if(!empty($ct->note))
                <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $ct->note }}</div>
              @endif
            </td>

            <td style="border-bottom:1px solid #eee;padding:8px;">{{ $ct->role_title ?? '-' }}</td>

            <td style="border-bottom:1px solid #eee;padding:8px;">
              @php
                $p1 = $ct->phone_primary ?? '';
                $p2 = $ct->phone_alt ?? '';
                $wa1 = \App\Support\WhatsApp::url($p1);
                $wa2 = \App\Support\WhatsApp::url($p2);
              @endphp

              <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <span>{{ $p1 !== '' ? $p1 : '-' }}</span>
                @if($wa1)
                  <a href="{{ $wa1 }}" target="_blank" rel="noopener"
                     style="padding:2px 8px;border-radius:999px;background:#16a34a;color:#fff;text-decoration:none;font-size:11px;font-weight:800;">WA</a>
                @endif
              </div>

              @if(!empty($p2))
                <div style="font-size:11px;color:#6b7280;margin-top:4px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                  <span>Alt: {{ $p2 }}</span>
                  @if($wa2)
                    <a href="{{ $wa2 }}" target="_blank" rel="noopener"
                       style="padding:2px 8px;border-radius:999px;background:#16a34a;color:#fff;text-decoration:none;font-size:11px;font-weight:800;">WA</a>
                  @endif
                </div>
              @endif
            </td>

            <td style="border-bottom:1px solid #eee;padding:8px;">{{ $ct->reportsTo?->name ?? '-' }}</td>
            <td style="border-bottom:1px solid #eee;padding:8px;">{{ $ct->is_active ? 'Yes' : 'No' }}</td>

            <td style="border-bottom:1px solid #eee;padding:8px;display:flex;gap:8px;flex-wrap:wrap;">
              @if($canManage)
                <a href="{{ route('mt.company_contacts.edit', [$company, $ct]) }}"
                   style="padding:6px 10px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Edit</a>

                <form method="POST" action="{{ route('mt.company_contacts.destroy', [$company, $ct]) }}" onsubmit="return confirm('Delete this contact?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" style="padding:6px 10px;border-radius:8px;border:0;background:#dc2626;color:#fff;cursor:pointer;">Delete</button>
                </form>
              @else
                <span style="color:#6b7280;font-size:12px;">View only</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="padding:8px;color:#6b7280;">No contacts found.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">
      <a href="{{ route('mt.companies.index') }}" style="padding:10px 12px;border-radius:10px;background:#111827;color:#fff;text-decoration:none;">Back to Companies</a>
    </div>
  </div>
@endsection
