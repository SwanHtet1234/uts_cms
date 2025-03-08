<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    /** @use HasFactory<\Database\Factories\CardFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cardNumber',
        'expireDate',
        'cvv',
        'balance',
        'type_scheme_id',
        'status',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cardTypeScheme()
    {
        return $this->belongsTo(CardTypeScheme::class, 'type_scheme_id');
    }

    public function cardServices()
    {
        return $this->hasMany(CardService::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
