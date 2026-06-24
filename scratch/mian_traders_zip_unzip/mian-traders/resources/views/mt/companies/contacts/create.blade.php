@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;border-radius:10px;padding:14px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Add Contact — {{ $company->name }}</h2>
      <div style="font-size:12px;color:#6b7280;margin-top:6px;">Add CEO / Sales Head / Manager / Coordinator etc.</div>
    </div>
    <a href="{{ route('mt.company_contacts.index', $company) }}" style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Back</a>
  </div>

  <div style="background:#fff;border-radius:10px;padding:14px;max-width:950px;">
    <form method="POST" action="{{ route('mt.company_contacts.store', $company) }}" style="display:grid;gap:12px;">
      @csrf

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
          <label style="font-size:12px;color:#6b7280;">Name</label>
          <input name="name" value="{{ old('name') }}" required
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
          @error('name') <div style="color:#b91c1c;font-size:12px;">{{ $message }}</div> @enderror
        </div>
        <div>
          <label style="font-size:12px;color:#6b7280;">Role / Title</label>
          <input name="role_title" value="{{ old('role_title') }}" placeholder="CEO / Sales Head / Manager..."
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
          <label style="font-size:12px;color:#6b7280;">Phone (Primary)</label>
          <input name="phone_primary" value="{{ old('phone_primary') }}" placeholder="0300xxxxxxx"
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
        <div>
          <label style="font-size:12px;color:#6b7280;">Phone (Alt)</label>
          <input name="phone_alt" value="{{ old('phone_alt') }}" placeholder="optional"
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
          <label style="font-size:12px;color:#6b7280;">Email</label>
          <input name="email" value="{{ old('email') }}" placeholder="optional"
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
        <div>
          <label style="font-size:12px;color:#6b7280;">Reports To (Hierarchy)</label>
          <select name="reports_to_contact_id" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
            <option value="">— None —</option>
            @foreach($reportToOptions as $r)
              <option value="{{ $r->id }}" @selected(old('reports_to_contact_id') == $r->id)>
                {{ $r->name }}@if($r->role_title) ({{ $r->role_title }})@endif
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div>
        <label style="font-size:12px;color:#6b7280;">Address</label>
        <input name="address" value="{{ old('address') }}" placeholder="optional"
               style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;align-items:end;">
        <div>
          <label style="font-size:12px;color:#6b7280;">Sort Order (0 first)</label>
          <input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}"
                 style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;">
        </div>
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
          <label style="display:flex;gap:8px;align-items:center;">
            <input type="checkbox" name="is_active" value="1" checked>
            Active
          </label>
        </div>
      </div>

      <div>
        <label style="font-size:12px;color:#6b7280;">Note</label>
        <textarea name="note" rows="3" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;" placeholder="optional">{{ old('note') }}</textarea>
      </div>

      @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;">
          <ul style="margin:0;padding-left:18px;">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button style="padding:12px 14px;border-radius:10px;border:0;background:#16a34a;color:#fff;cursor:pointer;font-weight:800;">Save Contact</button>
        <a href="{{ route('mt.company_contacts.index', $company) }}" style="padding:12px 14px;border-radius:10px;background:#111827;color:#fff;text-decoration:none;">Cancel</a>
      </div>
    </form>
  </div>
@endsection
