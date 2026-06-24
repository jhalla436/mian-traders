<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        return view('mt.users.create', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:180','unique:users,email'],
            'role' => ['required','in:admin,manager,cashier'],
            'password' => ['required','string','min:4'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        User::create($data);

        return redirect()->route('mt.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('mt.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:180','unique:users,email,'.$user->id],
            'role' => ['required','in:admin,manager,cashier'],
            'password' => ['nullable','string','min:4'],
        ]);

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
