@extends('mt.layouts.app')

@section('content')
<div class="page-header">
  <h2 class="page-title">My Account</h2>
</div>

<div class="card" style="max-width:620px;">
  <form method="POST" action="{{ route('mt.account.update') }}" class="form-stack">
    @csrf

    <div class="badge badge-info" style="align-self:start;">Role: {{ auth()->user()?->role }}</div>

    <div class="form-group">
      <label class="form-label">Name</label>
      <input name="name" value="{{ old('name', $user->name) }}" placeholder="Name" class="mt-input">
    </div>
    <div class="form-group">
      <label class="form-label">Email</label>
      <input name="email" value="{{ old('email', $user->email) }}" placeholder="Email" class="mt-input">
    </div>

    <hr class="separator">

    <div class="font-bold">Change Password (Optional)</div>

    <div class="form-group">
      <label class="form-label">Current Password</label>
      <input name="current_password" type="password" placeholder="Current Password" class="mt-input">
    </div>
    <div class="form-group">
      <label class="form-label">New Password</label>
      <input name="new_password" type="password" placeholder="New Password (min 4)" class="mt-input">
    </div>

    @if($errors->any())
      <div class="errors-box">
        <b>Fix errors:</b>
        <ul>
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <button class="btn btn-primary" style="justify-self:start;">Save Changes</button>
  </form>
</div>
@endsection
