@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <div>
      <h2 class="page-title">{{ $company->name }}</h2>
      <div class="page-subtitle">
        <span class="font-bold">Company</span>
        @if(!empty($company->phone_main)) • {{ $company->phone_main }} @endif
        @if(!empty($company->address)) • {{ $company->address }} @endif
      </div>
    </div>
    <a href="{{ route('mt.companies.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <div class="filter-bar" style="margin-bottom:16px;">
    <div class="mt-tabs">
      <span class="mt-tab active">Details</span>
      <a href="{{ route('mt.company_contacts.index', $company) }}" class="mt-tab">Contacts</a>
    </div>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('mt.companies.update', $company) }}" class="form-stack">
      @csrf @method('PUT')
      @include('mt.companies.form', ['company' => $company])
      <button class="btn btn-primary" style="justify-self:start;">Update Company</button>
    </form>
    <hr class="separator">
    <div class="flex justify-end">
      <a href="{{ route('mt.company_contacts.index', $company) }}" class="btn btn-secondary btn-sm">Manage Contacts →</a>
    </div>
  </div>
@endsection
