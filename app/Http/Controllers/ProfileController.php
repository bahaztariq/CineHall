<?php

namespace App\Http\Controllers;

use Doctrine\Inflector\Rules\Ruleset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;


class ProfileController extends Controller
{
    public function me()
    {
        return response()->json(auth()->user());
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($user, $validated, $request) {
            if ($request->filled('name')) {
                $user->name = $validated['name'];
            }
            if ($request->filled('email')) {
                $user->email = $validated['email'];
            }
            if ($request->filled('password')) {
                $user->password = bcrypt($validated['password']);
            }

            $user->save();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('avatars', 'public');
                $user->image()->updateOrCreate(
                    [
                        'imageable_id' => $user->id,
                        'imageable_type' => get_class($user)
                    ],
                    ['path' => $path]
                );
            }
            return $user;
        });



        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user->load('image')
        ]);
    }

    public function delete()
    {
        $user = auth()->user();
        $user->delete();
        auth()->logout();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}
