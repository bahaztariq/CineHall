<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorefavouriteRequest;
use App\Http\Requests\UpdatefavouriteRequest;
use App\Models\favourite;

use OpenApi\Attributes as OA;

class FavouriteController extends Controller
{
    #[OA\Tag(name: "Favourites", description: "User favourite films")]

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
    public function store(StorefavouriteRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(favourite $favourite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(favourite $favourite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatefavouriteRequest $request, favourite $favourite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(favourite $favourite)
    {
        //
    }
}
