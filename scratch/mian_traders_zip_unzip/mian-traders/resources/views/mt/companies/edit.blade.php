@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
      <div>
        <h2 style="margin:0;">{{ $company->name }}</h2>
        <div style="font-size:12px;color:#6b7280;margin-top:6px;">
          <span style="font-weight:800;">Company</span>
          @if(!empty($company->phone_main)) • {{ $company->phone_main }} @endif
          @if(!empty($company->address)) • {{ $company->address }} @endif
        </div>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('mt.companies.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
      </div>
    </div>

    <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <div style="display:flex;gap:8px;align-items:center;background:#f9fafb;border:1px solid #eee;border-radius:10px;padding:8px;">
        <span style="padding:8px 10px;border-radius:8px;background:#111827;color:#fff;font-weight:800;">Details</span>
        <a href="{{ route('mt.company_contacts.index', $company) }}" style="padding:8px 10px;border-radius:8px;background:#fff;border:1px solid #e5e7eb;color:#111827;text-decoration:none;">Contacts</a>
      </div>
    </div>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="{{ route('mt.companies.update', $company) }}" style="display:grid;gap:12px;">
      @csrf
      @method('PUT')
      @include('mt.companies.form', ['company' => $company])
      <button style="padding:12px;border-radius:10px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
        Update Company
      </button>
    </form>

    <div style="margin-top:12px;padding-top:12px;border-top:1px solid #eee;display:flex;justify-content:flex-end;">
      <a href="{{ route('mt.company_contacts.index', $company) }}" style="padding:10px 12px;border-radius:10px;background:#111827;color:#fff;text-decoration:none;">Manage Contacts</a>
    </div>
  </div>
@endsection
