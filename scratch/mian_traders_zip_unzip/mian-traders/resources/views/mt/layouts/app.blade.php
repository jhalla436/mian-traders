<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ config('app.name', 'Mian Traders') }}</title>
</head>

<body style="font-family:Arial;margin:0;background:#f6f6f6;">
@php
  $role = auth()->user()?->role ?? 'cashier';
  $canSeeCost = \App\Support\Authz::canSeeCost();
  $isActive = function(string $routeName){
    try {
      return request()->routeIs($routeName) ? 'background:#1f2937;' : 'background:transparent;';
    } catch (\Throwable $e) {
      return '';
    }
  };
@endphp

<div style="display:flex;min-height:100vh;">

  {{-- SIDEBAR --}}
  <div style="width:240px;background:#111827;color:#fff;padding:16px;position:sticky;top:0;height:100vh;overflow:auto;">
    <h3 style="margin-top:0;">{{ config('app.name', 'Mian Traders') }}</h3>

    <div style="color:#9ca3af;font-size:12px;margin-bottom:10px;">
      Role: <b style="color:#fff;">{{ $role }}</b>
    </div>

    <a href="{{ route('dashboard') }}"
       style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('dashboard') }}">
      Dashboard
    </a>

    <a href="{{ route('mt.pos.index') }}"
       style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.pos.index') }}">
      POS
    </a>

    <a href="{{ route('mt.products.index') }}"
       style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.products.index') }}">
      Products
    </a>

    @if($canSeeCost)
      <a href="{{ route('mt.products.import_form') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.products.import_form') }}">
        Import Price List
      </a>

      <a href="{{ route('mt.purchases.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.purchases.index') }}">
        Purchases (Stock In)
      </a>

      <a href="{{ route('mt.company_orders.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.company_orders.index') }}">
        Company Orders
      </a>

      <a href="{{ route('mt.categories.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.categories.index') }}">
        Categories
      </a>

      <a href="{{ route('mt.companies.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.companies.index') }}">
        Companies
      </a>

      @if(\Illuminate\Support\Facades\Route::has('mt.company_groups.index'))
        <a href="{{ route('mt.company_groups.index') }}"
           style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.company_groups.index') }}">
          Company Groups
        </a>
      @endif

      @if(\Illuminate\Support\Facades\Route::has('mt.stock_movements.index'))
        <a href="{{ route('mt.stock_movements.index') }}"
           style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.stock_movements.index') }}">
          Stock Movements
        </a>
      @endif

      <a href="{{ route('mt.discount_types.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.discount_types.index') }}">
        Discount Types
      </a>

      <a href="{{ route('mt.discount_rules.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.discount_rules.index') }}">
        Discount Rules
      </a>

      <a href="{{ route('mt.expenses.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.expenses.index') }}">
        Expenses
      </a>

      <a href="{{ route('mt.recurring_expenses.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.recurring_expenses.index') }}">
        Recurring Expenses
      </a>
    @endif

    @if(\Illuminate\Support\Facades\Route::has('mt.sales.index'))
      <a href="{{ route('mt.sales.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.sales.index') }}">
        Sales
      </a>
    @endif

    @if(\Illuminate\Support\Facades\Route::has('mt.udhar.index'))
      <a href="{{ route('mt.udhar.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.udhar.index') }}">
        Udhar
      </a>
    @endif

    @if(\Illuminate\Support\Facades\Route::has('mt.whatsapp.index'))
      <a href="{{ route('mt.whatsapp.index') }}"
         style="display:block;color:#fff;text-decoration:none;padding:10px;border-radius:8px;margin-bottom:6px;{{ $isActive('mt.whatsapp.index') }}">
        WhatsApp
      </a>
    @endif

    @if(\Illuminate\Support\Facades\Route::has('logout'))
      <form method="POST" action="{{ route('logout') }}" style="margin-top:14px;">
        @csrf
        <button type="submit"
                style="width:100%;padding:10px;border:0;border-radius:8px;background:#374151;color:#fff;cursor:pointer;">
          Logout
        </button>
      </form>
    @else
      <div style="margin-top:14px;color:#9ca3af;font-size:12px;">
        Logout route not enabled.
      </div>
    @endif

  </div>

  {{-- MAIN CONTENT --}}
  <div style="flex:1;padding:18px;">

    @if(session('success'))
      <div style="background:#dcfce7;border:1px solid #86efac;padding:10px;border-radius:10px;margin-bottom:12px;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;margin-bottom:12px;">
        {{ session('error') }}
      </div>
    @endif

    @yield('content')
  </div>

</div>
</body>
</html>
