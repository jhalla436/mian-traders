@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Edit Sheet Design</h2>
    <a href="{{ route('mt.sheet_designs.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('mt.sheet_designs.update', $sheetDesign) }}" class="form-stack">
      @csrf
      @method('PUT')
      @include('mt.sheet_designs.form', ['sheetDesign' => $sheetDesign])
      <button class="btn btn-success">
        Update Design
      </button>
    </form>
  </div>
@endsection
