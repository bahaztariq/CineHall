<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;
use App\Http\Requests\Film\StoreFilmRequest;
use App\Http\Requests\Film\UpdateFilmRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class FilmController extends Controller
{
    /**
     * List all films with associated genres and images.
     */
    public function index()
    {
        $films = Film::with(['genres', 'image'])->paginate(10);

        return response()->json($films);

    }

    /**
     * Show a specific film.
     */
    public function show(Film $film)
    {
        return response()->json($film->load(['genres', 'image']));
    }

    /**
     * Create a film.
     */
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



    /**
     * Update a film.
     */
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

    /**
     * Delete a film.
     */
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
