<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\reservation;
use App\Models\Seat;
use App\Models\ticket;
use App\Models\session;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        $validated = $request->validated();
        $session = session::findOrFail($validated['session_id']);
        $seatId = $validated['seat_id'];

        $seat = Seat::where('id', $seatId)
            ->where('room_id', $session->room_id)
            ->first();

        if (!$seat) {
            return response()->json(['message' => 'This seat does not exist in this room'], 422);
        }

        $isTaken = $seat->reservations()
            ->where('session_id', $session->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($isTaken) {
            return response()->json(['message' => 'Seat is already reserved'], 422);
        }

        $reservation = $request->user()->reservations()->create([
            'session_id'  => $session->id,
            'seat_id'     => $seatId,
            'reserved_at' => now()->addMinutes(15),
            'status'      => 'pending',
        ]);

        return response()->json([
            'message' => 'Reservation created. Please pay within 15 minutes to confirm your seat.',
            'reservation_id' => $reservation->id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, reservation $reservation)
    {
         if ($request->user()->id !== $reservation->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservation->update([
            'status' => $request->validated()
        ]);
        
        return response()->json(['message' => 'Reservation updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(reservation $reservation)
    {
         if ($request->user()->id !== $reservation->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservation->delete();

        return response()->json(['message' => 'Reservation Deleted successfully']);
    }
}
