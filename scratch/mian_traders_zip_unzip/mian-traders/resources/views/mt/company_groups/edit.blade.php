@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Edit Company Group</h2>
    <a href="{{ route('mt.company_groups.index') }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <form method="POST" action="{{ route('mt.company_groups.update', $group) }}" style="display:grid;gap:12px;">
      @csrf
      @method('PUT')
      @include('mt.company_groups.form', ['group' => $group])
      <button style="padding:12px;border-radius:10px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
        Update Group
      </button>
    </form>
  </div>
@endsection
