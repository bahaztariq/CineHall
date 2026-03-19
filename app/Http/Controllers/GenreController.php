<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoregenreRequest;
use App\Http\Requests\UpdategenreRequest;
use App\Models\genre;
use Illuminate\Support\Facades\Gate;

class GenreController extends Controller
{
    /**
     * Display a listing of all genres.
     */
    public function index()
    {
        $genres = genre::withCount('films')->get();

        return response()->json($genres);
    }

    /**
     * Store a newly created genre in storage. (Admin only)
     */
    public function store(StoregenreRequest $request)
    {
        $genre = genre::create($request->validated());

        return response()->json([
            'message' => 'Genre created successfully.',
            'data'    => $genre,
        ], 201);
    }

    /**
     * Display the specified genre with its films.
     */
    public function show(genre $genre)
    {
        return response()->json($genre->load('films'));
    }

    /**
     * Update the specified genre in storage. (Admin only)
     */
    public function update(UpdategenreRequest $request, genre $genre)
    {
        $genre->update($request->validated());

        return response()->json([
            'message' => 'Genre updated successfully.',
            'data'    => $genre,
        ]);
    }

    /**
     * Remove the specified genre from storage. (Admin only)
     */
    public function destroy(genre $genre)
    {
        Gate::authorize('admin');

        $genre->delete();

        return response()->json([
            'message' => 'Genre deleted successfully.',
        ]);
    }
}
