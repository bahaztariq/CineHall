<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoregenreRequest;
use App\Http\Requests\UpdategenreRequest;
use App\Models\genre;
use OpenApi\Attributes as OA;

class GenreController extends Controller
{
    #[OA\Get(
        path: '/genres',
        summary: 'List all genres',
        tags: ['Genres'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Genre')))
        ]
    )]
    public function index()
    {
        $genres = genre::withCount('films')->get();

        return response()->json($genres);
    }

    #[OA\Post(
        path: '/genres',
        summary: 'Create a new genre',
        tags: ['Genres'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Horror')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Genre created successfully', content: new OA\JsonContent(ref: '#/components/schemas/Genre')),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function store(StoregenreRequest $request)
    {
        $genre = genre::create($request->validated());

        return response()->json([
            'message' => 'Genre created successfully.',
            'data'    => $genre,
        ], 201);
    }

    #[OA\Get(
        path: '/genres/{genre}',
        summary: 'Show a specific genre',
        tags: ['Genres'],
        parameters: [
            new OA\Parameter(name: 'genre', in: 'path', required: true, description: 'Genre ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Genre')),
            new OA\Response(response: 404, description: 'Genre not found')
        ]
    )]
    public function show(genre $genre)
    {
        return response()->json($genre->load('films'));
    }

    #[OA\Put(
        path: '/genres/{genre}',
        summary: 'Update an existing genre',
        tags: ['Genres'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'genre', in: 'path', required: true, description: 'Genre ID', schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Thriller')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Genre updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/Genre')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Genre not found')
        ]
    )]
    public function update(UpdategenreRequest $request, genre $genre)
    {
        $genre->update($request->validated());

        return response()->json([
            'message' => 'Genre updated successfully.',
            'data'    => $genre,
        ]);
    }

    #[OA\Delete(
        path: '/genres/{genre}',
        summary: 'Delete a genre',
        tags: ['Genres'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'genre', in: 'path', required: true, description: 'Genre ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Genre deleted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Genre not found')
        ]
    )]
    public function destroy(genre $genre)
    {
        Gate::authorize('admin');

        $genre->delete();

        return response()->json([
            'message' => 'Genre deleted successfully.',
        ]);
    }
}
