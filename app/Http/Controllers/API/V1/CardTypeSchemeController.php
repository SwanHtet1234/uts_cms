<?php

namespace App\Http\Controllers\API\V1;

use App\Models\CardTypeScheme;
use App\Http\Requests\StoreCardTypeSchemeRequest;
use App\Http\Requests\UpdateCardTypeSchemeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CardTypeSchemeController extends Controller
{
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
    public function store(StoreCardTypeSchemeRequest $request)
    {
        $request->validate([
            'type_id' => 'required|exists:card_types,id',
            'scheme_id' => 'required|exists:card_schemes,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image upload
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('card_type_scheme_images', 'public');
        } else {
            $imagePath = null;
        }

        // Create the card type scheme
        $cardTypeScheme = CardTypeScheme::create([
            'type_id' => $request->type_id,
            'scheme_id' => $request->scheme_id,
            'image' => $imagePath, // Save the image path
        ]);

        return response()->json($cardTypeScheme, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CardTypeScheme $cardTypeScheme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CardTypeScheme $cardTypeScheme)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardTypeSchemeRequest $request, CardTypeScheme $cardTypeScheme)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CardTypeScheme $cardTypeScheme)
    {
        //
    }
}
