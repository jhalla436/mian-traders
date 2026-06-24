@extends('mt.layouts.app')

@section('content')
@php
  $from = $from ?? '';
  $to = $to ?? '';
  $balance = $balance ?? 0;
@endphp

  <div class="page-header">
    <div>
      <div class="page-subtitle">Company Ledger</div>
      <h2 class="page-title">{{ $company->name }}</h2>
    </div>
    <div class="page-actions">
      <a href="{{ route('mt.companies.index') }}" class="btn btn-secondary btn-sm">← Back</a>
      <div class="total-badge">
        Balance: {{ number_format((float)$balance,2) }}
        <span class="text-xs" style="opacity:0.7;">(Debit - Credit)</span>
      </div>
    </div>
  </div>

  <div class="filter-bar" style="justify-content:space-between;">
    <form method="GET" class="flex gap-12 flex-wrap items-end">
      <div class="filter-group">
        <span class="filter-label">From</span>
        <input type="date" name="from" value="{{ $from }}" class="mt-input">
      </div>
      <div class="filter-group">
        <span class="filter-label">To</span>
        <input type="date" name="to" value="{{ $to }}" class="mt-input">
      </div>
      <button class="btn btn-primary btn-sm">Filter</button>
    </form>
    <a href="{{ route('mt.purchases.create', ['company_id'=>$company->id]) }}" class="btn btn-success btn-sm">+ Stock In (Purchase)</a>
  </div>

  <div class="card mb-16">
    <h3 style="margin:0 0 14px;font-size:16px;font-weight:800;">Add Entry</h3>
    <form method="POST" action="{{ route('mt.company_ledger.store', $company) }}" class="flex gap-12 flex-wrap items-end">
      @csrf
      <div class="form-group">
        <label class="form-label">Date</label>
        <input type="date" name="entry_date" value="{{ old('entry_date', now()->toDateString()) }}" required class="mt-input">
      </div>
      <div class="form-group">
        <label class="form-label">Type</label>
        <select name="entry_type" class="mt-select">
          <option value="payment">Payment</option>
          <option value="transport">Transport</option>
          <option value="adjustment">Adjustment</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Direction</label>
        <select name="direction" class="mt-select">
          <option value="credit">Credit (reduces payable)</option>
          <option value="debit">Debit (increases payable)</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Amount</label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required class="mt-input" style="width:140px;text-align:right;">
      </div>
      <div class="form-group" style="flex:1;min-width:200px;">
        <label class="form-label">Description</label>
        <input name="description" value="{{ old('description') }}" class="mt-input" placeholder="Optional">
      </div>
      <button class="btn btn-teal btn-sm">Save</button>
    </form>
    @if($errors->any())
      <div class="form-error" style="margin-top:10px;">{{ implode(' | ', $errors->all()) }}</div>
    @endif
  </div>

  <div class="table-card">
    <div style="padding:16px 20px 0;display:flex;gap:12px;flex-wrap:wrap;">
      <div class="badge badge-info">Debit: {{ number_format((float)$debitTotal,2) }}</div>
      <div class="badge badge-success">Credit: {{ number_format((float)$creditTotal,2) }}</div>
    </div>
    <table class="mt-table" style="margin-top:12px;">
      <thead>
        <tr>
          <th>Date</th>
          <th>Type</th>
          <th>Description</th>
          <th class="text-right">Debit</th>
          <th class="text-right">Credit</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->entry_date?->format('Y-m-d') }}</td>
            <td class="font-bold">{{ $r->entry_type }}</td>
            <td>{{ $r->description ?? '-' }}</td>
            <td class="text-right">{{ $r->direction==='debit' ? number_format((float)$r->amount,2) : '-' }}</td>
            <td class="text-right">{{ $r->direction==='credit' ? number_format((float)$r->amount,2) : '-' }}</td>
            <td>
              <form method="POST" action="{{ route('mt.company_ledger.destroy', [$company, $r]) }}" onsubmit="return confirm('Delete this ledger entry?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-xs">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="6">No ledger entries.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $rows->links() }}</div>
  </div>
@endsection
