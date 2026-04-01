<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Seat;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Models\reservation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\session;
use OpenApi\Attributes as OA;

class RoomController extends Controller
{
    #[OA\Get(
        path: '/rooms',
        summary: 'List all rooms',
        tags: ['Rooms'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function index()
    {
        return response()->json(Room::with('seats')->paginate(10));
    }

    #[OA\Get(
        path: '/rooms/{room}',
        summary: 'Show a specific room',
        tags: ['Rooms'],
        parameters: [
            new OA\Parameter(name: 'room', in: 'path', required: true, description: 'Room ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Room')),
            new OA\Response(response: 404, description: 'Room not found')
        ]
    )]
    public function show(Room $room)
    {
        return response()->json($room->load('seats'));
    }

    #[OA\Post(
        path: '/rooms',
        summary: 'Create a new room',
        tags: ['Rooms'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'capacity'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Hall A'),
                    new OA\Property(property: 'capacity', type: 'integer', example: 50)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Room created successfully', content: new OA\JsonContent(ref: '#/components/schemas/Room')),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function store(StoreRoomRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $room = Room::create($request->validated());


            $this->syncSeats($room, $request->capacity);

            return response()->json([
                'message' => 'Room and ' . $request->capacity . ' seats created successfully',
                'data'    => $room->load('seats')
            ], 201);
        });
    }

    #[OA\Put(
        path: '/rooms/{room}',
        summary: 'Update an existing room',
        tags: ['Rooms'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'room', in: 'path', required: true, description: 'Room ID', schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Hall B'),
                    new OA\Property(property: 'capacity', type: 'integer', example: 60)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Room updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/Room')),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function update(UpdateRoomRequest $request, Room $room)
    {
        return DB::transaction(function () use ($request, $room) {
            $oldCapacity = $room->capacity;
            $room->update($request->validated());
            $newCapacity = $room->capacity;


            if ($oldCapacity !== $newCapacity) {
                $this->syncSeats($room, $newCapacity, $oldCapacity);
            }

            return response()->json([
                'message' => 'Room updated and seats adjusted successfully',
                'data'    => $room->load('seats')
            ]);
        });
    }

    #[OA\Delete(
        path: '/rooms/{room}',
        summary: 'Delete a room',
        tags: ['Rooms'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'room', in: 'path', required: true, description: 'Room ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Room deleted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function destroy(Room $room)
    {
        Gate::authorize('admin');

        $room->delete();

        return response()->json(['message' => 'Room and all its seats deleted.']);
    }


    private function syncSeats(Room $room, int $newCapacity, int $oldCapacity = 0)
    {
        if ($newCapacity > $oldCapacity) {

            $toAdd = $newCapacity - $oldCapacity;
            for ($i = 1; $i <= $toAdd; $i++) {
                $room->seats()->create([
                    'seat_number' => 'S-' . ($oldCapacity + $i)
                ]);
            }
        } elseif ($newCapacity < $oldCapacity) {

            $toDelete = $oldCapacity - $newCapacity;
            $room->seats()
                ->orderBy('id', 'desc')
                ->limit($toDelete)
                ->delete();
        }
    }

    public function getSeats($sessionId)
    {
        $session = Session::findOrFail($sessionId);

        $seats = Seat::where('room_id', $session->room_id)->get();

        $reservedSeatIds = reservation::where('session_id', $sessionId)
            ->pluck('seat_id')
            ->toArray();

        $seatMap = $seats->map(function ($seat) use ($reservedSeatIds) {
            return [
                'id' => $seat->id,
                'label' => $seat->seat_number,
                'is_booked' => in_array($seat->id, $reservedSeatIds),
            ];
        });
        
        return response()->json($seatMap);
    }
}
