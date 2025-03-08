<?php

namespace App\Http\Controllers\API\V1;

use App\Models\TransactionType;
use App\Http\Requests\StoreTransactionTypeRequest;
use App\Http\Requests\UpdateTransactionTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionType $transactionType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionType $transactionType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionTypeRequest $request, TransactionType $transactionType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionType $transactionType)
    {
        //
    }
}
