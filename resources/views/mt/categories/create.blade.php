@extends('mt.layouts.app')
@section('content')
  <div class="page-header">
    <h2 class="page-title">Add Category</h2>
    <a href="{{ route('mt.categories.index') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
  <div class="card">
    <form method="POST" action="{{ route('mt.categories.store') }}" class="form-stack">
      @csrf
      @include('mt.categories.form', [
        'category' => $category,
        'companies' => $companies,
        'selectedCompanyId' => $selectedCompanyId ?? null,
        'parentCategory' => $parentCategory ?? null,
        'parentId' => $parentId ?? null,
        'availableParents' => $availableParents ?? collect()
      ])
      <button class="btn btn-success" style="justify-self:start;">Save Category</button>
    </form>
  </div>
@endsection
