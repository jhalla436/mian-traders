@extends('mt.layouts.app')

@section('content')
<div class="page-header">
  <h2 class="page-title">Add User</h2>
  <a href="{{ route('mt.users.index') }}" class="btn btn-secondary btn-sm">Back</a>
</div>

<div class="card">
  <form method="POST" action="{{ route('mt.users.store') }}" class="form-stack">
    @csrf
    @include('mt.users.form', ['user'=>$user])
    <button class="btn btn-success">Save User</button>
  </form>
</div>
@endsection
