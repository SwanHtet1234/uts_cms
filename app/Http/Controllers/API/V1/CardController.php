<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Card;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CardController extends Controller
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
    public function store(StoreCardRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Card $card)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardRequest $request, Card $card)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        //
    }

    /**
     * Fetch the authenticated user's cards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserCards(Request $request)
    {
        $user = $request->user();

        // Fetch the user's cards with type and scheme information
        $cards = $user->cards()->with('typeScheme.type', 'typeScheme.scheme')->get();

        if ($cards->isEmpty()) {
            return response()->json([
                'message' => 'No cards found for this user.',
                'cards' => [],
            ]);
        }

        // Format the card data (exclude sensitive information)
        $formattedCards = $cards->map(function ($card) {
            return [
                'id' => $card->id,
                'card_number' => '**** **** **** ' . substr($card->card_number, -4), // Mask card number
                'expire_date' => $card->expire_date,
                'balance' => $card->balance,
                'type' => $card->typeScheme->type->type,
                'scheme' => $card->typeScheme->scheme->scheme_name,
                'status' => $card->status ? 'Active' : 'Inactive',
            ];
        });

        return response()->json([
            'message' => 'Cards fetched successfully.',
            'cards' => $formattedCards,
        ]);
    }
}
