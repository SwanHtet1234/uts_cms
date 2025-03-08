<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Card;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
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

    public function gettype()
    {

        // Fetch all transaction types
        $transactionTypes = TransactionType::all();

        // Prepare the data for export
        $data = $transactionTypes->map(function ($transactionType) {
            return [
                'id' => $transactionType->id,
                'typeName' => $transactionType->typeName,
            ];
        });

        
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Transaction types exported successfully.',
                'data' => $data,
                'metadata' => null,
            ], 200);
                
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'card_id' => 'required|exists:cards,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|exists:transaction_types,id',
            'datetime' => 'sometimes|date',
        ]);

        // Fetch the card
        $card = Card::find($request->card_id);

        // Ensure the card belongs to the authenticated user
        if ($card->user_id !== $request->user_id) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Card not found.',
                'data' => null,
                'metadata' => null,
            ], 404);
        }

        // Create the transaction
        $transaction = Transaction::create([
            'card_id' => $card->id,
            'amount' => $request->amount,
            'datetime' => $request->datetime ?? now(), // Use current time if not provided
            'type' => $request->type,
        ]);

        // Fetch the transaction type
        $transactionType = TransactionType::find($request->type);

        // Prepare the response data
        $responseData = [
            'id' => $transaction->id,
            'card_id' => $transaction->card_id,
            'amount' => $transaction->amount,
            'datetime' => $transaction->datetime,
            'type' => $transactionType->typeName,
        ];

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 201,
            'message' => 'Transaction added successfully.',
            'data' => $responseData,
            'metadata' => null,
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
