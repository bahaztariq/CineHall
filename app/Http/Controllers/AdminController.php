<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\reservation;
use App\Models\session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{

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