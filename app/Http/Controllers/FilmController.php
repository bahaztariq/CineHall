<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;
use App\Http\Requests\Film\StoreFilmRequest;
use App\Http\Requests\Film\UpdateFilmRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;

class FilmController extends Controller
{
    #[OA\Get(
        path: '/films',
        summary: 'List all films',
        tags: ['Films'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function index()
    {
        $films = Film::with(['genres', 'image', 'sessions'])->paginate(10);

        return response()->json($films);

    }

    #[OA\Get(
        path: '/films/{film}',
        summary: 'Show a specific film',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'film', in: 'path', required: true, description: 'Film ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Film')),
            new OA\Response(response: 404, description: 'Film not found')
        ]
    )]
    public function show(Film $film)
    {
        return response()->json($film->load(['genres', 'image']));
    }

    #[OA\Post(
        path: '/films',
        summary: 'Create a new film',
        tags: ['Films'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'duration', type: 'integer'),
                        new OA\Property(property: 'rate', type: 'string'),
                        new OA\Property(property: 'trailer', type: 'string'),
                        new OA\Property(property: 'genres', type: 'array', items: new OA\Items(type: 'integer')),
                        new OA\Property(property: 'image', type: 'string', format: 'binary')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Film created successfully', content: new OA\JsonContent(ref: '#/components/schemas/Film'))
        ]
    )]
    public function store(StoreFilmRequest $request)
    {
        $data = $request->validated();

        $film = Film::create($data);

        if (isset($data['genres'])) {
            $film->genres()->sync($data['genres']);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('films', 'public');
            $film->image()->create(['path' => $path]);
        }

        return response()->json($film->load(['genres', 'image']), 201);
    }



    #[OA\Put(
        path: '/films/{film}',
        summary: 'Update an existing film',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'film', in: 'path', required: true, description: 'Film ID', schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'duration', type: 'integer'),
                    new OA\Property(property: 'rate', type: 'string'),
                    new OA\Property(property: 'trailer', type: 'string'),
                    new OA\Property(property: 'genres', type: 'array', items: new OA\Items(type: 'integer'))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Film updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/Film'))
        ]
    )]
    public function update(UpdateFilmRequest $request, Film $film)
    {
        $data = $request->validated();

        $film->update($data);

        if (isset($data['genres'])) {
            $film->genres()->sync($data['genres']);
        }

        if ($request->hasFile('image')) {

            if ($film->image) {
                Storage::disk('public')->delete($film->image->path);
            }

            $path = $request->file('image')->store('films', 'public');

            $film->image()->updateOrCreate(
                ['imageable_id' => $film->id, 'imageable_type' => Film::class],
                ['path' => $path]
            );
        }

        return response()->json($film->load(['genres', 'image']));
    }

    #[OA\Delete(
        path: '/films/{film}',
        summary: 'Delete a film',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'film', in: 'path', required: true, description: 'Film ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Film deleted successfully'),
            new OA\Response(response: 403, description: 'Unauthorized')
        ]
    )]
    public function destroy(Film $film)
    {

        Gate::authorize('admin');

        if ($film->image) {
            Storage::disk('public')->delete($film->image->path);
            $film->image()->delete();
        }

        $film->delete();

        return response()->json([
            'message' => 'Film and associated assets deleted successfully'
        ]);
    }

    #[OA\Get(
        path: '/films/search',
        summary: 'Search films by title',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'film', in: 'query', required: true, description: 'Film title or part of it', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Films found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'film is found'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Film'))
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Film not found')
        ]
    )]
    public function search(Request $request)
    {
        $searched = $request->film;
        $films = Film::where('title', 'like', '%' . $searched . '%')->get();

        if ($films->isEmpty()) {
            return response()->json([
                'message' => 'film not found'
            ], 404);
        }

        return response()->json([
            'message' => 'film is found',
            'data' => $films
        ], 200);
    }

    #[OA\Get(
        path: '/films/filter',
        summary: 'Filter films by genre',
        tags: ['Films'],
        parameters: [
            new OA\Parameter(name: 'genre_id', in: 'query', required: true, description: 'Genre ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Filtered films found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Filtered films found'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Film'))
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'No films match this genre')
        ]
    )]
    public function filter(Request $request)
    {
        $query = Film::with(['genres', 'image']);

        if ($request->has('genre_id')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre_id);
            });
        }

        $films = $query->get();

        if ($films->isEmpty()) {
            return response()->json([
                'message' => 'No films match this genre'
            ], 404);
        }

        return response()->json([
            'message' => 'Filtered films found',
            'data' => $films
        ], 200);
    }
}
