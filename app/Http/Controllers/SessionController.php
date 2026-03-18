<?php

namespace App\Http\Controllers;

use App\Models\session;
use App\Http\Requests\Session\StoreSessionRequest;
use App\Http\Requests\Session\UpdateSessionRequest;
use Illuminate\Support\Facades\Gate;

class SessionController extends Controller
{
    /**
     * List all film sessions. (Public)
     */
    public function index()
    {
        
        $sessions = session::with(['film', 'room'])
            ->orderBy('start_time', 'asc')
            ->paginate(15);

        return response()->json($sessions);
    }

    /**
     * Show a specific film session. (Public)
     */
    public function show(session $filmSession)
    {
        return response()->json($filmSession->load(['film', 'room']));
    }

    /**
     * Create a new film session. (Admin Only)
     */
    public function store(StoreSessionRequest $request)
    {
        $session = session::create($request->validated());

        return response()->json([
            'message' => 'Film session created successfully.',
            'data'    => $session->load(['film', 'room'])
        ], 201);
    }

    /**
     * Update an existing film session. (Admin Only)
     */
    public function update(UpdateSessionRequest $request, session $filmSession)
    {
        $filmSession->update($request->validated());

        return response()->json([
            'message' => 'Film session updated successfully.',
            'data'    => $filmSession->load(['film', 'room'])
        ]);
    }

    /**
     * Delete a film session. (Admin Only)
     */
    public function destroy(session $filmSession)
    {
        Gate::authorize('admin');

        $filmSession->delete();

        return response()->json([
            'message' => 'Film session deleted successfully.'
        ]);
    }
}