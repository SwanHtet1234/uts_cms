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
        'card_number',
        'expire_date',
        'cvv',
        'balance',
        'type_scheme_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function typeScheme()
    {
        return $this->belongsTo(CardTypeScheme::class, 'type_scheme_id');
    }
}
