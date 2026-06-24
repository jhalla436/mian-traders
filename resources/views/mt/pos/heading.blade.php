
@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">{{ $heading->name }}</h2>
    <a href="{{ route('mt.pos.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="card">
    <p>Variants page will show pictures + codes here.</p>
  </div>
@endsection

