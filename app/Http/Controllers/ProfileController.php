<?php

namespace App\Http\Controllers;

use Doctrine\Inflector\Rules\Ruleset;
use Illuminate\Http\Request;
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
            'name'  => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user
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
