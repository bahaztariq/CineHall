<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Seat;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\session;

class RoomController extends Controller
{
    public function index()
    {
        return response()->json(Room::with('seats')->paginate(10));
    }

    public function show(Room $room)
    {
        return response()->json($room->load('seats'));
    }

    /**
     * Create a room and its seats.
     */
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

    /**
     * Update a room and adjust seats based on capacity change.
     */
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
}