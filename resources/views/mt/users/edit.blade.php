@extends('mt.layouts.app')

@section('content')
<div class="page-header">
  <h2 class="page-title">Edit User</h2>
  <a href="{{ route('mt.users.index') }}" class="btn btn-secondary btn-sm">Back</a>
</div>

<div class="card">
  <form method="POST" action="{{ route('mt.users.update', $user) }}" class="form-stack">
    @csrf
    @method('PUT')
    @include('mt.users.form', ['user'=>$user])
    <button class="btn btn-primary">Update User</button>
  </form>
</div>
@endsection
