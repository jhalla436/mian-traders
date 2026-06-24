@extends('mt.layouts.app')

@section('content')
  <div style="background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
    <h2 style="margin:0;">Headings</h2>
    <a href="{{ route('mt.headings.create') }}"
       style="padding:8px 12px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">
      Add Heading
    </a>
  </div>

  <div style="background:#fff;padding:14px;border-radius:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Heading</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Category</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Variants</th>
          <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
        </tr>
      </thead>

      <tbody>
        @foreach($headings as $h)
          <tr>
            <td style="border-bottom:1px solid #eee;padding:8px;">{{ $h->name }}</td>
            <td style="border-bottom:1px solid #eee;padding:8px;">{{ $h->category?->name }}</td>
            <td style="border-bottom:1px solid #eee;padding:8px;">{{ $h->variants()->count() }}</td>

            <td style="border-bottom:1px solid #eee;padding:8px;display:flex;gap:8px;flex-wrap:wrap;">
              <a href="{{ route('mt.variants.index', $h) }}"
                 style="padding:8px 12px;border-radius:8px;background:#374151;color:#fff;text-decoration:none;">
                Variants
              </a>

              <form method="POST" action="{{ route('mt.headings.destroy', $h) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        style="padding:8px 12px;border-radius:8px;border:0;background:#dc2626;color:#fff;cursor:pointer;">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        @endforeach

        @if($headings->count() === 0)
          <tr><td colspan="4" style="padding:8px;">No headings yet.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
@endsection
