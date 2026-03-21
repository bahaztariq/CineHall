<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\reservation;
use App\Models\session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class AdminController extends Controller
{
    #[OA\Get(
        path: '/statistics',
        summary: 'Get system-wide statistics (Admin only)',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 403, description: 'Forbidden - User is not an admin')
        ]
    )]
    public function statistics()
    {
        Gate::authorize('admin');

        $now = Carbon::now();
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $startOfMonth = Carbon::now()->startOfMonth();

        return response()->json([
        
            'all_users'        => User::all(),
            'all_reservations' => reservation::with(['user', 'session.film'])->get(),

            'counts' => [
                'total_users'        => User::count(),
                'total_reservations' => reservation::count(),
                'pending_reservations'  => reservation::where('status', 'pending')->count(),
                'canceled_reservations' => reservation::where('status', 'canceled')->count(),
                'expired_reservations'  => reservation::where('status', 'expired')->count(),
            ],

            'rankings' => [
                'all_time' => session::with('film')
                    ->withCount('reservations')
                    ->orderBy('reservations_count', 'desc')
                    ->take(5)->get(),

                'last_six_months' => session::with('film')
                    ->withCount(['reservations' => function ($query) use ($sixMonthsAgo) {
                        $query->where('reserved_at', '>=', $sixMonthsAgo);
                    }])
                    ->orderBy('reservations_count', 'desc')
                    ->take(5)->get(),

                'this_month' => session::with('film')
                    ->withCount(['reservations' => function ($query) use ($startOfMonth) {
                        $query->where('reserved_at', '>=', $startOfMonth);
                    }])
                    ->orderBy('reservations_count', 'desc')
                    ->take(5)->get(),
            ]
        ]);
    }



    #[OA\Patch(
        path: '/users/{user}/toggle-status',
        summary: 'Toggle user status between Active and Banned (Admin only)',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Status toggled successfully'),
            new OA\Response(response: 403, description: 'Forbidden/Unauthorized')
        ]
    )]
    public function toggleUserStatus(User $user)
    {
        Gate::authorize('admin');

        if (Auth::id() === $user->id) {
            return response()->json(['message' => 'You cannot modify your own status.'], 403);
        }

        if ($user->status === 'active') {
            $user->update([
                'status'    => 'Banned',
                'banned_at' => now()
            ]);
            $message = "User '{$user->name}' is now Banned.";
        } else {
            $user->update([
                'status'    => 'active',
                'banned_at' => null
            ]);
            $message = "User '{$user->name}' is now active.";
        }

        return response()->json([
            'message' => $message,
            'user'    => $user
        ]);
    }
}