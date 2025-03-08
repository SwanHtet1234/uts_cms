<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        // Get the authenticated user
        $user = User::findOrFail($request->user_id);

        // Get user's cards with their latest 10 transactions
        $cards = $user->cards()->with(['cardTypeScheme.cardType', 'cardTypeScheme.cardScheme', 'transactions' => function ($query) {
            $query->latest()->take(10);
        }])->get();

        // Format the cards data
        $formattedCards = $cards->map(function ($card) {
            return [
                'card_id' => $card->id,
                'cardNumber' => $this->maskCardNumber($card->cardNumber),
                'status' => $card->status,
                'balance' => $card->balance,
                'cardType' => $card->cardTypeScheme->cardType->type,
                'cardScheme' => $card->cardTypeScheme->cardScheme->scheme_name,
                'image' => asset('storage/' . $card->cardTypeScheme->image),
                'transactions' => $card->transactions->map(function ($transaction) {
                    return [
                        'amount' => $transaction->amount,
                        'datetime' => $transaction->datetime,
                        'type' => $transaction->transactionType->typeName,
                    ];
                }),
            ];
        });

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Home page data retrieved successfully.',
            'data' => [
                'name' => $user->name,
                'cards' => $formattedCards,
            ],
            'metadata' => null,
        ];

        return response()->json($response, 200);
    }

    /**
     * Mask the card number (show first 6 and last 4 digits, replace others with x).
     */
    private function maskCardNumber($cardNumber)
    {
        return substr($cardNumber, 0, 6) . str_repeat('x', strlen($cardNumber) - 10) . substr($cardNumber, -4);
    }
}
