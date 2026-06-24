@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Expense</h2>
    <a href="{{ route('mt.expenses.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <div class="card" style="max-width:900px;">
    <form method="POST" action="{{ route('mt.expenses.store') }}" class="form-stack">
      @csrf
      <div class="flex gap-12 flex-wrap items-end">
        <div class="form-group">
          <label class="form-label">Date</label>
          <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required class="mt-input">
          @error('expense_date') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group" style="flex:1;min-width:240px;">
          <label class="form-label">Title</label>
          <input name="title" value="{{ old('title') }}" required class="mt-input" placeholder="Shop Rent / Salary / etc">
          @error('title') <div class="form-error">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="flex gap-12 flex-wrap items-end">
        <div class="form-group">
          <label class="form-label">Category</label>
          <input name="category" value="{{ old('category') }}" class="mt-input" placeholder="rent/salary/transport">
          @error('category') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group" style="flex:1;min-width:220px;">
          <label class="form-label">Vendor (optional)</label>
          <input name="vendor" value="{{ old('vendor') }}" class="mt-input" placeholder="Landlord / Worker">
          @error('vendor') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label class="form-label">Payment Method</label>
          <input name="payment_method" value="{{ old('payment_method') }}" class="mt-input" placeholder="cash/bank">
          @error('payment_method') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label class="form-label">Amount</label>
          <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required class="mt-input" style="width:160px;text-align:right;">
          @error('amount') <div class="form-error">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Note</label>
        <input name="note" value="{{ old('note') }}" class="mt-input" placeholder="Optional">
        @error('note') <div class="form-error">{{ $message }}</div> @enderror
      </div>

      <button class="btn btn-success" style="justify-self:start;">Save Expense</button>
    </form>
  </div>
@endsection
