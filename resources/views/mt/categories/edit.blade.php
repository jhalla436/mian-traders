@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Edit Category</h2>
    <a href="{{ route('mt.categories.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="{{ route('mt.categories.update', $category) }}" class="form-stack">
      @csrf @method('PUT')
      @include('mt.categories.form', [
        'category' => $category,
        'companies' => $companies,
        'availableParents' => $availableParents
      ])
      <button class="btn btn-primary" style="justify-self:start;">Update Category</button>
    </form>
  </div>
@endsection
