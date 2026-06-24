@extends('mt.layouts.app')

@section('content')
@php
  $from = $from ?? '';
  $to = $to ?? '';
  $total = $total ?? 0;
@endphp

  <div class="page-header">
    <h2 class="page-title">Expenses</h2>
    <a href="{{ route('mt.expenses.create') }}" class="btn btn-success btn-sm">+ Add Expense</a>
  </div>

  <div class="filter-bar">
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
    <div class="ml-auto">
      <div class="total-badge">Total: {{ number_format((float)$total,2) }}</div>
    </div>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Title</th>
          <th>Category</th>
          <th>Vendor</th>
          <th class="text-right">Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->expense_date?->format('Y-m-d') }}</td>
            <td class="font-bold">{{ $r->title }}</td>
            <td>{{ $r->category ?? '-' }}</td>
            <td>{{ $r->vendor ?? '-' }}</td>
            <td class="text-right font-bold">{{ number_format((float)$r->amount,2) }}</td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="5">No expenses found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination-wrap">{{ $rows->links() }}</div>
  </div>
@endsection
