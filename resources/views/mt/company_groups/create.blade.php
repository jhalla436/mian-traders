@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Company Group</h2>
    <a href="{{ route('mt.company_groups.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('mt.company_groups.store') }}" class="form-stack">
      @csrf
      @include('mt.company_groups.form', ['group' => $group])
      <button class="btn btn-success">
        Save Group
      </button>
    </form>
  </div>
@endsection
