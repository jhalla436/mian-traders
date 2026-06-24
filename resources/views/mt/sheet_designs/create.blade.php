@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Sheet Design</h2>
    <a href="{{ route('mt.sheet_designs.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('mt.sheet_designs.store') }}" class="form-stack">
      @csrf
      @include('mt.sheet_designs.form', ['sheetDesign' => $sheetDesign])
      <button class="btn btn-success">
        Save Design
      </button>
    </form>
  </div>
@endsection
