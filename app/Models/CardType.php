<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardType extends Model
{
    /** @use HasFactory<\Database\Factories\CardTypeFactory> */
    use HasFactory;

    protected $fillable = ['type'];

    // Define relationships
    public function cardTypeSchemes()
    {
        return $this->hasMany(CardTypeScheme::class, 'type_id');
    }
}
