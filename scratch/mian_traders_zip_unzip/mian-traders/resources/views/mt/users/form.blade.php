@php $user = $user ?? new \App\Models\User(); @endphp

<div style="display:grid;gap:10px;max-width:520px;">
  <input name="name" value="{{ old('name', $user->name) }}" placeholder="Name"
         style="padding:10px;border:1px solid #ddd;border-radius:10px;">

  <input name="email" value="{{ old('email', $user->email) }}" placeholder="Email"
         style="padding:10px;border:1px solid #ddd;border-radius:10px;">

  @php $role = old('role', $user->role ?? 'cashier'); @endphp
  <select name="role" style="padding:10px;border:1px solid #ddd;border-radius:10px;">
    <option value="admin" @selected($role==='admin')>Admin</option>
    <option value="manager" @selected($role==='manager')>Manager</option>
    <option value="cashier" @selected($role==='cashier')>Cashier</option>
  </select>

  <input name="password" type="password"
         placeholder="Password {{ $user->exists ? '(leave blank to keep)' : '' }}"
         style="padding:10px;border:1px solid #ddd;border-radius:10px;">

  <label style="display:flex;gap:8px;align-items:center;">
    <input type="checkbox" name="is_active" @checked(old('is_active', (int)($user->is_active ?? 1)) == 1)>
    Active
  </label>

  @if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:10px;">
      <div style="font-weight:800;margin-bottom:6px;">Fix errors:</div>
      <ul style="margin:0;padding-left:18px;">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</div>
