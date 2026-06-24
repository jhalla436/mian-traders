@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Company</h2>
    <a href="{{ route('mt.companies.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('mt.companies.store') }}" class="form-stack">
      @csrf
      @include('mt.companies.form', ['company' => $company])
      <button class="btn btn-success">
        Save Company
      </button>
    </form>
  </div>
@endsection
