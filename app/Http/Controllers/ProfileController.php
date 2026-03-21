<?php

namespace App\Http\Controllers;

use Doctrine\Inflector\Rules\Ruleset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/profile',
        summary: 'Get authenticated user profile',
        tags: ['Profile'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function me()
    {
        return response()->json(auth()->user());
    }

    #[OA\Post(
        path: '/profile',
        summary: 'Update authenticated user profile',
        tags: ['Profile'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'email', type: 'string', format: 'email'),
                        new OA\Property(property: 'password', type: 'string', format: 'password'),
                        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Profile updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
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

    #[OA\Delete(
        path: '/profile',
        summary: 'Delete authenticated user account',
        tags: ['Profile'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Account deleted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function delete()
    {
        $user = auth()->user();
        $user->delete();
        auth()->logout();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}
