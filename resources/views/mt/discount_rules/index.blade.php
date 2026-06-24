@extends('mt.layouts.app')

@section('content')
  <div class="page-header">
    <h2 class="page-title">Discount Rules</h2>
    <a href="{{ route('mt.discount_rules.create') }}" class="btn btn-primary btn-sm">+ Add</a>
  </div>

  <div class="table-card">
    <table class="mt-table">
      <thead>
        <tr>
          <th>Scope</th>
          <th>Discount Type</th>
          <th>Rule</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rules as $r)
          @php
            $scopeName = $r->scope_type.' #'.$r->scope_id;
            if($r->scope_type==='company') $scopeName = 'Company: '.($companies[$r->scope_id]->name ?? ('#'.$r->scope_id));
            if($r->scope_type==='category') $scopeName = 'Category: '.($categories[$r->scope_id]->name ?? ('#'.$r->scope_id));
            if($r->scope_type==='product') $scopeName = 'Product: '.($products[$r->scope_id]->name ?? ('#'.$r->scope_id));
            $ruleText = $r->rule_type;
            if($r->rule_type==='percent_once') $ruleText = ($r->percent_1 ?? 0).'%';
            if($r->rule_type==='percent_twostep') $ruleText = ($r->percent_1 ?? 0).'% + '.($r->percent_2 ?? 0).'%';
            if($r->rule_type==='fixed_purchase') $ruleText = 'Fixed: '.($r->fixed_purchase_price ?? 0);
            if($r->rule_type==='none') $ruleText = 'No discount (manual)';
          @endphp
          <tr>
            <td class="font-bold">{{ $scopeName }}</td>
            <td>{{ $r->discountType?->name }}</td>
            <td><span class="badge badge-info">{{ $ruleText }}</span></td>
            <td>
              <div class="actions">
                <a href="{{ route('mt.discount_rules.edit', $r) }}" class="btn btn-secondary btn-xs">Edit</a>
                <form method="POST" action="{{ route('mt.discount_rules.destroy', $r) }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
        @if($rules->count()===0)
          <tr class="empty-row"><td colspan="4">No discount rules yet.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
@endsection
