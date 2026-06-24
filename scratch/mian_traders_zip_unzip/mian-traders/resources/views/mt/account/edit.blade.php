@extends('mt.layouts.app')

@section('content')
<div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
  <h2 style="margin:0;">My Account</h2>
</div>

<div style="background:#fff;padding:14px;border-radius:10px;max-width:620px;">
  <form method="POST" action="{{ route('mt.account.update') }}" style="display:grid;gap:12px;">
    @csrf

    <div style="color:#6b7280;font-size:12px;">
      Role: <b>{{ auth()->user()?->role }}</b>
    </div>

    <input name="name" value="{{ old('name', $user->name) }}" placeholder="Name"
           style="padding:10px;border:1px solid #ddd;border-radius:10px;">

    <input name="email" value="{{ old('email', $user->email) }}" placeholder="Email"
           style="padding:10px;border:1px solid #ddd;border-radius:10px;">

    <hr style="border:0;border-top:1px solid #eee;">

    <div style="font-weight:800;">Change Password (Optional)</div>

    <input name="current_password" type="password" placeholder="Current Password"
           style="padding:10px;border:1px solid #ddd;border-radius:10px;">

    <input name="new_password" type="password" placeholder="New Password (min 4)"
           style="padding:10px;border:1px solid #ddd;border-radius:10px;">

    @if($errors->any())
      <div style="background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;">
        <div style="font-weight:800;margin-bottom:6px;">Fix errors:</div>
        <ul style="margin:0;padding-left:18px;">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <button style="padding:12px;border-radius:10px;border:0;background:#2563eb;color:#fff;cursor:pointer;">
      Save Changes
    </button>
  </form>
</div>
@endsection
