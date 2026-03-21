<?php

namespace App\Http\Controllers;

use App\Models\session;
use App\Http\Requests\Session\StoreSessionRequest;
use App\Http\Requests\Session\UpdateSessionRequest;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;

class SessionController extends Controller
{
    #[OA\Get(
        path: '/sessions',
        summary: 'List all film sessions',
        tags: ['Sessions'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function index()
    {

        $sessions = session::with(['film', 'room'])
            ->orderBy('start_time', 'asc')
            ->paginate(15);

        return response()->json($sessions);
    }

    #[OA\Get(
        path: '/sessions/{film_session}',
        summary: 'Show a specific film session',
        tags: ['Sessions'],
        parameters: [
            new OA\Parameter(name: 'film_session', in: 'path', required: true, description: 'Session ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Session')),
            new OA\Response(response: 404, description: 'Session not found')
        ]
    )]
    public function show(session $filmSession)
    {
        return response()->json($filmSession->load(['film', 'room']));
    }

    #[OA\Post(
        path: '/sessions',
        summary: 'Create a new film session',
        tags: ['Sessions'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['film_id', 'room_id', 'language', 'start_time', 'end_time', 'price'],
                properties: [
                    new OA\Property(property: 'film_id', type: 'integer'),
                    new OA\Property(property: 'room_id', type: 'integer'),
                    new OA\Property(property: 'language', type: 'string'),
                    new OA\Property(property: 'start_time', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'end_time', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'price', type: 'number', format: 'float')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Session created successfully', content: new OA\JsonContent(ref: '#/components/schemas/Session')),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function store(StoreSessionRequest $request)
    {
        $session = session::create($request->validated());

        return response()->json([
            'message' => 'Film session created successfully.',
            'data'    => $session->load(['film', 'room'])
        ], 201);
    }

    #[OA\Put(
        path: '/sessions/{film_session}',
        summary: 'Update an existing film session',
        tags: ['Sessions'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'film_session', in: 'path', required: true, description: 'Session ID', schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'film_id', type: 'integer'),
                    new OA\Property(property: 'room_id', type: 'integer'),
                    new OA\Property(property: 'language', type: 'string'),
                    new OA\Property(property: 'start_time', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'end_time', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'price', type: 'number', format: 'float')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Session updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/Session')),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function update(UpdateSessionRequest $request, session $filmSession)
    {
        $filmSession->update($request->validated());

        return response()->json([
            'message' => 'Film session updated successfully.',
            'data'    => $filmSession->load(['film', 'room'])
        ]);
    }

    #[OA\Delete(
        path: '/sessions/{film_session}',
        summary: 'Delete a film session',
        tags: ['Sessions'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'film_session', in: 'path', required: true, description: 'Session ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Session deleted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function destroy(session $filmSession)
    {
        Gate::authorize('admin');

        $filmSession->delete();

        return response()->json([
            'message' => 'Film session deleted successfully.'
        ]);
    }
}
