@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <h2 style="margin:0;">Add Expense</h2>
    <a href="{{ route('mt.expenses.index') }}" style="padding:10px 12px;border-radius:10px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <form method="POST" action="{{ route('mt.expenses.store') }}" style="background:#fff;padding:14px;border-radius:10px;max-width:900px;">
    @csrf

    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Date</div>
        <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required style="padding:10px;border:1px solid #ddd;border-radius:10px;">
        @error('expense_date') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>
      <div style="flex:1;min-width:260px;">
        <div style="font-size:12px;color:#6b7280;">Title</div>
        <input name="title" value="{{ old('title') }}" required style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Shop Rent / Salary / etc">
        @error('title') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:12px;">
      <div>
        <div style="font-size:12px;color:#6b7280;">Category</div>
        <input name="category" value="{{ old('category') }}" style="padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="rent/salary/transport">
        @error('category') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>
      <div style="flex:1;min-width:240px;">
        <div style="font-size:12px;color:#6b7280;">Vendor (optional)</div>
        <input name="vendor" value="{{ old('vendor') }}" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Landlord / Worker / Transporter">
        @error('vendor') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Payment Method</div>
        <input name="payment_method" value="{{ old('payment_method') }}" style="padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="cash/bank">
        @error('payment_method') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>
      <div>
        <div style="font-size:12px;color:#6b7280;">Amount</div>
        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required style="padding:10px;border:1px solid #ddd;border-radius:10px;text-align:right;width:180px;">
        @error('amount') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
      </div>
    </div>

    <div style="margin-bottom:12px;">
      <div style="font-size:12px;color:#6b7280;">Note</div>
      <input name="note" value="{{ old('note') }}" style="padding:10px;border:1px solid #ddd;border-radius:10px;width:100%;" placeholder="Optional">
      @error('note') <div style="color:#dc2626;font-size:12px;">{{ $message }}</div> @enderror
    </div>

    <button style="padding:12px 14px;border:0;border-radius:10px;background:#16a34a;color:#fff;font-weight:800;">Save</button>
  </form>
@endsection
