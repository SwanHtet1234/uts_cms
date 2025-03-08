<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Card;
use App\Models\CardTypeScheme;
use App\Models\CardService;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'cardNumber' => 'required|string|size:16|regex:/^\d+$/|unique:cards,cardNumber',
            'transaction_pin' => 'required|string|size:6',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Invalid input.',
                'data' => $validator->errors(),
                'metadata' => null,
            ], 400);
        }

        // Check if the card number already exists
        if (Card::where('cardNumber', $request->cardNumber)->exists()) {
            return response()->json([
                'status' => 'error',
                'code' => 409,
                'message' => 'Card number already exists.',
                'data' => null,
                'metadata' => null,
            ], 409);
        }

        // Generate random card details
        $cardTypeScheme = CardTypeScheme::inRandomOrder()->first();
        $expireDate = now()->addYears(rand(1, 5))->format('Y-m-d');
        $cvv = rand(100, 999);
        $balance = rand(1000, 100000) / 100;
        $status = 'active';

        // Create the card
        $card = Card::create([
            'user_id' => $request->user_id,
            'cardNumber' => $request->cardNumber,
            'expireDate' => $expireDate,
            'cvv' => $cvv,
            'balance' => $balance,
            'type_scheme_id' => $cardTypeScheme->id,
            'status' => $status,
        ]);

        // Generate 20-30 random transactions for the card
        $transactionTypes = TransactionType::pluck('id')->toArray();
        $numberOfTransactions = rand(20, 30);

        for ($i = 0; $i < $numberOfTransactions; $i++) {
            Transaction::create([
                'card_id' => $card->id,
                'amount' => rand(100, 10000) / 100,
                'datetime' => now()->subDays(rand(1, 365))->format('Y-m-d H:i:s'),
                'type' => $transactionTypes[array_rand($transactionTypes)],
            ]);
        }

        // Format the card data for the response
        $formattedCard = [
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

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 201,
            'message' => 'Card added successfully.',
            'data' => $formattedCard,
            'metadata' => null,
        ];

        return response()->json($response, 201);
    }

    /**
     * Mask the card number (show first 6 and last 4 digits, replace others with x).
     */
    private function maskCardNumber($cardNumber)
    {
        return substr($cardNumber, 0, 6) . str_repeat('x', strlen($cardNumber) - 10) . substr($cardNumber, -4);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_pin' => 'required|string|size:6',
            'user_id' => 'required|exists:users,id',
            'card_id' => 'required|exists:cards,id',
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

        // Format the card data for the response
        $formattedCard = [
            'cardNumber' => $card->cardNumber, // Full card number (not masked)
            'status' => $card->status,
            'balance' => $card->balance,
            'expireDate' => $card->expireDate,
            'cvv' => $card->cvv,
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

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Card details retrieved successfully.',
            'data' => $formattedCard,
            'metadata' => null,
        ];

        return response()->json($response, 200);
    }

    public function changeStatus(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_pin' => 'required|string|size:6',
            'user_id' => 'required|exists:users,id',
            'card_id' => 'required|exists:cards,id',
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

        // Toggle the card status
        $card->status = ($card->status === 'active') ? 'locked' : 'active';
        $card->save();

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Card status updated successfully.',
            'data' => [
                'card_id' => $card->id,
                'new_status' => $card->status,
            ],
            'metadata' => null,
        ];

        return response()->json($response, 200);
    }

    public function updateCardServices(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_pin' => 'required|string|size:6',
            'card_id' => 'required|exists:cards,id',
            'services' => 'required|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.status' => 'required|boolean',
            'services.*.spendingLimit' => 'required|numeric|min:0',
            'services.*.globalOrLocal' => 'required|in:global,local',
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

        // Update card services
        DB::beginTransaction();
        try {
            foreach ($request->services as $serviceData) {
                CardService::where('card_id', $card->id)
                    ->where('service_id', $serviceData['service_id'])
                    ->where('globalOrLocal', $serviceData['globalOrLocal'])
                    ->update([
                        'status' => $serviceData['status'],
                        'spendingLimit' => $serviceData['spendingLimit'],
                    ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to update card services.',
                'data' => null,
                'metadata' => null,
            ], 500);
        }

        // Fetch the updated card services
        $updatedCardServices = $card->cardServices()->with('service')->get();

        // Format the updated card services data for the response
        $formattedCardServices = $updatedCardServices->map(function ($cardService) {
            return [
                'service_id' => $cardService->service_id,
                'service_type' => $cardService->service->service_type,
                'status' => $cardService->status,
                'spendingLimit' => $cardService->spendingLimit,
                'globalOrLocal' => $cardService->globalOrLocal,
            ];
        });

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Card services updated successfully.',
            'data' => [
                'card_id' => $card->id,
                'services' => $formattedCardServices,
            ],
            'metadata' => null,
        ];

        return response()->json($response, 200);
    }

    public function updateCardService(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_pin' => 'required|string|size:6',
            'card_id' => 'required|exists:cards,id',
            'service_id' => 'required|exists:services,id',
            'spendingLimit' => 'required|numeric|min:0',
            'globalOrLocal' => 'required|in:global,local',
        ]);

        // Fetch the card
        $card = Card::find($request->card_id);

        // Find the specific card service
        $cardService = CardService::where('card_id', $card->id)
            ->where('service_id', $request->service_id)
            ->where('globalOrLocal', $request->globalOrLocal)
            ->first();

        if (!$cardService) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Card service not found.',
                'data' => null,
                'metadata' => null,
            ], 404);
        }

        // Toggle the card service status
        $cardService->status = !$cardService->status; // Toggle the status
        $cardService->spendingLimit = $request->spendingLimit; // Update the spending limit
        $cardService->save();

        // Fetch the updated card service
        $updatedCardService = $cardService->load('service');

        // Format the updated card service data for the response
        $formattedCardService = [
            'service_id' => $updatedCardService->service_id,
            'service_type' => $updatedCardService->service->service_type,
            'status' => $updatedCardService->status,
            'spendingLimit' => $updatedCardService->spendingLimit,
            'globalOrLocal' => $updatedCardService->globalOrLocal,
        ];

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Card service updated successfully.',
            'data' => $formattedCardService,
            'metadata' => null,
        ];

        return response()->json($response, 200);
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

    public function getCardServices(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_pin' => 'required|string|size:6',
            'card_id' => 'required|exists:cards,id',
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

        // Fetch the card services with their status and spending limits
        $cardServices = $card->cardServices()->with('service')->get();

        // Format the card services data for the response
        $formattedCardServices = $cardServices->map(function ($cardService) {
            return [
                'service_id' => $cardService->service_id,
                'service_type' => $cardService->service->service_type,
                'status' => $cardService->status,
                'spendingLimit' => $cardService->spendingLimit,
                'globalOrLocal' => $cardService->globalOrLocal,
            ];
        });

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Card services retrieved successfully.',
            'data' => [
                'card_id' => $card->id,
                'services' => $formattedCardServices,
            ],
            'metadata' => null,
        ];

        return response()->json($response, 200);
    }

    /**
     * Fetch the authenticated user's cards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function getUserCards(Request $request)
    // {
    //     $user = $request->user();

    //     // Fetch the user's cards with type and scheme information
    //     $cards = $user->cards()->with('typeScheme.type', 'typeScheme.scheme')->get();

    //     if ($cards->isEmpty()) {
    //         return response()->json([
    //             'message' => 'No cards found for this user.',
    //             'cards' => [],
    //         ]);
    //     }

    //     // Format the card data (exclude sensitive information)
    //     $formattedCards = $cards->map(function ($card) {
    //         return [
    //             'id' => $card->id,
    //             'card_number' => '**** **** **** ' . substr($card->card_number, -4), // Mask card number
    //             'expire_date' => $card->expire_date,
    //             'balance' => $card->balance,
    //             'type' => $card->typeScheme->type->type,
    //             'scheme' => $card->typeScheme->scheme->scheme_name,
    //             'status' => $card->status ? 'Active' : 'Inactive',
    //         ];
    //     });

    //     return response()->json([
    //         'message' => 'Cards fetched successfully.',
    //         'cards' => $formattedCards,
    //     ]);
    // }
}
