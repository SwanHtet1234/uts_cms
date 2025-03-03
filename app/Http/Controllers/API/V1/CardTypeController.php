<?php

namespace App\Http\Controllers\API\V1;

use App\Models\CardType;
use App\Http\Requests\StoreCardTypeRequest;
use App\Http\Requests\UpdateCardTypeRequest;
use App\Http\Controllers\Controller;

class CardTypeController extends Controller
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
    public function store(StoreCardTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CardType $cardType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CardType $cardType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardTypeRequest $request, CardType $cardType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CardType $cardType)
    {
        //
    }
}
