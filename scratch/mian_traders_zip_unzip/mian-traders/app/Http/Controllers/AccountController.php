<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('mt.account.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:180','unique:users,email,' . $user->id],
            'current_password' => ['nullable','string'],
            'new_password' => ['nullable','string','min:4'],
        ]);

        // Update name/email
        $user->name = $data['name'];
        $user->email = $data['email'];

        // If user wants to change password
        if (!empty($data['new_password'])) {

            if (empty($data['current_password'])) {
                return back()->with('error', 'Enter current password to set a new password.');
            }

            if (!Hash::check($data['current_password'], $user->password)) {
                return back()->with('error', 'Current password is wrong.');
            }

            $user->password = Hash::make($data['new_password']);
        }

        $user->save();

        return back()->with('success', 'Account updated.');
    }
}
