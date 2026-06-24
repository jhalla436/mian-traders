@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Discount Types</h2>
    <a href="{{ route('mt.discount_types.create') }}" style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">Add</a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Name</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Active</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($types as $t)
          <tr>
            <td style="border-bottom:1px solid #eee;padding:8px;">{{ $t->name }}</td>
            <td style="border-bottom:1px solid #eee;padding:8px;">{{ $t->is_active ? 'Yes' : 'No' }}</td>
            <td style="border-bottom:1px solid #eee;padding:8px;display:flex;gap:8px;flex-wrap:wrap;">
              <a href="{{ route('mt.discount_types.edit', $t) }}" style="padding:6px 10px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">Edit</a>
              <form method="POST" action="{{ route('mt.discount_types.destroy', $t) }}">
                @csrf @method('DELETE')
                <button type="submit" style="padding:6px 10px;border-radius:8px;border:0;background:#dc2626;color:#fff;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
        @if($types->count()===0)
          <tr><td colspan="3" style="padding:8px;">No discount types yet.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
@endsection
