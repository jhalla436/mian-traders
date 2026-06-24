@php $user = $user ?? new \App\Models\User(); @endphp

<div class="form-stack" style="max-width:520px;">
  <div class="form-group">
    <label class="form-label">Name</label>
    <input name="name" value="{{ old('name', $user->name) }}" placeholder="Name" class="mt-input">
  </div>

  <div class="form-group">
    <label class="form-label">Email</label>
    <input name="email" value="{{ old('email', $user->email) }}" placeholder="Email" class="mt-input">
  </div>

  <div class="form-group">
    <label class="form-label">Role</label>
    @php $role = old('role', $user->role ?? 'cashier'); @endphp
    <select name="role" class="mt-select">
      <option value="admin" @selected($role==='admin')>Admin</option>
      <option value="manager" @selected($role==='manager')>Manager</option>
      <option value="cashier" @selected($role==='cashier')>Cashier</option>
    </select>
  </div>

  @php
    $assigned = $assigned ?? [];
    $oldShops = old('shops');
    if (is_array($oldShops)) { $assigned = array_map('intval', $oldShops); }
  @endphp

  @if(isset($shops))
    <div class="card" style="background:#f8fafc;padding:16px;">
      <div class="font-bold" style="margin-bottom:6px;">Shop Access</div>
      <div class="text-xs text-muted" style="margin-bottom:10px;">Select which shops this user can access. (Admin can see all shops even if empty)</div>
      <div class="form-stack" style="gap:8px;">
        @foreach($shops as $s)
          <label class="form-checkbox">
            <input type="checkbox" name="shops[]" value="{{ $s->id }}" {{ in_array((int)$s->id, $assigned, true) ? 'checked' : '' }}>
            {{ $s->name ?? ('Shop #' . $s->id) }}
          </label>
        @endforeach
      </div>
    </div>
  @endif

  <div class="form-group">
    <label class="form-label">Password</label>
    <input name="password" type="password" placeholder="Password {{ $user->exists ? '(leave blank to keep)' : '' }}" class="mt-input">
  </div>

  <label class="form-checkbox">
    <input type="checkbox" name="is_active" @checked(old('is_active', (int)($user->is_active ?? 1)) == 1)>
    Active
  </label>

  @if($errors->any())
    <div class="errors-box">
      <b>Fix errors:</b>
      <ul>
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</div>
