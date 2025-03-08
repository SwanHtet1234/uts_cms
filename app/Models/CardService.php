<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardService extends Model
{
    /** @use HasFactory<\Database\Factories\CardServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'card_id',
        'service_id',
        'status',
        'spendingLimit',
        'globalOrLocal',
    ];

    // Define relationships
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
