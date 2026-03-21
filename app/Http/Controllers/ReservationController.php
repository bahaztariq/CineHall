<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\reservation;
use App\Models\Seat;
use App\Models\ticket;
use App\Models\session;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class ReservationController extends Controller
{
    #[OA\Get(
        path: '/reservations',
        summary: 'List all reservations for the authenticated user',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Reservation'))),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function index()
    {
        $reservations = reservation::with(['session.film', 'seat'])
            ->where('user_id', auth()->id())
            ->get();

        return response()->json($reservations);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    #[OA\Post(
        path: '/reservations',
        summary: 'Create a new reservation',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['session_id', 'seat_id'],
                properties: [
                    new OA\Property(property: 'session_id', type: 'integer', example: 1),
                    new OA\Property(property: 'seat_id', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Reservation created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'reservation_id', type: 'integer')
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error or seat taken'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
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

    #[OA\Get(
        path: '/reservations/{reservation}',
        summary: 'Get reservation details',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'reservation', in: 'path', required: true, description: 'Reservation ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Reservation')),
            new OA\Response(response: 403, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Reservation not found')
        ]
    )]
    public function show(reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($reservation->load(['session.film', 'seat', 'tickets']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(reservation $reservation)
    {
        //
    }

    #[OA\Put(
        path: '/reservations/{reservation}',
        summary: 'Update a reservation',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'reservation', in: 'path', required: true, description: 'Reservation ID', schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/Reservation')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Reservation updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/Reservation')),
            new OA\Response(response: 403, description: 'Unauthorized')
        ]
    )]
    public function update(UpdateReservationRequest $request, reservation $reservation)
    {
        if ($request->user()->id !== $reservation->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($request, $reservation) {
            $reservation->update($request->validated());
        });

        return response()->json([
            'message' => 'Reservation updated successfully',
            'reservation' => $reservation->load(['session.film', 'seat', 'tickets'])
        ]);
    }

    #[OA\Delete(
        path: '/reservations/{reservation}',
        summary: 'Cancel/Delete a reservation',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'reservation', in: 'path', required: true, description: 'Reservation ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Reservation deleted successfully'),
            new OA\Response(response: 403, description: 'Unauthorized')
        ]
    )]
    public function destroy(reservation $reservation)
    {
        if (auth()->id() !== $reservation->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservation->delete();

        return response()->json(['message' => 'Reservation Deleted successfully']);
    }
}
