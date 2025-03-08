<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'card_id',
        'amount',
        'datetime',
        'type',
    ];

    // Define relationships
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'type');
    }
}
