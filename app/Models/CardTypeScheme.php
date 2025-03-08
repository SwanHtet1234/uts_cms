<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardTypeScheme extends Model
{
    /** @use HasFactory<\Database\Factories\CardTypeSchemeFactory> */
    use HasFactory;

    protected $fillable = [
        'type_id',
        'scheme_id',
        'image',
    ];

    // Define relationships
    public function cardType()
    {
        return $this->belongsTo(CardType::class, 'type_id');
    }

    public function cardScheme()
    {
        return $this->belongsTo(CardScheme::class, 'scheme_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'type_scheme_id');
    }
}
