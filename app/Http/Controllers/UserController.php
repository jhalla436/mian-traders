<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $role = trim((string)$request->get('role',''));

        $users = User::query()
            ->when($role !== '', function($query) use ($role){
                $query->where('role', $role);
            })
            ->when($q !== '', function($query) use ($q){
                $query->where('name','like',"%{$q}%")
                      ->orWhere('email','like',"%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('mt.users.index', compact('users','q','role'));
    }

    public function create()
    {
        $user = new User();
        $shops = Shop::where('is_active', 1)->orderBy('name')->get();
        return view('mt.users.create', compact('user','shops'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:180','unique:users,email'],
            'role' => ['required','in:admin,manager,cashier'],
            'password' => ['required','string','min:4'],
            'shops' => ['nullable','array'],
            'shops.*' => ['integer','exists:shops,id'],
        ]);

        $selected = collect($data['shops'] ?? [])->map(fn($v) => (int)$v)->unique()->values();
        unset($data['shops']);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $user = User::create($data);

        // Assign shops
        if (($data['role'] ?? '') === 'admin' && $selected->count() === 0) {
            $selected = Shop::where('is_active', 1)->pluck('id');
        }
        $user->shops()->sync($selected->all());

        return redirect()->route('mt.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $shops = Shop::where('is_active', 1)->orderBy('name')->get();
        $assigned = $user->shops()->pluck('shops.id')->all();
        return view('mt.users.edit', compact('user','shops','assigned'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:180','unique:users,email,'.$user->id],
            'role' => ['required','in:admin,manager,cashier'],
            'password' => ['nullable','string','min:4'],
            'shops' => ['nullable','array'],
            'shops.*' => ['integer','exists:shops,id'],
        ]);

        $selected = collect($data['shops'] ?? [])->map(fn($v) => (int)$v)->unique()->values();
        unset($data['shops']);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // prevent removing admin from self
        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'You cannot remove admin role from your own account.');
        }

        if (!empty($data['password'] ?? '')) {
            $data['password'] = Hash::make($data['password']); // Reset password here
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Update shop assignment
        if (($data['role'] ?? '') === 'admin' && $selected->count() === 0) {
            $selected = Shop::where('is_active', 1)->pluck('id');
        }
        $user->shops()->sync($selected->all());

        return redirect()->route('mt.users.index')->with('success', 'User updated (username/role/password if provided).');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('mt.users.index')->with('success', 'User deleted.');
    }
}
